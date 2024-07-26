<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DotPhaService;
use App\Events\CanDanMessage;
use Redis;
use App\Account;

class CanDanSll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tutien:can-dan-sll {accountId} {danDuoc=tcd} {isDt=0} {vatphamphutro=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Can dan cho nhieu ID toi VM';

    protected $service;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(DotPhaService $dp)
    {
        $this->service = $dp;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $accountId = $this->argument('accountId');
        $danDuoc = ['danDuoc' => explode(',', $this->argument('danDuoc'))];
        $isDt = $this->argument('isDt');
        $vatphamphutro = ['vatphamphutro' => explode(',', $this->argument('vatphamphutro'))];
        $account = Account::find($accountId);
        if (Redis::exists('dang_can_dan' . $accountId)) {
            debug($accountId . ' - Đang cắn đan - skip');
            CanDanMessage::dispatch($account, 'Acc đang cắn đan - skip');
            return;
        }

        Redis::set('dang_can_dan' . $accountId, 1, 'EX', 10 * 60); // 10 min
        $count = 0;
        while ((strpos($account->progress, 'Viên Mãn') === false && $count <= 20 && Redis::exists('dang_can_dan' . $accountId)) || strpos($account->progress, 'Luyện Khí Viên Mãn') !== false) {
            CanDanMessage::dispatch($account, '[Cắn Đan Lần ' . $count . ']' . $account->account_name . ' - ' . $account->progress);
            $res = $this->service->canDan($account, $danDuoc, $isDt, $vatphamphutro);
            if ($res == "Số lượng vật phẩm không đủ để chuyển" || $res == "Bảo khố không có vật phẩm này hoặc đã sử dụng hết" || $res == "Chưa đăng nhập bảo khố") {
                CanDanMessage::dispatch($account, $res);
                

                Redis::del('dang_can_dan' . $accountId);
            };
            $count++;
            sleep(5);
        }
        Redis::del('dang_can_dan' . $accountId);
    }
}

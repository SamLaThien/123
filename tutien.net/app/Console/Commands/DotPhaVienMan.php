<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Account;
use App\Services\AccountService;
use Redis;
use Ixudra\Curl\Facades\Curl;
use Illuminate\Support\Arr;

class DotPhaVienMan extends Command
{
    const ID_TTD = 9;
    const ID_TCD = 13;
    const ID_BND = 14;
    const ID_UTD = 22;
    const ID_BAD = 40;
    const ID_PTD = 36;

    const ID_HKD = 10;
    const ID_DGT = 11;
    const ID_TLC = 17;
    const ID_THANH = 34;

    const ID_LTTHP = 61;
    const ID_CTD = 60;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tutien:dp-vm';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $accounts = Account::where('progress', 'like', "%Viên Mãn - 100%")->get();
        $service = app(AccountService::class);
        foreach ($accounts as $account) {
            $progress = $account->progress;
            $buff['btnDotPha'] = 1;
            $buff['vatphamphutro'] = [];
            if (strpos($progress, "Luyện Khí Viên Mãn") !== false) {
                $service->congCongHien($account, 100);
                sleep(1);
                $service->chuyenDo($account, self::ID_TCD, 5);
                sleep(1);
                //$service->chuyenDo($account, self::ID_DGT, 1);
                //$buff['vatphamphutro'] = [];
                //$buff['vatphamphutro'] = [self::ID_DGT];
            } else if (strpos($progress, "Trúc Cơ Viên Mãn") !== false) {
                $service->chuyenDo($account, self::ID_UTD, 1);
                sleep(1);
                //$service->chuyenDo($account, self::ID_TLC, 1);
                //sleep(1);
                //$service->chuyenDo($account, self::ID_DGT, 1);
                //$buff['vatphamphutro'] = [self::ID_TLC, self::ID_DGT];
            } else if (strpos($progress, "Kim Đan Viên Mãn") !== false) {
                $service->chuyenDo($account, self::ID_PTD, 1);
                sleep(1);
                $service->chuyenDo($account, self::ID_TLC, 1);
                sleep(1);
                $service->chuyenDo($account, self::ID_DGT, 1);
                sleep(1);
                $service->chuyenDo($account, self::ID_THANH, 1);
                $buff['vatphamphutro'] = [self::ID_DGT, self::ID_TLC, self::ID_THANH];
            } else if (strpos($progress, "Nguyên Anh Viên Mãn") !== false) {
                $service->chuyenDo($account, self::ID_TLC, 1);
                sleep(1);
                $service->chuyenDo($account, self::ID_DGT, 1);
                sleep(1);
                $service->chuyenDo($account, self::ID_THANH, 1);
                sleep(1);
                $service->chuyenDo($account, self::ID_LTTHP, 1);
                sleep(1);
                $service->chuyenDo($account, self::ID_CTD, 1);
                $buff['vatphamphutro'] = [self::ID_DGT, self::ID_TLC, self::ID_THANH];
            }

            sleep(1);
            $service->taoNhanVat($account);
            sleep(2);
            $service->nopCongPhap($account);
            sleep(2);
            $this->dotPha($account, $buff);
            sleep(2);
            $this->rutCongPhap($account);
        }
    }

    public function rutCongPhap(Account $account)
    {
        $keys = Redis::keys($account->account_id . "_cp_*");
        foreach ($keys as $key) {
            $id = str_replace($account->account_id . "_cp_", "", $key);
            $amount = Redis::get($key) || 0;
            app(AccountService::class)->rutHanhTrang($account, $id, $amount);
        }
    }

    public function dotPha(Account $account, $buff = [])
    {
        \Log::channel('progress_log')->info('Auto dp for: ' . $account->account_name);
        $proxies = config('dp_proxies.list');
        $proxy = Arr::random($proxies);
        Curl::to('https://tutien.net/account/tu_luyen/dot_pha')
            ->withHeader('authority: tutien.net')
            ->withHeader('accept: */*')
            ->withHeader('accept-language: en-US,en;q=0.9,vi;q=0.8')
            ->withHeader('content-type: application/x-www-form-urlencoded; charset=UTF-8')
            ->withHeader('origin: https://tutien.net')
            ->withHeader('referer: https://tutien.net/account/tu_luyen/dot_pha')
            ->withHeader('sec-ch-ua: " Not A;Brand";v="99", "Chromium";v="102", "Google Chrome";v="102"')
            ->withHeader('sec-ch-ua-mobile: ?0')
            ->withHeader('sec-ch-ua-platform: "Linux"')
            ->withHeader('sec-fetch-dest: empty')
            ->withHeader('sec-fetch-mode: cors')
            ->withHeader('sec-fetch-site: same-origin')
            ->withHeader('user-agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/102.0.0.0 Safari/537.36')
            ->withHeader('x-requested-with: XMLHttpRequest')
            ->withProxy($proxy['host'], $proxy['port'], 'http://', $proxy['username'], $proxy['password'])
            ->withHeader($account->cookie)
            ->withData($buff)
            ->post();
         // $this->checkBac($account);
    }
}

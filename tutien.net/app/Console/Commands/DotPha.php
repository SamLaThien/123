<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Account;
use App\Services\AccountService;
use Redis;
use Ixudra\Curl\Facades\Curl;
use Illuminate\Support\Arr;

class DotPha extends Command
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
    protected $signature = 'tutien:dp';

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
        $accounts = Account::where('progress', 'like', "100%")->get();
        $service = app(AccountService::class);
	foreach ($accounts as $account) {
            $buff['btnDotPha'] = 1;
            sleep(2);
            $this->dotPha($account, $buff);
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

    public function dotPha(Account $account, $buff = []) {
        \Log::channel('progress_log')->info('Auto dp for: ' . $account->account_name);
        $proxies = config('proxies.list');
        $proxy = Arr::random($proxies);
        Curl::to('https://tutien.net/account/tu_luyen/dot_pha')
            ->withHeader('authority: truyencv.com')
            ->withHeader('accept: */*')
            ->withHeader('sec-fetch-dest: empty')
            ->withHeader('x-requested-with: XMLHttpRequest')
            ->withHeader('user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.3990.0 Safari/537.36')
            ->withHeader('content-type: application/x-www-form-urlencoded; charset=UTF-8')
            ->withHeader('origin: https://tutien.net')
            ->withHeader('sec-fetch-site: same-origin')
            ->withHeader('sec-fetch-mode: cors')
            ->withHeader('referer: https://tutien.net/account/tu_luyen/dot_pha')
            ->withHeader('accept-language: en-US,en;q=0.9')
            ->withProxy($proxy['host'], $proxy['port'], 'http://', $proxy['username'], $proxy['password'])
            ->withHeader($account->cookie)
            ->withData($buff)
            ->post();
        // $this->checkBac($account);
    }
}


<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Account;
use App\Services\AccountService;
use Redis;

class NopCongPhap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tutien:nop-cp';

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
        $account = Account::find(766);
        app(AccountService::class)->nopCongPhap($account);
        sleep(4);
        $keys = Redis::keys($account->account_id . "_cp_*");
        foreach ($keys as $key) {
            $id = str_replace($account->account_id . "_cp_", "", $key);
            $amount = Redis::get($key) || 0;

            $this->info($id);
            $this->info($amount);
            app(AccountService::class)->rutHanhTrang($account, $id, $amount);
        }
    }
}

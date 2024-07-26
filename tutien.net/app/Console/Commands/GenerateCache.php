<?php

namespace App\Console\Commands;

use App\Account;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class GenerateCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tutien:generate-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto generate cookie for use';

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
     */
    public function handle()
    {
        Redis::del(env('ACCOUNT_IDS_KEY', 'account_ids'));

        /** @var Collection $accounts */
        $accounts = Account::whereIsNsd(1)
            ->whereNotNull('cookie')
            ->get();

        $ids = $accounts->pluck('account_id')->toArray();
        Redis::set(env('ACCOUNT_IDS_KEY', 'account_ids'), json_encode($ids));
        Redis::set('default_cookie', $accounts->first()->cookie);

        foreach ($accounts as $account) {
            Redis::set($account->account_id . '_cookie', $account->cookie);
        }
    }
}

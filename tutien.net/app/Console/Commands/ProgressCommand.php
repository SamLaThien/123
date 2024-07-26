<?php

namespace App\Console\Commands;

use App\Account;
use App\Services\AccountService;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use App\Jobs\UpdateAccount;

class ProgressCommand extends Command
{
    const BASE_URL = '';

    protected $accountService;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tutien:get-detail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lấy các thông tin cơ bản của account';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(AccountService $accountService)
    {
        $this->accountService = $accountService;
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
	    $accounts = Account::where('cookie', '<>', '')
		    ->where('is_nsd', 1)
		    //->where('user_id', 1)
		    ->get();

        $bar = $this->output->createProgressBar($accounts->count());
        $bar->start();
        foreach ($accounts as $key => $account) {
	    $bar->advance();
	    try {
	        $this->accountService->getAccountInfo($account, '', true);
	    } catch (\Exception $e) {
	        debug($e->getMessage());
	    }
	    # UpdateAccount::dispatch($account, $account->cookie, true)->onQueue('accounts');
        }

        $bar->finish();
    }
}

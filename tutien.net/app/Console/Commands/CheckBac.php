<?php

namespace App\Console\Commands;

use App\Account;
use App\Services\AccountService;
use App\Jobs\UpdateAccount;
use Illuminate\Console\Command;

class CheckBac extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tutien:check-bac';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check bac cac acc';

    protected $accountService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(AccountService $accountService)
    {
        parent::__construct();
        $this->accountService = $accountService;
    }

    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
        $accounts = Account::whereIsNsd(0)->get();
        #$accounts = Account::whereIsNsd(1)->get();				  

        // $bar = $this->output->createProgressBar($accounts->count());
        // $bar->start();
        foreach ($accounts as $key => $account) {
            // $bar->advance();
            // $this->accountService->checkBac($account);
            UpdateAccount::dispatch($account, $account->cookie, true)->onQueue('accounts');
        }

        // $bar->finish();
    }
}

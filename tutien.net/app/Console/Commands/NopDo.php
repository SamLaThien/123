<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Account;
use App\Services\AccountService;

class NopDo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tutien:nop-do';

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
    public function __construct(AccountService $accountService)
    {
        $this->accountService = $accountService;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $accounts = Account::where('cookie', '<>', '')
		    ->get();

        $bar = $this->output->createProgressBar($accounts->count());
        $bar->start();
        foreach ($accounts as $key => $account) {
            $bar->advance();
            $this->accountService->checkRuong($account);
        }

        $bar->finish();
    }
}

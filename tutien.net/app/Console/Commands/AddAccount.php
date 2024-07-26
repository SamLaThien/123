<?php

namespace App\Console\Commands;

use App\Account;
use Illuminate\Console\Command;

class AddAccount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tutien:add-account {accountId} {accountName}';

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
        $accountId = $this->argument('accountId');
        $accountName = $this->argument('accountName');
        Account::create([
            'account_id' => $accountId,
            'account_name' => $accountName,
        ]);

        $this->info('New account added!!!');
    }
}

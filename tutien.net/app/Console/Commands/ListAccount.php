<?php

namespace App\Console\Commands;

use App\Account;
use Illuminate\Console\Command;

class ListAccount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'truyencv:list-account';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all accounts';

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
        $headers = ['Account id', 'Account name', 'Reading'];
        $accounts = Account::with(['reading.story'])->get()->toArray();

        $body = [];
        foreach ($accounts as $account) {
            $body[] = [
                'account_id' => $account['account_id'],
                'account_name' => $account['account_name'],
                'reading' => empty($account['reading']) ? 'Chưa đọc truyện gì!' : $account['reading']['story']['name'],
            ];
        }
        $this->table($headers, $body);
    }
}

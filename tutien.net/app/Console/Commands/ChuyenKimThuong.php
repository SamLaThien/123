<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Inventory;
use App\Account;
use App\Services\AccountService;

class ChuyenKimThuong extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tutien:chuyen-kt';

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
        $inventories = Inventory::whereHas('account')
            ->with('account')
            ->where('amount', '>', 0)
            ->where('item_id', 2)
            ->get();
        $bar = $this->output->createProgressBar($inventories->count());
        $bar->start();
        foreach ($inventories as $inventory) {
            $bar->advance();
            $account = $inventory->account;
            $amount = $inventory->amount;
            $this->accountService->chuyenDo($account, 57, $amount);
            sleep(2);
        }
        $bar->finish();
    }
}

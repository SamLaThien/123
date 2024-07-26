<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DotPhaService;
use App\Account;
use App\Events\CanDanMessage;

class CanDan1Acc extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tutien.net:can-dan {accountId} {danDuoc=ttd} {isDt=0} {vatphamphutro=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    protected $service;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(DotPhaService $dp)
    {
        $this->service = $dp;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $accountId = $this->argument('accountId');
        $danDuoc = ['danDuoc' => explode(',', $this->argument('danDuoc'))];
        $isDt = $this->argument('isDt');
        $vatphamphutro = ['vatphamphutro' => explode(',', $this->argument('vatphamphutro'))];
        $account = Account::find($accountId);
        sleep(2);
        $this->service->canDan($account, $danDuoc, $isDt, $vatphamphutro);
    }
}

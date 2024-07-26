<?php

namespace App\Console\Commands;

use App\Account;
use Illuminate\Console\Command;
use Ixudra\Curl\Facades\Curl;
use App\CookieHelper;
use App\Jobs\CallNsd as CallNsdJob;

class CallNsd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tutien:nsd';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tự động gọi tới nsd';

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
        $accounts = Account::where('is_nsd', 1)->where('cookie', '!=', '')->get();
        $proxies = config('proxies.list');
        $count = count($proxies);
        $bar = $this->output->createProgressBar($accounts->count());
        $bar->start();
        foreach ($accounts as $key => $account) {
            $bar->advance();
            CallNsdJob::dispatch($account, $key % $count)->onQueue('nsd');
        }

        $bar->finish();
    }
}

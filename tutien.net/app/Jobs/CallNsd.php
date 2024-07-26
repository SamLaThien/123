<?php

namespace App\Jobs;

use App\Account;
use App\Services\NsdService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CallNsd implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $account;
    protected $proxyIndex;

    /**
     * Create a new job instance.
     *
     * @param $account
     */
    public function __construct($account, $proxyIndex = 0)
    {
        $this->account = $account;
        $this->proxyIndex = $proxyIndex;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (!$this->account->is_nsd) {
            return;
        }

        $service = app(NsdService::class);
        $service->initNsd($this->account, $this->proxyIndex);
        // sleep(1);
        // $service->callLogBang($this->account);
        sleep(1);
        $service->callNsd($this->account, $this->proxyIndex);
    }
}

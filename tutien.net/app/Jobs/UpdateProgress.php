<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateProgress implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $account;
    protected $cookie;
    protected $autoDp;

    /**
     * Create a new job instance.
     *
     * @param Account $account
     */
    public function __construct(Account $account, $coookie = '', $autoDp = false)
    {
        $this->account = $account;
        $this->cookie = $coookie;
        $this->autoDp = $autoDp;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        app(AccountService::class)->getAccountInfo($this->account, $this->cookie, $this->autoDp);
    }
}

<?php

namespace App\Jobs;

use App\Account;
use App\Services\AccountService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class NopBac implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $accountService;
    protected $account;
    protected $itemId;
    protected $amount;

    /**
     * Create a new job instance.
     *
     * @param Account $account
     * @param int $amount
     */
    public function __construct(Account $account, $amount = 0)
    {
        $this->account = $account;
        $this->amount = $amount;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        app(AccountService::class)->nopBac($this->account, $this->amount);
    }
}

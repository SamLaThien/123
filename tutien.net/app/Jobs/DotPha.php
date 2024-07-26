<?php

namespace App\Jobs;

use App\Account;
use App\Services\AccountService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DotPha implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const HUYET_KHI_DAN = '10';
    const DE_GIAI_THUAN = '11';
    const TI_LOI_CHAU = '17';
    const THANH_TAM_DAN = '34';

    protected $account;

    /**
     * Create a new job instance.
     *
     * @param Account $account
     */
    public function __construct(Account $account)
    {
        $this->account = $account;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        app(AccountService::class)->dotPha($this->account);
    }
}

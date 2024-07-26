<?php

namespace App\Console\Commands;

use App\Account;
use App\Inventory;
use Illuminate\Console\Command;
use Ixudra\Curl\Facades\Curl;

class NopVatPham extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tutien:nop-vat-pham {vatPhamName}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Nop vp vao bang';

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
        $name = $this->argument('vatPhamName');
        $vatPhamId = (int) convertTbd($name);
        $tcvId = (int) convertTcvId($name);

        $this->info($vatPhamId);
        $this->info($tcvId);
        if ($tcvId == 0) {
            return;
        }

        $inventories = Inventory::whereHas('account')
            ->with('account')
            ->where('amount', '>', 0)
            ->where('item_id', $vatPhamId)
            ->get();

        foreach ($inventories as $inventory) {
            $account = $inventory->account;
            $this->info($account->account_name . ' - ' . $account->bang_phai);
            if (!$account || !$account->bang_phai) {
                continue;
            }

            $amount = $inventory->amount;
            $this->nopVatPham($account, $tcvId, $amount);
            $this->info($account->account_name . ': ' . $amount);
            sleep(2);
        }
    }

    /**
     * @param Account $account
     */
    public function nopVatPham(Account $account, $itemId, $amount)
    {
        Curl::to('https://tutien.net/account/vat_pham/')
            ->withHeader('authority: tutien.net')
            ->withHeader('accept: */*')
            ->withHeader('sec-fetch-dest: empty')
            ->withHeader('x-requested-with: XMLHttpRequest')
            ->withHeader('user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36')
            ->withHeader('dnt: 1')
            ->withHeader('content-type: application/x-www-form-urlencoded; charset=UTF-8')
            ->withHeader('origin: https://tutien.net')
            ->withHeader('sec-fetch-site: same-origin')
            ->withHeader('sec-fetch-mode: cors')
            ->withHeader('referer: https://tutien.net/account/vat_pham/')
            ->withHeader($account->cookie)
            ->withData([
                'btnDongGop' => 1,
                'shop' => $itemId,
                'txtNumber' => $amount,
            ])
            ->withResponseHeaders()
            ->returnResponseObject()
            ->post();
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Account;
use Ixudra\Curl\Facades\Curl;

class SendReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tutien:send-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check acc which has bad cookie';

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
     */
    public function handle()
    {
        $accounts = Account::where('cookie', 'not like', '%TAM=%')
            ->orWhere('cookie', 'not like', '%USER=%')
            ->get();
        $now = now();
        $message = "============= REPORT: " . $now->toDateTimeString() . " ==========";
        if ($accounts->count()) {
            $message .= "\nCác id bị lỗi cookie: ";
            foreach ($accounts as $account) {
                $message .= $account->account_id . " ";
            }
        } else {
            $message .= "\nKhông có account nào bị lỗi cookie!";
        }

        $message .= "\n==================== END REPORT ====================";

        $toId = '228826';
        Curl::to('https://tutien.net/index.php')
            ->withHeader('authority: tutien.net')
            ->withHeader('accept: */*')
            ->withHeader('accept-language: en-US,en;q=0.9')
            ->withHeader('content-type: application/x-www-form-urlencoded; charset=UTF-8')
            ->withHeader('cookie: USER=rJ%2FheR9OaHLs%3AFZywf%2BAsXOVMfxc%2F6qaZntCAH4N4ctmsfyj8qhxjqP3f; PHPSESSID=u39111oj7ikupn7v4acntce31b; reada=123')
            ->withHeader('origin: https://tutien.net')
            ->withHeader('referer: https://tutien.net/member/' . $toId)
            ->withHeader('sec-ch-ua: " Not A;Brand";v="99", "Chromium";v="102", "Google Chrome";v="102"')
            ->withHeader('sec-ch-ua-mobile: ?0')
            ->withHeader('sec-ch-ua-platform: "Linux"')
            ->withHeader('sec-fetch-dest: empty')
            ->withHeader('sec-fetch-mode: cors')
            ->withHeader('sec-fetch-site: same-origin')
            ->withHeader('user-agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/102.0.0.0 Safari/537.36')
            ->withHeader('x-requested-with: XMLHttpRequest')
            ->withData([
                'btnMemberComment' => 1,
                'media_id' => $toId,
                'num' => 5,
                'txtContent' => $message,
                'parent' => '',
            ])
            ->post();
    }
}

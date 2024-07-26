<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Account;
use Ixudra\Curl\Facades\Curl;
use voku\helper\HtmlDomParser;
use App\Services\AccountService;

class CanDan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tutien:can-dan {accountId} {danDuoc=ttd}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    const TTD_EXP = 150;
    const TCD_EXP = 200;
    const BND_EXP = 300;
    const BAD_EXP = 600;
    const HND_EXP = 1200;

    const ID_TTD = 9;
    const ID_TCD = 13;
    const ID_BND = 14;
    const ID_BAD = 40;
    const ID_HND = 62;

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
        $accountId = $this->argument('accountId');
        $danDuoc = $this->argument('danDuoc');
        $account = Account::whereAccountId($accountId)->first();
        $response = Curl::to('https://tutien.net/account/tu_luyen/dot_pha')
            ->withHeader('authority: tutien.net')
            ->withHeader('cache-control: max-age=0')
            ->withHeader('dnt: 1')
            ->withHeader('upgrade-insecure-requests: 1')
            ->withHeader('user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36')
            ->withHeader('sec-fetch-dest: document')
            ->withHeader('accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9')
            ->withHeader('sec-fetch-site: none')
            ->withHeader('sec-fetch-mode: navigate')
            ->withHeader('sec-fetch-user: ?1')
            ->withHeader('accept-language: vi-VN,vi;q=0.9,en-US;q=0.8,en;q=0.7,de;q=0.6,ja;q=0.5')
            ->withHeader($account->cookie)
            ->withResponseHeaders()
            ->returnResponseObject()
            ->get();

        $content = $response->content;
        $headers = $response->headers;

        $html = HTMLDomParser::str_get_html($content);
        if (gettype($html) == 'boolean') {
            return;
        }

        $info = $html->find('.progress-bar');
        $infoText = $html->find('#content strong > span');
        if (count($info) === 0)  {
            // debug($account->toArray());
            return;
        }

        $progressText = count($infoText) ? $infoText[0]->plaintext : '';
        $progressNumber = $info[0]->plaintext;

     	$this->info($account->account_id . " - " . $progressText . " - " . $progressNumber);
        $progressNumber = str_replace(")", "", $progressNumber);
        $progressNumber = explode("(", $progressNumber)[1];
        $progresses = explode("/", $progressNumber);

        $maxExp = intval($progresses[1]);
        $currentExp = intval($progresses[0]);
        $missingExp = $maxExp - $currentExp;
        $this->chuyenDanDuoc($account, $danDuoc, $missingExp);
        app(AccountService::class)->getAccountInfo($account, $account->cookie);
    }

    public function chuyenDanDuoc($account, $danDuoc, $exp)
    {
        $amount = 0;
        $danDuocId = self::ID_TTD;
        $amount10 = 0;
        switch ($danDuoc) {
            case 'ttd':
                $amount = (int) ($exp / self::TTD_EXP) + 1;
                $danDuocId = self::ID_TTD;
                break;
            case 'tcd':
                $amount = (int) ($exp / self::TCD_EXP) + 1;
                $danDuocId = self::ID_TCD;
                break;
            case 'bnd':
                $amount = (int) ($exp / self::BND_EXP) + 1;
                $danDuocId = self::ID_BND;
                break;
            case 'bad':
                $amount = (int) ($exp / self::BAD_EXP) + 1;
                $danDuocId = self::ID_BAD;
                break;
            case 'hnd':
                $amount = (int) ($exp / self::HND_EXP) + 1;
                $danDuocId = self::ID_HND;
                break;
        }

        $service = app(AccountService::class);
        $service->chuyenDo($account, $danDuocId, $amount);

        $amount10 = (int) ($amount / 10);
        $amount1 = $amount % 10;
        for ($i = 0; $i < $amount10; $i++) {
            $this->canDan($account, $danDuocId, 10);
            sleep(2);
        }

        for ($i = 0; $i < $amount1; $i++) {
            $this->canDan($account, $danDuocId, 1);
            sleep(2);
        }
    }

    public function canDan($account, $danDuocId, $amount = 1)
    {
        $response = Curl::to('https://tutien.net/account/vat_pham/')
            ->withHeader('authority: tutien.net')
            ->withHeader('sec-ch-ua: " Not A;Brand";v="99", "Chromium";v="96", "Google Chrome";v="96"')
            ->withHeader('dnt: 1')
            ->withHeader('sec-ch-ua-mobile: ?0')
            ->withHeader('user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36')
            ->withHeader('content-type: application/x-www-form-urlencoded; charset=UTF-8')
            ->withHeader('accept: */*')
            ->withHeader('x-requested-with: XMLHttpRequest')
            ->withHeader('sec-ch-ua-platform: "macOS"')
            ->withHeader('origin: https://tutien.net')
            ->withHeader('sec-fetch-site: same-origin')
            ->withHeader('sec-fetch-mode: cors')
            ->withHeader('sec-fetch-dest: empty')
            ->withHeader('referer: https://tutien.net/account/vat_pham/')
            ->withHeader('accept-language: en-US,en;q=0.9,vi;q=0.8')
            ->withHeader($account->cookie)
            ->withData([
                'btnTangExpBac' => 1,
                'items' => $danDuocId,
                'txtSoLuong' => $amount
            ])
            ->withResponseHeaders()
            ->returnResponseObject()
            ->post();
        $content = $response->content;
        var_dump(json_decode(json_encode($content)));
    
    }

}

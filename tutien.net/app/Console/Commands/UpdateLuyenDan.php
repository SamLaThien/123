<?php

namespace App\Console\Commands;

use App\Account;
use App\LuyenDan as LuyenDanTable;
use App\CookieHelper;
use Carbon\Carbon;
use HTMLDomParser;
use Illuminate\Console\Command;
use Ixudra\Curl\Facades\Curl;

class UpdateLuyenDan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tutien:update-luyen-dan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tu dong luyen dan';

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
        $account = Account::find(7);
        $this->luyenDan($account);
    }

    private function luyenDan($account)
    {
        $response = Curl::to('https://tutien.net/account/tu_luyen/luyen_dan_that/')
            ->withHeader(':authority: tutien.net')
            ->withHeader(':method: GET')
            ->withHeader('/account/tu_luyen/luyen_dan_that/')
            ->withHeader(':scheme: https')
            ->withHeader('accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3')
            // ->withHeader('accept-encoding: gzip, deflate, br')
            ->withHeader('accept-language: vi-VN,vi;q=0.9,en-US;q=0.8,en;q=0.7,de;q=0.6,ja;q=0.5')
            ->withHeader('cache-control: max-age=0')
            ->withHeader($account->cookie)
            // ->withHeader('sec-fetch-mode: navigate')
            // ->withHeader('sec-fetch-site: none')
            // ->withHeader('sec-fetch-user: ?1')
            ->withHeader('upgrade-insecure-requests: 1')
            ->withHeader('user-agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.90 Safari/537.36')
            ->withResponseHeaders()
            ->returnResponseObject()
            ->get();

        $content = $response->content;
        $headers = $response->headers;

        // Update cookie
        if (!empty($headers['Set-Cookie'])) {
            $cookie = app(CookieHelper::class)->updateCookie($account, $headers);
            $newCookie = implode('; ', $cookie);
            $account->update(['cookie' => $newCookie]);
        }

        $html = HTMLDomParser::str_get_html($content);
        if (gettype($html) == 'boolean') {
            return;
        }
        
        // $danPhuongs = $html->find('form label .text-warning');
        $danPhuongs = $html->find('form input[name="radLuyenDan"]');
        // for ($i = 0; $i < $overview)
        $names = config('luyendan.dan_phuong');
        foreach ($danPhuongs as $key => $danPhuong) {
            $danPhuongId = $danPhuong->value;
            $danPhuongName = $names[$danPhuongId]['name'];

            LuyenDanTable::create([
                'account_id' => $account->id,
                'dan_phuong_id' => $danPhuongId,
                'dan_phuong_name' => $danPhuongName,
            ]);
        }
    }
}

<?php

namespace App\Services;
use App\Account;
use App\CookieHelper;
use Ixudra\Curl\Facades\Curl;

class NsdMobileService
{
    public function callNsdForAll()
    {
        $accounts = Account::all();
        foreach ($accounts as $account) {
            $this->callNsd($account);
        }
    }

    private function callNsd(Account $account)
    {
        \Log::debug($account->account_id);
        $response = Curl::to('http://aios.tutien.net/chat-chit/')
            ->withHeader('Accept: */*')
            //->withHeader('Accept-Encoding: gzip, deflate')
            ->withHeader('Accept-Language: vi-VN,vi;q=0.9,en-US;q=0.8,en;q=0.7,de;q=0.6,ja;q=0.5')
            ->withHeader('Connection: keep-alive')
            ->withHeader('Content-Length: 41')
            ->withHeader('Content-type: application/x-www-form-urlencoded')
            ->withHeader('Host: aios.truyencv.com')
            ->withHeader('Origin: http://api.truyencv.com')
            // ->withHeader('Referer: http://api.tutien.net/chat-chit/?userid=291130&sig=1693f2239198069778c16f5874c1e468&deviceid=243452&os=android&app_version=1.0.4')
            ->withHeader('User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36')
            ->withData([
                'showThongBao' => 1,
                'chkLinhKhi' => 0,
                'userid' => $account->account_id
            ])
            ->post();
    }
}
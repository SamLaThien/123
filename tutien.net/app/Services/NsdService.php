<?php

namespace App\Services;

use App\Account;
use App\CookieHelper;
use Ixudra\Curl\Facades\Curl;

class NsdService
{
    public function callNsd(Account $account, $proxyIndex = 0)
    {
        $proxies = config('proxies.list');
        $proxy = $proxies[$proxyIndex];
        $cookie = $account->cookie;
        $response = Curl::to('https://tutien.net/account/bang_phai/')
            ->withHeader('user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.142 Safari/537.36')
            ->withHeader('host: tutien.net')
            ->withHeader('content-type: application/x-www-form-urlencoded; charset=UTF-8')
            ->withHeader($cookie)
            ->withHeader('content-length: 27')
            ->withHeader('expect: 100-continue')
            ->withData([
                'showThongBao' => 1,
                'chkLinhKhi' => 0,
            ])
            ->withProxy($proxy['host'], $proxy['port'], 'http://', $proxy['username'], $proxy['password'])
            ->withResponseHeaders()
            ->returnResponseObject()
            ->post();

        // $headers = $response->headers;
        // // Update cookie
        // if (!empty($headers['Set-Cookie']) || !empty($headers['set-cookie'])) {
        //     $cookie = app(CookieHelper::class)->updateCookie($cookie, $headers);
        //     $newCookie = implode('; ', $cookie);
        //     $account->update(['cookie' => $newCookie]);
        // }
    }

    public function callLogBang(Account $account, $proxyIndex = 0)
    {
        $proxies = config('proxies.list');
        $proxy = $proxies[$proxyIndex];
        $cookie = $account->cookie;
        $response = Curl::to('https://tutien.net/account/bang_phai/')
            ->withHeader('user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.142 Safari/537.36')
            ->withHeader('host: tutien.net')
            ->withHeader('content-type: application/x-www-form-urlencoded; charset=UTF-8')
            ->withHeader($cookie)
            ->withHeader('content-length: 27')
            ->withHeader('expect: 100-continue')
            ->withData([
                'btnLogBang' => 1,
            ])
            ->withProxy($proxy['host'], $proxy['port'], 'http://', $proxy['username'], $proxy['password'])
            ->withResponseHeaders()
            ->returnResponseObject()
            ->post();

        // $headers = $response->headers;
        // // Update cookie
        // if (!empty($headers['Set-Cookie']) || !empty($headers['set-cookie'])) {
        //     $cookie = app(CookieHelper::class)->updateCookie($cookie, $headers);
        //     $newCookie = implode('; ', $cookie);
        //     $account->update(['cookie' => $newCookie]);
        // }
    }

    public function initNsd(Account $account, $proxyIndex = 0)
    {
        $proxies = config('proxies.list');
        $proxy = $proxies[$proxyIndex];
        $cookie = $account->cookie;
        $response = Curl::to('https://tutien.net/account/bang_phai/nghi_su_dien')
            ->withHeader('user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.142 Safari/537.36')
            ->withHeader('host: tutien.net')
            ->withHeader($cookie)
            ->withHeader('connection: Keep-Alive')
            ->withProxy($proxy['host'], $proxy['port'], 'http://', $proxy['username'], $proxy['password'])
            // ->withProxy('103.3.246.221', 49284, 'http://', 'user49284', 'slgSqfve9E')
            ->withResponseHeaders()
            ->returnResponseObject()
            ->get();

        $headers = $response->headers;
        // Update cookie
        if (!empty($headers['Set-Cookie']) || !empty($headers['set-cookie'])) {
            $cookie = app(CookieHelper::class)->updateCookie($cookie, $headers);
            $newCookie = implode('; ', $cookie);
            $account->update(['cookie' => $newCookie]);
        }
    }
}

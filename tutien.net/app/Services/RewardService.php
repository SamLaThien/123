<?php

namespace App\Services;

use App\Reward;
use Illuminate\Support\Str;
use Ixudra\Curl\Facades\Curl;
use Carbon\Carbon;
use App\Account;

class RewardService
{
    public function getAppReward(Reward $reward)
    {
        $timestamp = Carbon::now()->timestamp;
        $sig = $this->generateSig($reward, $timestamp);

        $response = Curl::to('http://api.tutien.net/reward')
            ->withHeader('User-Agent:' . $reward->user_agent)
            ->withData([
                'user_id' => $reward->account_id,
                'time' => $timestamp,
                'sig' => $sig,
                'deviceid' => '5c7257e555d43067',
                'os' => 'android',
                'app_version' => '1.0.4',
            ])
//            ->withContentType('application/json')
//            ->asJson()
            ->get();

        dd($response);
    }

    public function fetchProxies()
    {

    }

    public function registerDeviceToken(Account $account)
    {
        $token = $this->generateDeviceToken();
        $response = Curl::to('http://api.tutien.net/registDeviceToken')
            ->withData([
                'user_id' => $account->account_id,
                'status' => 1,
                'device_token' => 'Android-' . $token
            ])
            ->withContentType('application/json')
            ->asJson()
            ->post();

        if ($response['success']) {
            Reward::create([
                'account_id' => $account->account_id,
                'device_token' => $token,
                'user_agent' => $this->generateUserAgent(),
            ]);
        }
    }

    public function generateDeviceToken()
    {
        return Str::random(11) . ':' . Str::random(14) . '-' . Str::random(34) . '_' . Str::random(1) . '-' . Str::random(17) . '-' . Str::random(27) . '_' . Str::random(42);
    }

    public function generateSig(Reward $reward, $timestamp)
    {
        $deviceToken = $reward->device_token;
        $inToken = "reward3V3ra52Lw1." . $reward->account_id . ".50.bac." . $timestamp . "." . $deviceToken;
        return md5($inToken);
    }

    public function generateUserAgent()
    {
        $userAgents = [
            'Mozilla/5.0 (Linux; Android 8.0.0; SM-G960F Build/R16NW) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.84 Mobile Safari/537.36',
            'Mozilla/5.0 (Linux; Android 7.0; SM-G892A Build/NRD90M; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/60.0.3112.107 Mobile Safari/537.36',
            'Mozilla/5.0 (Linux; Android 7.0; SM-G930VC Build/NRD90M; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/58.0.3029.83 Mobile Safari/537.36',
            'Mozilla/5.0 (Linux; Android 6.0.1; SM-G935S Build/MMB29K; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/55.0.2883.91 Mobile Safari/537.36',
            'Mozilla/5.0 (Linux; Android 6.0.1; SM-G920V Build/MMB29K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.98 Mobile Safari/537.36',
            'Mozilla/5.0 (Linux; Android 6.0.1; Nexus 6P Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.83 Mobile Safari/537.36',
            'Mozilla/5.0 (Linux; Android 7.1.1; G8231 Build/41.2.A.0.219; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/59.0.3071.125 Mobile Safari/537.36',
            'Mozilla/5.0 (Linux; Android 6.0.1; E6653 Build/32.2.A.0.253) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.98 Mobile Safari/537.36',
            'Mozilla/5.0 (Linux; Android 7.1.2; AFTMM Build/NS6265; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/70.0.3538.110 Mobile Safari/537.36',
            'Mozilla/5.0 (Linux; Android 7.1.2; AFTMM Build/NS6264; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/59.0.3071.125 Mobile Safari/537.36',
            'Mozilla/5.0 (Linux; Android 9; SM-G960F Build/PPR1.180610.011; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/74.0.3729.157 Mobile Safari/537.36',
            'Mozilla/5.0 (Linux; Android 9; SM-G950F Build/PPR1.180610.011; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/74.0.3729.157 Mobile Safari/537.36',
            'Mozilla/5.0 (Linux; Android 7.0; SM-G610M Build/NRD90M) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Mobile Safari/537.36',
            'Mozilla/5.0 (Linux; Android 7.1; Mi A1 Build/N2G47H) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.83 Mobile Safari/537.36',
            'Mozilla/5.0 (Linux; Android 7.0; SM-G892A Build/NRD90M; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/68.0.3440.1805 Mobile Safari/537.36',
            'Mozilla/5.0 (Linux; Android 7.0; SM-G570M Build/NRD90M) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Mobile Safari/537.36',
            'Mozilla/5.0 (Linux; Android 8.0.0; SM-G930F Build/R16NW; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/74.0.3729.157 Mobile Safari/537.36',
            'Mozilla/5.0 (Linux; Android 9; SAMSUNG SM-G960U) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/10.2 Chrome/71.0.3578.99 Mobile Safari/537.36',
            'Mozilla/5.0 (Linux; Android 9; SM-G965F Build/PPR1.180610.011; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/74.0.3729.157 Mobile Safari/537.36',
            'Mozilla/5.0 (Linux; Android 7.1.2; vivo y35 Build/N2G48B; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/66.0.3359.158 Mobile Safari/537.36',
            'Mozilla/5.0 (Linux; Android 9; SAMSUNG SM-G960U) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/10.1 Chrome/71.0.3578.99 Mobile Safari/537.36',
            'Mozilla/5.0 (Linux; Android 7.0; SM-G570M Build/NRD90M) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.87 Mobile Safari/537.36',
            'Mozilla/5.0 (Linux; Android 9; SAMSUNG SM-G950U) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/10.2 Chrome/71.0.3578.99 Mobile Safari/537.36',
            'Mozilla/5.0 (Linux; Android 7.1; vivo 1716 Build/N2G47H) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.98 Mobile Safari/537.36',
            'Mozilla/5.0 (Linux; Android 7.0; SAMSUNG SM-G610M Build/NRD90M) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/7.4 Chrome/59.0.3071.125 Mobile Safari/537.36',
            'Mozilla/5.0 (Linux; Android 7.0; TRT-LX2 Build/HUAWEITRT-LX2; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/59.0.3071.125 Mobile Safari/537.36',
        ];

        return $userAgents[rand(0, count($userAgents) - 1)];
    }
}


// http://api.tutien.net/reward?
//user_id=291130
//sig=57eba53a3941e1c8e33fe26799d81c76
//time=1590397361
//deviceid=5c7257e555d43067
//os=android
//app_version=1.0.4
//Android-dICgiOipz3M:APA91bFBrBRndngRh554ICZ6Z3mbWxJ2pcwDLhnoFqLDia9Bx2zgAPfGdGF2zt69z76YxiON_Nn84sZB1efXNC2cqF2-uXe9F1DpnP_FiNZzPEgbNScDp6GFVfMmzagKT4Anit-FG3JU

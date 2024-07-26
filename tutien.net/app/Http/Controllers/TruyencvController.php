<?php

namespace App\Http\Controllers;

use App\Account;
//use App\Jobs\QuayVqmm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use App\Events\CanDanMessage;
//use Ixudra\Curl\Facades\Curl;
//use Carbon\Carbon;
//use voku\helper\HtmlDomParser;
use Redis;
use App\Services\ShellCommand;
use App\Services\CommandService;
use App\Services\AccountService;

class TruyencvController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function vqmm()
    {
        $accounts = auth()->user()->accounts()->where("tai_san", ">", 1000)->orderBy("tai_san")->get();
        return view("vqmm")->with(compact("accounts"));
    }

    public function quay(Request $request)
    {
        $accounts = $request->get('accs', []);
        $accountIds = Arr::pluck($accounts, 'id');
        if (count($accountIds) == 0) {
            Redis::set('cookie_vqmm', json_encode([]), 'EX', 30 * 60);
            $res = shell_exec('/usr/bin/pm2 stop vqmm');
            debug($res);
            return response()->json("Done");
        }
        $cookies = Account::whereIn('id', $accountIds)->get('cookie')->pluck('cookie')->toArray();

        $arr = [];
        foreach ($cookies as $cookie) {
            $cookieArr = explode('; ', $cookie);
            $userCookie = '';
            foreach ($cookieArr as $index => $item) {
                if (strpos($item, 'USER') !== false) {
                    $arr[] = str_replace('cookie: ', '', $item);
                    continue;
                }
            }
        }

        Redis::set('cookie_vqmm', json_encode($arr), 'EX', 30 * 60); // expire after 1 hour
        $res = shell_exec('/usr/bin/pm2 restart vqmm');
        debug($res);
        return response()->json("Done");
    }

    public function canDan(Request $request)
    {
        $accountId = $request->get('accountId');
        $danDuoc = join(',', $request->get('danDuoc'));
        $vatphamphutro = join(',', $request->get('vatphamphutro'));
        $isDt = $request->get('is_dt', 0);
        // dispatch(function () {
        //     Artisan::call("tutien:can-dan-sll " . $accountId . " " . $danDuocName);
        // });
        //$res = ShellCommand::execute("php /home/pham.van.doanh/tutien.net/artisan tutien:can-dan-sll " . $accountId . " " . $danDuocName . "  >> /dev/null 2>&1");
        //debug($res);
        $service = app(CommandService::class);
        $service->runBackgroundCommand("tutien.net:can-dan " . $accountId . " " . $danDuoc .  " " . $isDt . " " . $vatphamphutro);
        return response()->json("Done");
    }
    public function canSam(Request $request)
    {
        $accountId = $request->get('accountId');
        $itemId = $request->get('itemId');
        $number = $request->get('number');
        $account = Account::find($accountId);
        $service = app(AccountService::class);
        sleep(2);
        $res = $service->chuyenDo($account, $itemId, $number);
        if ($res == 1) {
            sleep(2);
            for ($i = 0; $i < $number; $i++) {
                $res = $service->suDung($account, $itemId);
                CanDanMessage::dispatch($account, $res);
                sleep(3);
            }
            return;
        }
        return response()->json("Lỗi Chuyển Sâm");
    }

    public function canDanSll(Request $request)
    {
        $accountId = $request->get('accountId');
        $danDuocName = join(',', $request->get('danDuocName'));
        $vatphamphutro = join(',', $request->get('vatphamphutro'));
        $isDt = $request->get('is_dt', 0);
        // dispatch(function () {
        //     Artisan::call("tutien:can-dan-sll " . $accountId . " " . $danDuocName);
        // });
        //$res = ShellCommand::execute("php /home/pham.van.doanh/tutien.net/artisan tutien:can-dan-sll " . $accountId . " " . $danDuocName . "  >> /dev/null 2>&1");
        //debug($res);
        $service = app(CommandService::class);
        $service->runBackgroundCommand("tutien:can-dan-sll " . $accountId . " " . $danDuocName .  " " . $isDt . " " . $vatphamphutro);
        return response()->json("Done");
    }

    public function moRuong(Account $account)
    {
        $res = app(AccountService::class)->moRuong($account);
        return response()->json(['result' => $res]);
    }

    public function openEventItem(Account $account)
    {
        $res = app(AccountService::class)->openEventItem($account);
        return response()->json(['result' => $res['data']]);
    }


    /**
     * @param string $to
     * @param string $cookie
     * @param string $referer
     * @return mixed
     */
    private function getBaseCurl(string $to, string $cookie, string $referer = '')
    {
        if (!$referer) {
            $referer = $to;
        }

        return Curl::to($to)
            ->withHeader('authority: truyencv.com')
            ->withHeader('accept: */*')
            ->withHeader('sec-fetch-dest: empty')
            ->withHeader('x-requested-with: XMLHttpRequest')
            ->withHeader('user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.3990.0 Safari/537.36')
            ->withHeader('content-type: application/x-www-form-urlencoded; charset=UTF-8')
            ->withHeader('origin: https://truyencv.com')
            ->withHeader('sec-fetch-site: same-origin')
            ->withHeader('sec-fetch-mode: cors')
            ->withHeader('referer: ' . $referer)
            ->withHeader('accept-language: en-US,en;q=0.9')
            ->withHeader($cookie);
    }
}

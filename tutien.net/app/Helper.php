<?php

use App\Account;
use Ixudra\Curl\Facades\Curl;

function vn_to_str($str)
{
    $unicode = array(
        'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
        'd' => 'đ',
        'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
        'i' => 'í|ì|ỉ|ĩ|ị',
        'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
        'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
        'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
        'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
        'D' => 'Đ',
        'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
        'I' => 'Í|Ì|Ỉ|Ĩ|Ị',
        'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
        'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
        'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
    );

    foreach ($unicode as $nonUnicode => $uni) {
        $str = preg_replace("/($uni)/i", $nonUnicode, $str);
    }

    //$str = str_replace(' ', '-', $str);
    return strtolower($str);
}

function buy_ttd($accountId)
{
    $hac_diem_url = 'https://tutien.net/account/tu_luyen/hac_diem/';
    $account = Account::where(['account_id' => $accountId])->first();

    $response = Curl::to($hac_diem_url)
        ->withHeader(":authority: tutien.net")
        ->withHeader(":method: POST")
        ->withHeader(":path: /account/tu_luyen/hac_diem/")
        ->withHeader(":scheme: https")
        ->withHeader("accept: *")
        // ->withHeader("accept-encoding: gzip, deflate, br")
        ->withHeader("accept-language: vi-VN,vi;q=0.9,en-US;q=0.8,en;q=0.7,de;q=0.6,ja;q=0.5")
        ->withHeader("content-length: 34")
        ->withHeader("content-type: application/x-www-form-urlencoded; charset=UTF-8")
        ->withHeader("cache-control: no-cache")
        ->withHeader($account->cookie)
        ->withHeader("pragma: no-cache")
        ->withHeader("upgrade-insecure-requests: 1")
        ->withHeader("user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36")
        ->withResponseHeaders()
        ->withData([
            'btnHacDiemMua' => 1,
            'shop' => 9,
            'txtNumber' => 1,
        ])
        ->post();
}

function dot_pha($accountId)
{
    $account = Account::where(['account_id' => $accountId])->first();

    $url = 'https://tutien.net/account/tu_luyen/dot_pha/';
    $response = Curl::to($url)
        ->withHeader('authority: tutien.net')
        ->withHeader(':method: POST')
        ->withHeader(':path: /account/tu_luyen/dot_pha/')
        ->withHeader(':scheme: https')
        ->withHeader('accept: */*')
        ->withHeader('accept-encoding: gzip, deflate, br')
        ->withHeader('accept-language: vi-VN,vi;q=0.9,en-US;q=0.8,en;q=0.7,de;q=0.6,ja;q=0.5')
        ->withHeader('content-length: 11')
        ->withHeader('content-type: application/x-www-form-urlencoded; charset=UTF-8')
        ->withHeader($account->cookie)
        ->withHeader('origin: https://tutien.net')
        ->withHeader('referer: https://tutien.net/account/tu_luyen/dot_pha/')
        ->withHeader('user-agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36')
        ->withHeader('x-requested-with: XMLHttpRequest')
        ->withData([
            'btnDotPha' => 1,
        ])
        ->post();
}

function dot_pha_account($account)
{
    $url = 'https://tutien.net/account/tu_luyen/dot_pha/';
    $response = Curl::to($url)
        ->withHeader('authority: tutien.net')
        ->withHeader(':method: POST')
        ->withHeader(':path: /account/tu_luyen/dot_pha/')
        ->withHeader(':scheme: https')
        ->withHeader('accept: */*')
        ->withHeader('accept-encoding: gzip, deflate, br')
        ->withHeader('accept-language: vi-VN,vi;q=0.9,en-US;q=0.8,en;q=0.7,de;q=0.6,ja;q=0.5')
        ->withHeader('content-length: 11')
        ->withHeader('content-type: application/x-www-form-urlencoded; charset=UTF-8')
        ->withHeader($account->cookie)
        ->withHeader('origin: https://tutien.net')
        ->withHeader('referer: https://tutien.net/account/tu_luyen/dot_pha/')
        ->withHeader('user-agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36')
        ->withHeader('x-requested-with: XMLHttpRequest')
        ->withData([
            'btnDotPha' => 1,
        ])
        ->post();
}

function debug($abc)
{
    \Log::debug($abc);
}

function vietTat($str)
{
    if ($str == 'hợp nguyên đan') {
        return 'hnd2';
    }
    if ($str == 'bồ đề quả') {
        return 'bdq2';
    }
    $words = explode(" ", vn_to_str($str));
    $letters = "";
    foreach ($words as $value) {
        $letters .= substr($value, 0, 1);
    }

    return $letters;
}

// 'tàng bảo đồ',
// 'tàng bảo đồ cao',
// 'la bàn',
// 'quy giáp',
// 'dạ minh châu',
// 'băng hỏa ngọc',
// 'túi sủng vật',
// 'túi thức ăn',
// 'túi phân bón',
// 'thời gian chi thủy',
// 'thần bí bảo rương',
function convertTbd($str)
{
    switch ($str) {
        case 'tbd':
            return 1;
        case 'tbdc':
            return 2;
        case 'lb':
        case 'la-ban':
            return 3;
        case 'qg':
        case 'quy-giap':
            return 4;
        case 'dmc':
            return 5;
        case 'bhn':
            return 6;
        case 'tsv':
            return 7;
        case 'tta':
            return 8;
        case 'tpb':
            return 9;
        case 'tgct':
            return 10;
        case 'tbbr':
            return 11;
        case 'gg':
            return 12;
        case 'ttd':
            return 13;
        case 'tcd':
            return 14;
        case 'bnd':
            return 15;
        case 'bad':
            return 16;
        case 'hnd':
            return 17;
        case 'hnd2':
            return 18;
        case 'bdq':
        case 'dao':
            return 19;
        case 'bdq2':
        case 'de':
            return 20;
        case 'ccg':
            return 21;
    }
}

function convertTcvId($name)
{
    switch ($name) {
        case 'lb':
        case 'la-ban':
            return 11687;
        case 'qg':
        case 'quy-giap':
            return 11689;
        case 'tpb':
            return 32180;
        case 'tta':
            return 34348;
        case 'tsv':
            return 34340;
        case 'tgct':
            return 32002;
        case 'bt':
            return 40420;
        case 'ltbk':
            return 39388;
        case 'vcmk':
            return 37312;
        case 'ctd':
            return 60;
        case 'dmc':
            return 6853;
        case 'bhn':
            return 18;
        case 'tlt':
            return 10155;
        case 'hnc':
            return 10152;
        case 'snc':
            return 10154;
        case 'tnc':
            return 10153;
        case 'ltcp':
            return 76;
        case 'ttd':
            return 9;
        case 'tcd':
            return 13;
        case 'bnd':
            return 14;
        case 'bad':
            return 40;
        case 'hnd':
            return 62;
        case 'hnd2':
            return 603;
        case 'bdq':
        case 'dao':
            return 32004;
        case 'bdq2':
        case 'de':
            return 35680;
        case 'ccg':
            return 68575;
        default:
            return 0;
    }
}

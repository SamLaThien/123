<?php

namespace App\Services;


use App\Account;
use App\Proxy;
use App\AccountLog;
use App\CookieHelper;
use App\Jobs\NopDo;
use App\Jobs\NopKho;
use App\Jobs\DotPha;
use App\Jobs\UpdateAccount;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Redis;

use Carbon\Carbon;
// use HTMLDomParser;
use voku\helper\HtmlDomParser;
use Ixudra\Curl\Facades\Curl;

class AccountService
{
    protected $excepts;
    protected $requiredItems;

    const ID_TTD = 9;
    const ID_TCD = 13;
    const ID_BND = 14;
    const ID_UTD = 22;
    const ID_BAD = 40;
    const ID_PTD = 36;
    const ID_LTTHP = 61;
    const ID_LTCP = 76;
    const ID_TLHP = 599;
    const ID_TLTP = 600;
    const ID_TLTHP = 601;
    const ID_TLCP = 602;
    const LA_BAN = '11687';
    const QUY_GIAP = '11689';

    public function getAllAccountProgress()
    {
        $accounts = Account::whereIsNsd(1)->get();
        foreach ($accounts as $account) {
            $this->getAccountInfo($account);
            sleep(2);
        }
    }

    public function getAccountInfo(Account $account, $cookie = '', $autoDp = false)
    {
        $proxies = config('dp_proxies.list');
        $proxy = Arr::random($proxies);
        $response = Curl::to('https://tutien.net/member/' . $account->account_id)
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
            //->withProxy($proxy['host'], $proxy['port'], 'http://', $proxy['username'], $proxy['password'])
            ->withResponseHeaders()
            ->returnResponseObject()
            ->get();

        if (gettype($response->content) == 'boolean') {
            debug($account->account_id);
            return;
        }
        try {
            $content = $response->content;
            $html = HTMLDomParser::str_get_html($content);
        } catch (\Exception $e) {
            debug("Cookie error: no-content " . $account->account_name . " (" . $account->account_id . ")");
            return;
        }

        if (gettype($html) == 'boolean') {
            debug("Cookie error: no-content " . $account->account_name . " (" . $account->account_id . ")");
            return;
        }

        $info = $html->find('.block-detail-sidebar-author');
        if (count($info) === 0) {
            debug("Cookie error: no-info " . $account->account_name . " (" . $account->account_id . ")");
            return;
        }
        $tho = $this->checkMenh($account);

        $capDo = $info[1]->find('strong')->plaintext;
        $capBac = str_replace('<strong><span class="cap-', 'Cấp ', $info[1]->find('strong'));
        $capBac = explode('">', $capBac)[0];
        $data = $this->checkDong($account);
        $progress = $info[1]->find('.progress-bar')->plaintext;
        $progressPercent = str_replace('%', '', $progress[0]);
        $progress = $capDo[0] . ' - ' . $progress[0] . ' - ' . $capBac . ' - ' . $tho . $data;

        $progress = preg_replace('/\s+/', ' ', $progress);
        $taiSan = $info[0]->find('.statistic .row .item')[2];
        $taiSan = trim($taiSan->plaintext);
        $name = $html->find('h2.name')[0]->plaintext;
        $bangPhai = $html->find("#truyencv-detail-introduction .block-detail-sidebar-author .overview")[1];
        $bangPhai = preg_replace("/๖ۣۜ/", "", $bangPhai->plaintext);
        $isExpIncrease = true;
        if ($account->progress == $progress) {
            $isExpIncrease = false;
        }
        $account->update([
            'tai_san' => $taiSan,
            'progress' => $progress,
            'account_name' => $name,
            'bang_phai' => Str::slug($bangPhai),
            'progress_change' => $progressPercent,
        ]);

        $this->filterInventory($account, $content);

        if (strpos($progress, '100%') !== false && strpos($progress, 'Viên Mãn') === false) {
            if (
                strpos($progress, 'Luyện Khí') !== false ||
                strpos($progress, 'Trúc Cơ') !== false ||
                strpos($progress, 'Kim Đan') !== false ||
                strpos($progress, 'Nguyên Anh') !== false ||
                strpos($progress, 'Hóa Thần') !== false
            ) {
                $this->dotPha($account);
            }
        }
    }

    public function checkMenh(Account $account)
    {
        $proxies = config('dp_proxies.list');
        $proxy = Arr::random($proxies);
        $response = Curl::to('https://tutien.net/account/tu_luyen/dot_pha/')
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
            ////->withProxy($proxy['host'], $proxy['port'], 'http://', $proxy['username'], $proxy['password'])
            ->withResponseHeaders()
            ->returnResponseObject()
            ->get();

        $content = $response->content;
        $headers = $response->headers;

        $html = HTMLDomParser::str_get_html($content);
        if (gettype($html) == 'boolean') {
            return;
        }

        $cap = $html->find('.text-muted');
        $thoNguyen = str_replace('<p class="text-muted">', "", $cap[1]);
        $thoNguyen = str_replace('</strong>', "", $thoNguyen);
        $thoNguyen = str_replace('<strong>', "", $thoNguyen);
        $thoNguyen = str_replace('</p>', "", $thoNguyen);
        //$capDo = explode('">', $capDo)[0];
        return $thoNguyen;
    }

    public function checkDong(Account $account)
    {
        $proxies = config('dp_proxies.list');
        $proxy = Arr::random($proxies);
        $response = Curl::to('https://tutien.net/account/bang_phai/dong_thien/')
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
            ////->withProxy($proxy['host'], $proxy['port'], 'http://', $proxy['username'], $proxy['password'])
            ->withResponseHeaders()
            ->returnResponseObject()
            ->get();

        $content = $response->content;
        $headers = $response->headers;

        $html = HTMLDomParser::str_get_html($content);
        if (strpos($html, 'Đạo hữu chưa được phép vào động thiên') !== false) {
            return ' ';
        }
        return '✓';
    }

    public function getAccountProgress(Account $account, $cookie = '', $autoDp = false)
    {
        if ($cookie == '') {
            $cookie = $account->cookie;
        }

        if ($cookie == '') {
            debug('Empty cookie!');
            return;
        }

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
            ->withHeader($cookie)
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
        if (count($info) === 0) {
            // debug($account->toArray());
            return;
        }

        $progressText = count($infoText) ? $infoText[0]->plaintext : '';
        $progressNumber = $info[0]->plaintext;
        $progress = $progressText . ' - ' . $progressNumber;

        $updateText = $this->getSpeed($account, $progressNumber);
        $account->update([
            'progress' => $progressText . ' - ' . $progressNumber,
            'progress_change' => htmlentities('<span class="text-muted">' . $updateText . '</span>')
        ]);

        if (strpos($progress, '100%') !== false && strpos($progress, 'Viên Mãn') === false) {
            $this->dotPha($account);
        }
    }

    public function checkRuongCp(Account $account, $nopAll = false)
    {
        $proxies = config('dp_proxies.list');
        $proxy = Arr::random($proxies);
        $response = Curl::to('https://tutien.net/account/vat_pham/')
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
            //->withProxy($proxy['host'], $proxy['port'], 'http://', $proxy['username'], $proxy['password'])
            ->withResponseHeaders()
            ->returnResponseObject()
            ->get();

        $content = $response->content;
        $headers = $response->headers;

        if (gettype($content) == 'boolean') {
            return;
        }
        $html = HTMLDomParser::str_get_html($content);
        if (gettype($html) == 'boolean') {
            return;
        }

        $items = $html->find('#congphap div[id^=row_]');
        foreach ($items as $item) {
            $itemInfo = $item->find('.form-inline', 0)->innertext;
            $itemId = substr($itemInfo, strpos($itemInfo, "txtBac"));
            $itemId = substr($itemId, 6, strpos($itemId, '"') - 6);
            $amount = $item->find('span[id^="shopnum"]')[0]->plaintext;

            if ($itemId == '16') continue;
            $this->nopKho($account, $itemId, $amount);
            sleep(1);
        }
    }

    public function checkRuong(Account $account, $nopAll = false)
    {
        //$proxies = config('dp_proxies.list');
        // $proxy = Arr::random($proxies);
        $response = Curl::to('https://tutien.net/account/vat_pham/')
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
            //->withProxy($proxy['host'], $proxy['port'], 'http://', $proxy['username'], $proxy['password'])
            ->withResponseHeaders()
            ->returnResponseObject()
            ->get();

        $content = $response->content;
        $headers = $response->headers;

        $html = HTMLDomParser::str_get_html($content);
        if (gettype($html) == 'boolean') {
            return;
        }

        $items = $html->find('div.tab-pane:not(#congphap):not(#khaigiap):not(#binhkhi):not(#trangsuc) div[id^=row_]');
        if ($nopAll) {
            $items = $html->find('div.tab-pane:not(#khaigiap):not(#binhkhi):not(#trangsuc) div[id^=row_]');
        }


        $excepts = ['68747', '68748', '68749', '68750', '57427', '4', '5', '12', '1', '6', '34609', '7', '57', '8926', '40450', '6852', '40664', '67', '40662', '12276', '40918', '6851', '59136', '49', '41250', '50335', '32226', '55', '50336', '44352', '4066', '40665', '40661'];
        foreach ($items as $item) {
            $itemInfo = $item->find('.form-inline', 0)->innertext;
            $itemId = substr($itemInfo, strpos($itemInfo, "txtBac"));
            $itemId = substr($itemId, 6, strpos($itemId, '"') - 6);
            $amount = $item->find('span[id^="shopnum"]')[0]->plaintext;
            if (in_array($itemId, $excepts)) {
                continue;
            }

            $this->nopKho($account, $itemId, $amount);
            sleep(2);
        }
    }

    public function nopKho(Account $account, int $itemId = 0, int $amount = 0)
    {
        $proxies = config('dp_proxies.list');
        $proxy = Arr::random($proxies);
        $res = Curl::to('https://tutien.net/account/vat_pham/')
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
            //->withProxy($proxy['host'], $proxy['port'], 'http://', $proxy['username'], $proxy['password'])
            ->withData([
                'btnDongGop' => 1,
                'shop' => $itemId,
                'txtNumber' => $amount,
            ])
            ->withResponseHeaders()
            ->returnResponseObject()
            ->post();
        debug($itemId . $res->content);
    }

    public function sendMassage($message)
    {
        $botToken = "6900326197:AAHkX6a6K5fu3FlnN9QYRcOnaJvfRhM9NuE";
        $chatID = "6127981850";

        $apiEndpoint = "https://api.telegram.org/bot{$botToken}/sendMessage";

        $params = [
            'chat_id' => $chatID,
            'text' => $message
        ];

        $queryString = http_build_query($params);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiEndpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $queryString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);
    }

    public function nopCongPhap(Account $account)
    {
        $proxies = config('dp_proxies.list');
        $proxy = Arr::random($proxies);
        $response = Curl::to('https://tutien.net/account/vat_pham/')
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
            //->withProxy($proxy['host'], $proxy['port'], 'http://', $proxy['username'], $proxy['password'])
            ->withResponseHeaders()
            ->returnResponseObject()
            ->get();

        $content = $response->content;
        $headers = $response->headers;

        $html = HTMLDomParser::str_get_html($content);
        if (gettype($html) == 'boolean') {
            debug("Cookie error: " . $account->account_name . " (" . $account->account_id . ")");
            return;
        }

        $congphap = $html->find('#congphap span[id^="shopnum"]');
        foreach ($congphap as $cp) {
            $id = str_replace('shopnum', '', $cp->id);
            $amount = (int) $cp->innertext;
            if ($id == '16') {
                continue;
            }

            Redis::set($account->account_id . '_cp_' . $id, $amount);
            $this->vaoHanhTrang($account, $id, $amount);
        }

        // $nguyenLieus = config('dao_khoang');
        // foreach ($nguyenLieus as $tcvItemId => $item)
        // {
        //     $tcvItem = $html->find('#row_' . $tcvItemId);
        //     if ($tcvItem->length) {
        //         $amount = (int) $tcvItem->find('#shopnum' . $tcvItemId)[0]->innertext;
        //         NopKho::dispatch($account, $tcvItemId, $amount)->onQueue('accounts');
        //     }
        // }
    }

    public function vaoHanhTrang(Account $account, $itemId, $amount = 1)
    {
        $proxies = config('dp_proxies.list');
        $proxy = Arr::random($proxies);
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
            //->withProxy($proxy['host'], $proxy['port'], 'http://', $proxy['username'], $proxy['password'])
            ->withData([
                'btnHanhTrang' => 1,
                'shop' => $itemId,
                'txtNumber2' => $amount,
            ])
            ->withResponseHeaders()
            ->returnResponseObject()
            ->post();
    }

    public function rutHanhTrang(Account $account, $itemId, $amount)
    {
        $proxies = config('dp_proxies.list');
        $proxy = Arr::random($proxies);
        Curl::to('https://tutien.net/account/tu_luyen/nhan_vat')
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
            ->withHeader('referer: https://tutien.net/account/tu_luyen/nhan_vat')
            ->withHeader($account->cookie)
            //->withProxy($proxy['host'], $proxy['port'], 'http://', $proxy['username'], $proxy['password'])
            ->withData([
                'btnBoRuong' => 1,
                'vatpham_id' => $itemId,
                'amount' => $amount,
            ])
            ->withResponseHeaders()
            ->returnResponseObject()
            ->post();
    }

    public function Move(Account $account, $bangPhai)
    {
        $proxies = config('dp_proxies.list');
        $proxy = Arr::random($proxies);
        $res = Curl::to('https://tutien.net/account/bang_phai/')
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
            ->withHeader('referer: https://tutien.net/account/bang_phai/')
            ->withHeader($account->cookie)
            //->withProxy($proxy['host'], $proxy['port'], 'http://', $proxy['username'], $proxy['password'])
            ->withData([
                'btnXinVaoBang' => 1,
                'txtBang' => $bangPhai,
            ])
            ->withResponseHeaders()
            ->returnResponseObject()
            ->post();
        if ($res->content == 1) {
            debug('[Done]Đổi bang thành công');
        } else {
            debug($res->content);
        }
        return $res->content;
    }

    public function taoNhanVat(Account $account)
    {
        $proxies = config('dp_proxies.list');
        $proxy = Arr::random($proxies);
        $all = ["1.png", "10.png", "100.png", "101.png", "102.png", "103.png", "104.png", "105.png", "106.png", "107.png", "108.png", "109.png", "11.png", "110.png", "111.png", "112.png", "113.png", "114.png", "115.png", "116.png", "117.png", "118.png", "119.png", "12.png", "120.png", "121.png", "122.png", "123.png", "124.png", "125.png", "126.png", "127.png", "128.png", "129.png", "13.png", "130.png", "131.png", "132.png", "133.png", "134.png", "135.png", "136.png", "137.png", "138.png", "139.png", "14.png", "140.png", "141.png", "142.png", "143.png", "144.png", "145.png", "146.png", "147.png", "148.png", "149.png", "15.png", "150.png", "151.png", "152.png", "153.png", "154.png", "155.png", "156.png", "157.png", "158.png", "159.png", "16.png", "160.png", "161.png", "162.png", "163.png", "164.png", "165.png", "166.png", "167.png", "168.png", "169.png", "17.png", "170.png", "171.png", "172.png", "173.png", "174.png", "175.png", "176.png", "177.png", "178.png", "179.png", "18.png", "180.png", "181.png", "182.png", "183.png", "184.png", "185.png", "186.png", "187.png", "188.png", "189.png", "19.png", "190.png", "191.png", "192.png", "193.png", "194.png", "195.png", "196.png", "197.png", "198.png", "199.png", "2.png", "20.png", "200.png", "201.png", "202.png", "203.png", "204.png", "205.png", "206.png", "207.png", "208.png", "209.png", "21.png", "210.png", "211.png", "212.png", "213.png", "214.png", "215.png", "216.png", "217.png", "218.png", "219.png", "22.png", "220.png", "221.png", "222.png", "223.png", "224.png", "23.png", "24.png", "25.png", "26.png", "27.png", "28.png", "29.png", "3.png", "30.png", "31.png", "32.png", "33.png", "34.png", "35.png", "36.png", "37.png", "38.png", "39.png", "4.png", "40.png", "41.png", "42.png", "43.png", "44.png", "45.png", "46.png", "47.png", "48.png", "49.png", "5.png", "50.png", "51.png", "52.png", "53.png", "54.png", "55.png", "56.png", "57.png", "58.png", "59.png", "6.png", "60.png", "61.png", "62.png", "63.png", "64.png", "65.png", "66.png", "67.png", "68.png", "69.png", "7.png", "70.png", "71.png", "72.png", "73.png", "74.png", "75.png", "76.png", "77.png", "78.png", "79.png", "8.png", "80.png", "81.png", "82.png", "83.png", "84.png", "85.png", "86.png", "87.png", "88.png", "89.png", "9.png", "90.png", "91.png", "92.png", "93.png", "94.png", "95.png", "96.png", "97.png", "98.png", "99.png"];
        Curl::to('https://tutien.net/account/tu_luyen/nhan_vat')
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
            ->withHeader('referer: https://tutien.net/account/tu_luyen/nhan_vat')
            ->withHeader($account->cookie)
            //->withProxy($proxy['host'], $proxy['port'], 'http://', $proxy['username'], $proxy['password'])
            ->withData([
                'btnCreate' => 1,
                'radHe' => 2,
                'radNhanVat' => Arr::random($all),
            ])
            ->withResponseHeaders()
            ->returnResponseObject()
            ->post();
    }

    // public function nopKho(Account $account, int $itemId = 0, int $amount = 0)
    // {
    //     $nguyenLieus = config('dao_khoang');
    //     debug($account->account_id . " - " . $nguyenLieus[$itemId] . '(' . $amount . ')');
    //     Curl::to('https://tutien.net/account/vat_pham/')
    //         ->withHeader('authority: tutien.net')
    //         ->withHeader('accept: */*')
    //         ->withHeader('sec-fetch-dest: empty')
    //         ->withHeader('x-requested-with: XMLHttpRequest')
    //         ->withHeader('user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36')
    //         ->withHeader('dnt: 1')
    //         ->withHeader('content-type: application/x-www-form-urlencoded; charset=UTF-8')
    //         ->withHeader('origin: https://tutien.net')
    //         ->withHeader('sec-fetch-site: same-origin')
    //         ->withHeader('sec-fetch-mode: cors')
    //         ->withHeader('referer: https://tutien.net/account/vat_pham/')
    //         ->withHeader($account->cookie)
    //         ->withData([
    //             'btnDongGop' => 1,
    //             'shop' => $itemId,
    //             'txtNumber' => $amount,
    //         ])
    //         ->withResponseHeaders()
    //         ->returnResponseObject()
    //         ->post();
    // }

    public function checkBac(Account $account)
    {
        $res = Curl::to('http://api.mottruyen.com/member?user_id=' . $account->account_id)
            ->returnResponseObject()
            ->get();
        $content = json_decode($res->content, true);
        $bac = Arr::get($content, 'data.BAC', 0);
        $account->update(['tai_san' => $bac]);
    }

    public function nopBac(Account $account, $amount = 0)
    {
        if ($amount == 0) {
            $taiSan = (int) $account->tai_san;
            $amount = (int) ($taiSan * 0.98);
        }

        $bankId = '618888';
        $proxies = config('dp_proxies.list');
        $proxy = Arr::random($proxies);
        $response = Curl::to("https://tutien.net/index.php")
            ->withHeader('authority: https://tutien.net')
            ->withHeader('accept: */*')
            ->withHeader('sec-fetch-dest: empty')
            ->withHeader('x-requested-with: XMLHttpRequest')
            ->withHeader('user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.100 Safari/537.36')
            ->withHeader('dnt: 1')
            ->withHeader('content-type: application/x-www-form-urlencoded; charset=UTF-8')
            ->withHeader('origin: https://tutien.net')
            ->withHeader('sec-fetch-site: same-origin')
            ->withHeader('sec-fetch-mode: cors')
            ->withHeader('referer: https://tutien.net/member/' . $bankId)
            ->withHeader('accept-language: vi-VN,vi;q=0.9,en-US;q=0.8,en;q=0.7,de;q=0.6,ja;q=0.5')
            ->withHeader($account->cookie)
            //->withProxy($proxy['host'], $proxy['port'], 'http://', $proxy['username'], $proxy['password'])
            ->withData([
                'btntangNganLuong' => 1,
                'txtMoney' => $amount,
                'member' => $bankId,
            ])
            ->returnResponseObject()
            ->post();
        if ($response->content == 1) {
            debug("Nộp " . $amount . " bạc vào khố");
        } else {
            debug($response->content);
            $this->nopBac($account, $amount);
        }
        $this->checkBac($account);
    }

    public function dotPha(Account $account, $buff = [])
    {
        \Log::channel('progress_log')->info('Auto dp for: ' . $account->account_name);
        $this->checkRequireItems($account);
        sleep(2);

        $buff['btnDotPha'] = 1;
        $proxies = config('dp_proxies.list');
        $proxy = Arr::random($proxies);
        $response = Curl::to('https://tutien.net/account/tu_luyen/dot_pha')
            ->withHeader('authority: truyencv.com')
            ->withHeader('accept: */*')
            ->withHeader('sec-fetch-dest: empty')
            ->withHeader('x-requested-with: XMLHttpRequest')
            ->withHeader('user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.3990.0 Safari/537.36')
            ->withHeader('content-type: application/x-www-form-urlencoded; charset=UTF-8')
            ->withHeader('origin: https://tutien.net')
            ->withHeader('sec-fetch-site: same-origin')
            ->withHeader('sec-fetch-mode: cors')
            ->withHeader('referer: https://tutien.net/account/tu_luyen/dot_pha')
            ->withHeader('accept-language: en-US,en;q=0.9')
            ->withHeader($account->cookie)
            //->withProxy($proxy['host'], $proxy['port'], 'http://', $proxy['username'], $proxy['password'])
            ->withData($buff)
            ->withResponseHeaders()
            ->returnResponseObject()
            ->post();

        $headers = $response->headers;
        // Update cookie
        if (!empty($headers['Set-Cookie']) || !empty($headers['set-cookie'])) {
            $cookie = app(CookieHelper::class)->updateCookie($account->cookie, $headers);
            $newCookie = implode('; ', $cookie);
            $account->update(['cookie' => $newCookie]);
        }
        $this->getAccountInfo($account);
        //$this->checkBac($account);
    }

    public function dotPhaDongThien(Account $account, $buff = [])
    {
        $buff['btnDotPha'] = 1;
        $buff['tiledotpha'] = 0;
        $response = Curl::to('https://tutien.net/account/bang_phai/dong_thien/')
            ->withHeader('authority: truyencv.com')
            ->withHeader('accept: */*')
            ->withHeader('sec-fetch-dest: empty')
            ->withHeader('x-requested-with: XMLHttpRequest')
            ->withHeader('user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.3990.0 Safari/537.36')
            ->withHeader('content-type: application/x-www-form-urlencoded; charset=UTF-8')
            ->withHeader('origin: https://tutien.net')
            ->withHeader('sec-fetch-site: same-origin')
            ->withHeader('sec-fetch-mode: cors')
            ->withHeader('referer: https://tutien.net/account/bang_phai/dong_thien/')
            ->withHeader('accept-language: en-US,en;q=0.9')
            ->withHeader($account->cookie)
            ->withData($buff)
            ->withResponseHeaders()
            ->returnResponseObject()
            ->post();

        if ($response->content == 1) {
            \Log::channel('progress_log')->info('Đột phá thành công');
        }
    }

    private function getSpeed(Account $account, $currentProgress)
    {
        $preProgress = $account->progress;
        $preProgress = preg_replace('/.*\(/', '', $preProgress);
        $preProgress = (int) preg_replace('/\/.*\)/', '', $preProgress);

        $current = preg_replace('/.*\(/', '', $currentProgress);
        $current = (int) preg_replace('/\/.*\)/', '', $current);

        return ' (<span class="text-success">+' . ($current - $preProgress) . '</span>)';
    }

    public function checkRequireItems(Account $account)
    {
        $progress = $account->progress;
        if (strpos($progress, "Phàm Nhân") !== false) {
            $this->congCongHien($account, 500000);
            sleep(2);
            $this->chuyenDo($account, self::ID_TTD, 1);
            return;
        }

        if (
            strpos($progress, "Trúc Cơ Tầng 3") !== false ||
            strpos($progress, "Trúc Cơ Tầng 6") !== false ||
            strpos($progress, "Trúc Cơ Tầng 9") !== false
        ) {
            $this->chuyenDo($account, self::ID_TCD, 1); // tcd
            return;
        }

        if (
            strpos($progress, "Kim Đan Tầng 1") !== false ||
            strpos($progress, "Kim Đan Tầng 2") !== false ||
            strpos($progress, "Kim Đan Tầng 3") !== false ||
            strpos($progress, "Kim Đan Tầng 6") !== false ||
            strpos($progress, "Kim Đan Tầng 9") !== false
        ) {
            $this->chuyenDo($account, self::ID_BND, 1); // tcd
            return;
        }

        if (
            strpos($progress, "Nguyên Anh Tầng 3") !== false ||
            strpos($progress, "Nguyên Anh Tầng 6") !== false ||
            strpos($progress, "Nguyên Anh Tầng 9") !== false
        ) {
            $this->chuyenDo($account, self::ID_BAD, 1);
            return;
        }

        if (
            strpos($progress, "Hóa Thần Tầng 3") !== false ||
            strpos($progress, "Hóa Thần Tầng 6") !== false ||
            strpos($progress, "Hóa Thần Tầng 9") !== false
        ) {
            $this->chuyenDo($account, self::ID_LTTHP, 1);
            return;
        }

        if (
            strpos($progress, "Luyện Hư Tầng 3") !== false ||
            strpos($progress, "Luyện Hư Tầng 6") !== false ||
            strpos($progress, "Luyện Hư Tầng 9") !== false
        ) {
            $this->chuyenDo($account, self::ID_LTCP, 1);
            return;
        }

        if (strpos($progress, "Hợp Thể") !== false) {
            if (strpos($progress, "Hợp Thể Tầng 9") !== false || strpos($progress, "Hợp Thể Tầng 8") !== false || strpos($progress, "Hợp Thể Tầng 7") !== false) {
                return;
            } else
                $this->chuyenDo($account, self::ID_TLHP, 1);
            return;
        }

        if (strpos($progress, "Đại Thừa") !== false) {
            if (strpos($progress, "Đại Thừa Tầng 3") !== false || strpos($progress, "Đại Thừa Tầng 6") !== false || strpos($progress, "Đại Thừa Tầng 9") !== false) {
                $this->chuyenDo($account, 78, 1);
                sleep(2);
                $this->chuyenDo($account, self::ID_TLTP, 1);
                return;
            } else
                $this->chuyenDo($account, self::ID_TLTP, 1);
            return;
        }
    }

    public function chuyenDo(Account $account, int $shopId = 40420, int $amount = 1)
    {
        sleep(1);
        $cookie = 'PHPSESSID=imk6egj51u5nvkf96l398ihmjk; USER=A9lMWrjHg7By%3AEqt%2F9qALZ0QfZJossNAoi1Snng%2BJWAKT76lADgsHagHF; reada=6335';
        if ($account->bang_phai == 'vinh-hang-dien') {
            $cookie = 'cookie: PHPSESSID=imk6egj51u5nvkf96l398ihmjk; USER=A9lMWrjHg7By%3AEqt%2F9qALZ0QfZJossNAoi1Snng%2BJWAKT76lADgsHagHF; reada=6335';
        } elseif ($account->bang_phai == 'de-thien-mon') {
            $cookie = 'cookie: PHPSESSID=djr96c71k3uedsbmd7s251785m; USER=CyTqLOl0kewY%3Ai3%2B8ovWYTWJrKMZZolcKtNU2PTOK05pkhnb27R7qazdx; reada=4';
        } elseif ($account->bang_phai == 'vo-ta-team') {
            $cookie = 'cookie: USER=QRhPfJMd3ecw%3Avxjd6tgrL9QU%2BMSm6g18xYEezF7aouOdp1%2BuLRyaMtIL; PHPSESSID=ha63ukuhcl447upac04ouhs87b; reada=11';
        } elseif ($account->bang_phai == 'tu-la-ma-dien') {
            $cookie = 'cookie: PHPSESSID=88lo2jrbbee5ohbdkj3b3gd0ko; USER=q2auAwfEJTHL%3AQ4jNLIt0hDbgvhQyGfjffxclJe%2Bd4J0AOKg9ApwY8I0y; reada=2';
        }
        $proxies = config('dp_proxies.list');
        $proxy = Arr::random($proxies);
        $res = Curl::to('https://tutien.net/account/bang_phai/bao_kho_duong/')
            ->withHeader('authority: tutien.net')
            ->withHeader('sec-ch-ua: " Not A;Brand";v="99", "Chromium";v="90", "Google Chrome";v="90"')
            ->withHeader('accept: */*')
            ->withHeader('dnt: 1')
            ->withHeader('x-requested-with: XMLHttpRequest')
            ->withHeader('sec-ch-ua-mobile: ?0')
            ->withHeader('user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85 Safari/537.36')
            ->withHeader('content-type: application/x-www-form-urlencoded; charset=UTF-8')
            ->withHeader('origin: https://tutien.net')
            ->withHeader('sec-fetch-site: same-origin')
            ->withHeader('sec-fetch-mode: cors')
            ->withHeader('sec-fetch-dest: empty')
            ->withHeader('referer: https://tutien.net/account/bang_phai/bao_kho_duong/')
            ->withHeader('accept-language: en-US,en;q=0.9')
            ->withHeader($cookie)
            //->withProxy($proxy['host'], $proxy['port'], 'http://', $proxy['username'], $proxy['password'])
            ->withData([
                'btnChuyenVatPham' => 1,
                'shop' => $shopId,
                'txtNumber' => $amount,
                'txtMember' => $account->account_id,
            ])
            ->returnResponseObject()
            ->post();
        if ($res->content == 1) {
            debug('[Done]Chuyển ' . $this->name($shopId) . ' số lượng ' . $amount . ' cho ' . $account->account_id);
            $this->sendMassage('[Done]Chuyển ' . $this->name($shopId) . ' số lượng ' . $amount . ' cho ' . $account->account_id);
        } else {
            debug('Chuyển ' . $this->name($shopId) . ' số lượng ' . $amount . ' cho ' . $account->account_id . ' bị lỗi: ' . $res->content);
            $this->sendMassage('Chuyển ' . $this->name($shopId) . ' số lượng ' . $amount . ' cho ' . $account->account_id . ' bị lỗi: ' . $res->content);
        }
        return $res->content;
    }

    /**
     * @param Account $account
     * @param string $content
     */
    private function filterInventory(Account $account, string $content)
    {
        $html = HTMLDomParser::str_get_html($content);
        if (gettype($html) == 'boolean') {
            return;
        }

        $items = $this->filterItems($html, $account->id);
        foreach ($items as $item) {
            if (!$item['item_id']) {
                continue;
            }

            $account->inventories()->updateOrCreate(
                [
                    'item_id' => $item['item_id'],
                    'account_id' => $item['account_id'],
                ],
                $item
            );
        }
    }

    protected function filterItems(HtmlDomParser $html, $accountId): array
    {
        $items = $html->find('.block-content.block-detail-sidebar-author p');
        $result = [];
        $filter = [
            'tàng bảo đồ',
            'tàng bảo đồ cao',
            'la bàn',
            'quy giáp',
            'dạ minh châu',
            'băng hỏa ngọc',
            'túi sủng vật',
            'túi thức ăn',
            'túi phân bón',
            'thời gian chi thủy',
            'thần bí bảo rương',
            'gân gà',
            'tẩy tủy đan',
            'trúc cơ đan',
            'bổ nguyên đan',
            'bổ anh đan',
            'hóa nguyên đan',
            'hợp nguyên đan',
            'bàn đào quả',
            'bồ đề quả',
            'cá chép giấy'
        ];

        for ($i = 1; $i < $items->count(); $i++) {
            $row = $items[$i];
            $text = $row->findOne('small')->plaintext;
            $text = mb_strtolower($text, 'UTF-8');

            if (in_array($text, $filter)) {
                $amount = (int) $row->findOne('span')->plaintext;
                $itemName = vietTat($text);
                $result[] = [
                    'item_id' => convertTbd($itemName),
                    'amount' => $amount,
                    'account_id' => $accountId,
                ];
            }
        }


        $idFilter = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21];
        $resultId = Arr::pluck($result, 'item_id');
        $diff = array_filter(array_diff($idFilter, $resultId));
        foreach ($diff as $id) {
            $result[] = [
                'item_id' => $id,
                'amount' => 0,
                'account_id' => $accountId,
            ];
        }

        return $result;
    }

    public function changeMember(Account $account, $dt = 1)
    {
        $accountId = $account->account_id;
        $cookie = 'PHPSESSID=imk6egj51u5nvkf96l398ihmjk; USER=A9lMWrjHg7By%3AEqt%2F9qALZ0QfZJossNAoi1Snng%2BJWAKT76lADgsHagHF; reada=6335';
        if ($account->bang_phai == 'vinh-hang-dien') {
            $cookie = 'cookie: PHPSESSID=imk6egj51u5nvkf96l398ihmjk; USER=A9lMWrjHg7By%3AEqt%2F9qALZ0QfZJossNAoi1Snng%2BJWAKT76lADgsHagHF; reada=6335';
        } elseif ($account->bang_phai == 'de-thien-mon') {
            $cookie = 'cookie: PHPSESSID=djr96c71k3uedsbmd7s251785m; USER=CyTqLOl0kewY%3Ai3%2B8ovWYTWJrKMZZolcKtNU2PTOK05pkhnb27R7qazdx; reada=4';
        } elseif ($account->bang_phai == 'vo-ta-team') {
            $cookie = 'cookie: USER=QRhPfJMd3ecw%3Avxjd6tgrL9QU%2BMSm6g18xYEezF7aouOdp1%2BuLRyaMtIL; PHPSESSID=ha63ukuhcl447upac04ouhs87b; reada=11';
        } elseif ($account->bang_phai == 'tu-la-ma-dien') {
            $cookie = 'cookie: PHPSESSID=88lo2jrbbee5ohbdkj3b3gd0ko; USER=q2auAwfEJTHL%3AQ4jNLIt0hDbgvhQyGfjffxclJe%2Bd4J0AOKg9ApwY8I0y; reada=2';
        }

        $res = Curl::to('https://tutien.net/account/bang_phai/chap_su_duong/')
            ->withHeader('authority: tutien.net')
            ->withHeader('sec-ch-ua: " Not A;Brand";v="99", "Chromium";v="90", "Google Chrome";v="90"')
            ->withHeader('accept: */*')
            ->withHeader('dnt: 1')
            ->withHeader('x-requested-with: XMLHttpRequest')
            ->withHeader('sec-ch-ua-mobile: ?0')
            ->withHeader('user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85 Safari/537.36')
            ->withHeader('content-type: application/x-www-form-urlencoded; charset=UTF-8')
            ->withHeader('origin: https://tutien.net')
            ->withHeader('sec-fetch-site: same-origin')
            ->withHeader('sec-fetch-mode: cors')
            ->withHeader('sec-fetch-dest: empty')
            ->withHeader('referer: https://tutien.net/account/bang_phai/chap_su_duong/?txtMember=' . $accountId)
            ->withHeader('accept-language: en-US,en;q=0.9')
            ->withHeader($cookie)
            ->withData([
                'btnDoiMemberBang' => 1,
                'member_id' => $accountId,
                'txtTenMoi' => '',
                'txtCongHien' => 0,
                'selQuyenHan' => 0,
                'chkDongThien' => $dt,
            ])
            ->returnResponseObject()
            ->post();
        if ($res->content == 1) {
            debug('Cho ' . $accountId . ' vào động');
        } else {
            debug($res->content);
        }
        return $res->content;
    }

    public function congCongHien(Account $account, $dch = 100000)
    {
        $accountId = $account->account_id;
        $cookie = 'PHPSESSID=imk6egj51u5nvkf96l398ihmjk; USER=A9lMWrjHg7By%3AEqt%2F9qALZ0QfZJossNAoi1Snng%2BJWAKT76lADgsHagHF; reada=6335';
        if ($account->bang_phai == 'vinh-hang-dien') {
            $cookie = 'cookie: PHPSESSID=imk6egj51u5nvkf96l398ihmjk; USER=A9lMWrjHg7By%3AEqt%2F9qALZ0QfZJossNAoi1Snng%2BJWAKT76lADgsHagHF; reada=6335';
        } elseif ($account->bang_phai == 'de-thien-mon') {
            $cookie = 'cookie: PHPSESSID=djr96c71k3uedsbmd7s251785m; USER=CyTqLOl0kewY%3Ai3%2B8ovWYTWJrKMZZolcKtNU2PTOK05pkhnb27R7qazdx; reada=4';
        } elseif ($account->bang_phai == 'vo-ta-team') {
            $cookie = 'cookie: USER=QRhPfJMd3ecw%3Avxjd6tgrL9QU%2BMSm6g18xYEezF7aouOdp1%2BuLRyaMtIL; PHPSESSID=ha63ukuhcl447upac04ouhs87b; reada=11';
        } elseif ($account->bang_phai == 'tu-la-ma-dien') {
            $cookie = 'cookie: PHPSESSID=88lo2jrbbee5ohbdkj3b3gd0ko; USER=q2auAwfEJTHL%3AQ4jNLIt0hDbgvhQyGfjffxclJe%2Bd4J0AOKg9ApwY8I0y; reada=2';
        }

        $res = Curl::to('https://tutien.net/account/bang_phai/chap_su_duong/')
            ->withHeader('authority: tutien.net')
            ->withHeader('sec-ch-ua: " Not A;Brand";v="99", "Chromium";v="90", "Google Chrome";v="90"')
            ->withHeader('accept: */*')
            ->withHeader('dnt: 1')
            ->withHeader('x-requested-with: XMLHttpRequest')
            ->withHeader('sec-ch-ua-mobile: ?0')
            ->withHeader('user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85 Safari/537.36')
            ->withHeader('content-type: application/x-www-form-urlencoded; charset=UTF-8')
            ->withHeader('origin: https://tutien.net')
            ->withHeader('sec-fetch-site: same-origin')
            ->withHeader('sec-fetch-mode: cors')
            ->withHeader('sec-fetch-dest: empty')
            ->withHeader('referer: https://tutien.net/account/bang_phai/chap_su_duong/?txtMember=' . $accountId)
            ->withHeader('accept-language: en-US,en;q=0.9')
            ->withHeader($cookie)
            ->withData([
                'btnDoiMemberBang' => 1,
                'member_id' => $accountId,
                'txtTenMoi' => '',
                'txtCongHien' => $dch,
                'selQuyenHan' => 0,
                'chkDongThien' => 0,
            ])
            ->returnResponseObject()
            ->post();
    }

    public function suDung(Account $account, $shop)
    {
        $res = Curl::to('https://tutien.net/account/vat_pham/')
            ->withHeader('authority: tutien.net')
            ->withHeader('accept: */*')
            ->withHeader('accept-language: en-US,en;q=0.9,vi;q=0.8')
            ->withHeader('content-type: application/x-www-form-urlencoded; charset=UTF-8')
            ->withHeader($account->cookie)
            ->withHeader('origin: https://tutien.net')
            ->withHeader('referer: https://tutien.net/account/vat_pham/')
            ->withHeader('sec-ch-ua: "Google Chrome";v="107", "Chromium";v="107", "Not=A?Brand";v="24"')
            ->withHeader('sec-ch-ua-mobile: ?0')
            ->withHeader('sec-ch-ua-platform: "Linux"')
            ->withHeader('sec-fetch-dest: empty')
            ->withHeader('sec-fetch-mode: cors')
            ->withHeader('sec-fetch-site: same-origin')
            ->withHeader('user-agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/107.0.0.0 Safari/537.36')
            ->withHeader('x-requested-with: XMLHttpRequest')
            ->withData([
                'btnTangExpBac' => 1,
                'items' => $shop,
                'txtSoLuong' => 1,
            ])
            ->returnResponseObject()
            ->post();
        $responseData = json_decode($res->content, true);
        $message = isset($responseData['data']) ? $responseData['data'] : 'Không có dữ liệu';
        $this->getAccountInfo($account, $account->cookie);
        debug($message);
        return $message;
    }

    public function moRuong(Account $account)
    {
        $res = Curl::to('https://tutien.net/account/vat_pham/')
            ->withHeader('authority: tutien.net')
            ->withHeader('accept: */*')
            ->withHeader('accept-language: en-US,en;q=0.9,vi;q=0.8')
            ->withHeader('content-type: application/x-www-form-urlencoded; charset=UTF-8')
            ->withHeader($account->cookie)
            ->withHeader('origin: https://tutien.net')
            ->withHeader('referer: https://tutien.net/account/vat_pham/')
            ->withHeader('sec-ch-ua: "Google Chrome";v="107", "Chromium";v="107", "Not=A?Brand";v="24"')
            ->withHeader('sec-ch-ua-mobile: ?0')
            ->withHeader('sec-ch-ua-platform: "Linux"')
            ->withHeader('sec-fetch-dest: empty')
            ->withHeader('sec-fetch-mode: cors')
            ->withHeader('sec-fetch-site: same-origin')
            ->withHeader('user-agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/107.0.0.0 Safari/537.36')
            ->withHeader('x-requested-with: XMLHttpRequest')
            ->withData([
                'btnTangExpBac' => 1,
                'items' => 7905,
                'txtSoLuong' => 1,
            ])
            ->returnResponseObject()
            ->post();

        return json_decode($res->content, true);
    }

    public function openEventItem(Account $account)
    {
        $res = Curl::to('https://tutien.net/account/vat_pham/')
            ->withHeader('authority: tutien.net')
            ->withHeader('accept: */*')
            ->withHeader('accept-language: en-US,en;q=0.9,vi;q=0.8')
            ->withHeader('content-type: application/x-www-form-urlencoded; charset=UTF-8')
            ->withHeader($account->cookie)
            ->withHeader('origin: https://tutien.net')
            ->withHeader('referer: https://tutien.net/account/vat_pham/')
            ->withHeader('sec-ch-ua: "Google Chrome";v="107", "Chromium";v="107", "Not=A?Brand";v="24"')
            ->withHeader('sec-ch-ua-mobile: ?0')
            ->withHeader('sec-ch-ua-platform: "Linux"')
            ->withHeader('sec-fetch-dest: empty')
            ->withHeader('sec-fetch-mode: cors')
            ->withHeader('sec-fetch-site: same-origin')
            ->withHeader('user-agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/107.0.0.0 Safari/537.36')
            ->withHeader('x-requested-with: XMLHttpRequest')
            ->withData([
                'btnTangExpBac' => 1,
                'items' => 55813,
                'txtSoLuong' => 1,
            ])
            ->returnResponseObject()
            ->post();

        return json_decode($res->content, true);
    }

    public   function name($number)
    {
        $name = '';
        switch ($number) {
            case 8:
                $name = 'Hoàng Kim Lệnh';
                break;
            case 10:
                $name = 'Huyết Khí Đan';
                break;
            case 11:
                $name = 'Đê Giai Thuẫn';
                break;
            case 17:
                $name = 'Tị Lôi Châu';
                break;
            case 34:
                $name = 'Thanh Tâm Đan';
                break;
            case 70:
                $name = 'Hộ Linh Trận';
                break;
            case 10152:
                $name = 'Hỏa Ngọc Châu';
                break;
            case 10153:
                $name = 'Thải Ngọc Châu';
                break;
            case 10154:
                $name = 'Sa Ngọc Châu';
                break;
            case 10155:
                $name = 'Tán Lôi Trận';
                break;
            case 9:
                $name = 'Tẩy Tủy Đan';
                break;
            case 13:
                $name = 'Trúc Cơ Đan';
                break;
            case 14:
                $name = 'Bổ Nguyên Đan';
                break;
            case 40:
                $name = 'Bổ Anh Đan';
                break;
            case 62:
                $name = 'Hóa Nguyên Đan';
                break;
            case 77:
                $name = 'Luyện Thần Đan';
                break;
            case 603:
                $name = 'Hợp Nguyên Đan';
                break;
            case 605:
                $name = 'Đại Linh Đan';
                break;
            case 61:
                $name = 'Linh Thạch THP';
                break;
            case 76:
                $name = 'Linh Thạch CP';
                break;
            case 599:
                $name = 'Tinh Linh HP';
                break;
            case 600:
                $name = 'Tinh Linh TP';
                break;
            case 601:
                $name = 'Tinh Linh THP';
                break;
            case 602:
                $name = 'Tinh Linh CP';
                break;
            case 8007:
                $name = 'Độ Hư Đan';
                break;
            case 32004:
                $name = 'Bàn Đào Quả';
                break;
            case 35680:
                $name = 'Bồ Đề Quả';
                break;
            case 40912:
                $name = 'Ngô Đồng Quả';
                break;
            case 30482:
                $name = 'Tử Tinh HP';
                break;
            case 30483:
                $name = 'Tử Tinh TP';
                break;
            case 78:
                $name = 'Nhân Sâm Vạn Năm';
                break;
            case 30503:
                $name = 'Ngọc Tuyết Linh Sâm';
                break;
            case 22:
                $name = 'Uẩn Thiên Đan';
                break;
            case 36:
                $name = 'Phá Thiên Đan';
                break;
            case 60:
                $name = 'Cố Thần Đan';
                break;
            case 75:
                $name = 'Ngưng Thần Đan';
                break;
            case 218:
                $name = 'Dung Thần Đan';
                break;
            case 606:
                $name = 'Đại Phá Đan';
                break;
        }
        return $name;
    }
}

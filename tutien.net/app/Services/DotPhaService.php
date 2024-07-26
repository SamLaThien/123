<?php

namespace App\Services;


use App\Account;
use App\AccountLog;
use App\CookieHelper;
use App\Jobs\NopDo;
use App\Jobs\NopKho;
use App\Jobs\DotPha;
use App\Jobs\UpdateAccount;
use App\Events\CanDanMessage;
use App\Services\AccountService;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Redis;

use Carbon\Carbon;
use voku\helper\HtmlDomParser;
use Ixudra\Curl\Facades\Curl;

class DotPhaService
{
    const TTD_EXP = 150;
    const TCD_EXP = 200;
    const BND_EXP = 300;
    const BAD_EXP = 600;
    const HND_EXP = 1200;
    const LTD_EXP = 2400;
    const DLD_EXP = 9600;
    const DHD_EXP = 19200;
    const LTCP_EXP = 30000;
    const HOP_NGUYEN_DAN_EXP = 4800;
    const TLHP_EXP = 50000;
    const TLTP_EXP = 80000;
    const TLTHP_EXP = 120000;
    const TLCP_EXP = 170000;
    const BDQ_EXP = 38400;
    const BĐQ_EXP = 76800;
    const NDQ_EXP = 153600;
    const TU_HP_EXP = 230000;
    const TU_TP_EXP = 300000;

    const ID_TTD = 9;
    const ID_TCD = 13;
    const ID_BND = 14;
    const ID_HND = 62;
    const ID_BAD = 40;
    const ID_PTD = 36;
    const ID_UTD = 22;
    const ID_LTCP = 76;
    const ID_LTD = 77;
    const ID_TLHP = 599;
    const ID_LTTHP = 61;
    const ID_CTD = 60;

    const ID_HOP_NGUYEN_DAN = 603;
    const ID_TL_TP = 600;
    const ID_TL_THP = 601;
    const ID_TL_CP = 602;
    const ID_DLD = 605;
    const ID_DHD = 8007;
    const ID_TUHP = 30482;
    const ID_TUTP = 30483;
    const ID_BANDAO = 32004;
    const ID_BODE = 35680;
    const ID_NGODONG = 40912;

    const ID_HKD = 10;
    const ID_DGT = 11;
    const ID_TLC = 17;
    const ID_THANH = 34;

    const ID_NTD = 75;

    const ID_NDC6 = 136;
    const ID_DTD = 218;

    public $accountService;

    public function __construct(AccountService $service)
    {
        $this->accountService = $service;
    }

    public function canDan(Account $account, $danDuoc = [],  $isDt = false, $buff = [])
    {
        $proxies = config('dp_proxies.list');
        $proxy = Arr::random($proxies);
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

        $cap = $html->find('.text-muted');
        $capDo = str_replace('<p class="text-muted">Cảnh giới hiện tại: <strong><span class="cap-', "", $cap[0]);
        $capDo = explode('">', $capDo)[0];

        $vatphambatbuoc = str_replace('<p class="text-muted">- Vật phẩm bắt buộc: <strong class="text-danger">', "", $cap[4]);
        $vatphambatbuoc = preg_replace('/\//', '', $vatphambatbuoc);
        $vatphambatbuoc = preg_replace("/<sup>/", '', $vatphambatbuoc);
        $vatphambatbuoc = explode('<strong><p>', $vatphambatbuoc)[0];

        $info = $html->find('.progress-bar');
        $infoText = $html->find('#content strong > span');
        if (count($info) === 0) {
            // debug($account->toArray());
            return;
        }

        $progressText = count($infoText) ? $infoText[0]->plaintext : '';
        $progressNumber = $info[0]->plaintext;

        $progressNumber = str_replace(")", "", $progressNumber);
        $progressNumber = explode("(", $progressNumber)[1];
        $progresses = explode("/", $progressNumber);

        $maxExp = intval($progresses[1]);
        $currentExp = intval($progresses[0]);
        $missingExp = $maxExp - $currentExp;

        CanDanMessage::dispatch($account, 'Cắn Đan ' . $currentExp . '/' . $maxExp . ' Thiếu ' . $missingExp);
        if ($missingExp > 0) {
            $res = $this->chuyenDanDuoc($account, $danDuoc, $missingExp, $capDo);
            if ($res == 1) {
                sleep(2);
                $this->chuyenBuff($account, $buff);
                sleep(2);
                if ((strpos($vatphambatbuoc, "Không có vật phẩm bắt buộc") !== false)) {
                    CanDanMessage::dispatch($account, "Không cần vp bắt buộc!!!");
                } else {
                    $this->prepareDotPha($account, $vatphambatbuoc);
                }
                sleep(2);
                $this->dotPha($account, $buff, $isDt);
            } else {
                if ((strpos($res, "Số lượng vật phẩm không đủ để chuyển") !== false || strpos($res, "Chưa đăng nhập bảo khố") !== false || strpos($res, "Bảo khố không có vật phẩm này hoặc đã sử dụng hết") !== false)) {
                    return $res;
                } else if ($res == '') {
                    CanDanMessage::dispatch($account, "Lỗi sever, kết thúc lần sử dụng này");
                    return false;
                }
            }
        } else {
            if ((strpos($vatphambatbuoc, "Không có vật phẩm bắt buộc") !== false)) {
                CanDanMessage::dispatch($account, "Không cần vp bắt buộc!!!");
            } else {
                $this->prepareDotPha($account, $vatphambatbuoc);
            }
            $this->chuyenBuff($account, $buff);
            sleep(3);
            $this->dotPha($account, $buff, $isDt);
        }
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
        }
        return $name;
    }

    public   function getId($name)
    {
        $ID_Item = 0;
        switch ($name) {
            case 'Tẩy Tủy Đan':
                $ID_Item = 9;
                break;
            case 'Trúc Cơ Đan':
                $ID_Item = 13;
                break;
            case 'Bổ Nguyên Đan':
                $ID_Item = 14;
                break;
            case 'Bổ Anh Đan':
                $ID_Item = 40;
                break;
            case 'Uẩn Thiên Đan':
                $ID_Item = 22;
                break;
            case 'Phá Thiên Đan':
                $ID_Item = 36;
                break;
            case 'Ngưng Thần Đan':
                $ID_Item = 75;
                break;
            case 'Dung Thần Đan':
                $ID_Item = 218;
                break;
            case 'Đại Phá Đan':
                $ID_Item = 606;
                break;
            case 'Kiếp Tiên Đan':
                $ID_Item = 8008;
                break;
            case 'Nội Đan C6':
                $ID_Item = 136;
                break;
            case 'Nội Đan C8':
                $ID_Item = 8494;
                break;
            case 'Linh Thạch THP':
                $ID_Item = 61;
                break;
            case 'Linh Thạch CP':
                $ID_Item = 76;
                break;
            case 'Tinh Linh HP':
                $ID_Item = 599;
                break;
            case 'Tinh Linh TP':
                $ID_Item = 600;
                break;
            case 'Tinh Linh THP':
                $ID_Item = 601;
                break;
            case 'Tinh Linh CP':
                $ID_Item = 602;
                break;
            case 'Tử Tinh HP':
                $ID_Item = 30482;
                break;
            case 'Tử Tinh TP':
                $ID_Item = 30483;
                break;
            case 'Nhân Sâm Vạn Năm':
                $ID_Item = 78;
                break;
        }
        return $ID_Item;
    }
    public function chuyenBuff($account, $buff)
    {
        $phuTro = $buff['vatphamphutro'];
        if ($phuTro[0] == 0) {
            return;
        }
        for ($i = 0; $i < count($phuTro);) {
            $res = $this->accountService->chuyenDo($account, $phuTro[$i], 1);
            if ($res == 1) {
                CanDanMessage::dispatch($account, '[Done]Chuyển ' . $account->account_id . ' ' . $this->name($phuTro[$i]));
                $i++;
            } else if ((strpos($res, "Số lượng vật phẩm không đủ để chuyển") !== false ||
                strpos($res, "Bảo khố không có vật phẩm này hoặc đã sử dụng hết") !== false ||
                strpos($res, "Chưa đăng nhập bảo khố") !== false)) {
                CanDanMessage::dispatch($account, 'Lỗi chuyển ' . $this->name($phuTro[$i]) . ': ' . $res);
                return $res;
            } else {
                CanDanMessage::dispatch($account, 'Lỗi chuyển ' . $this->name($phuTro[$i]) . ' cho ' . $account->account_id . ' đang thử lại');
                sleep(3);
            }
            sleep(3);
        }
    }

    public function chuyenDanDuoc($account, $danDuoc, $exp, $currentExp)
    {
        $danDuoc = $danDuoc['danDuoc'];
        $amount = 0;
        $danDuocId = self::ID_TTD;
        $exp_items = 0;
        $amount10 = 0;
        switch ($danDuoc[0]) {
            case 'ttd':
                $amount = (int) (ceil($exp / self::TTD_EXP));
                $danDuocId = self::ID_TTD;
                break;
            case 'tcd':
                $amount = (int) (ceil($exp / self::TCD_EXP));
                $danDuocId = self::ID_TCD;
                break;
            case 'bnd':
                $amount = (int) (ceil($exp / self::BND_EXP));
                $danDuocId = self::ID_BND;
                break;
            case 'bad':
                $amount = (int) (ceil($exp / self::BAD_EXP));
                $danDuocId = self::ID_BAD;
                break;
            case 'hnd':
                $amount = (int) (ceil($exp / self::HND_EXP));
                $danDuocId = self::ID_HND;
                break;
            case 'ltd':
                $amount = (int) (ceil($exp / self::LTD_EXP));
                $danDuocId = self::ID_LTD;
                break;
            case 'ltcp':
                $amount = 1;
                $danDuocId = self::ID_LTCP;
                break;
            case 'tlhp':
                $amount = 1;
                $danDuocId = self::ID_TLHP;
                break;
            case 'tltp':
                $amount = 1;
                $danDuocId = self::ID_TL_TP;
                break;
            case 'tlthp':
                $amount = 1;
                $danDuocId = self::ID_TL_THP;
                break;
            case 'tlcp':
                $amount = 1;
                $danDuocId = self::ID_TL_CP;
                break;
            case 'hnd2':
                $amount = (int) (ceil($exp / self::HOP_NGUYEN_DAN_EXP));
                $danDuocId = self::ID_HOP_NGUYEN_DAN;
                break;
            case 'dld':
                $amount = (int) (ceil($exp / self::DLD_EXP));
                $danDuocId = self::ID_DLD;
                break;
            case 'dhd':
                $amount = (int) (ceil($exp / self::DHD_EXP));
                $danDuocId = self::ID_DHD;
                break;
            case 'bdq':
                $amount = (int) (ceil($exp / self::BDQ_EXP));
                $danDuocId = self::ID_BANDAO;
                break;
            case 'bđq':
                $amount = (int) (ceil($exp / self::BĐQ_EXP));
                $danDuocId = self::ID_BODE;
                break;
            case 'ndq':
                $amount = (int) (ceil($exp / self::NDQ_EXP));
                $danDuocId = self::ID_NGODONG;
                break;
            case 'tuhp':
                $amount = 1;
                $danDuocId = self::ID_TUHP;
                break;
            case 'tutp':
                $amount = 1;
                $danDuocId = self::ID_TUTP;
                break;
        }

        $capBac = 0;

        if (count($danDuoc) == 2) {
            switch ($danDuoc[1]) {
                case 'ltcp':
                    $amount = 1;
                    $danDuocId = self::ID_LTCP;
                    $exp_items = self::LTCP_EXP;
                    break;
                case 'tlhp':
                    $amount = 1;
                    $danDuocId = self::ID_TLHP;
                    $exp_items = self::TLHP_EXP;
                    break;
                case 'tltp':
                    $amount = 1;
                    $danDuocId = self::ID_TL_TP;
                    $exp_items = self::TLTP_EXP;
                    break;
                case 'tlthp':
                    $amount = 1;
                    $danDuocId = self::ID_TL_THP;
                    $exp_items = self::TLTHP_EXP;
                    break;
                case 'tlcp':
                    $amount = 1;
                    $danDuocId = self::ID_TL_CP;
                    $exp_items = self::TLCP_EXP;
                    break;
                case 'tuhp':
                    $amount = 1;
                    $danDuocId = self::ID_TUHP;
                    $exp_items = self::TU_HP_EXP;
                    break;
                case 'tutp':
                    $amount = 1;
                    $danDuocId = self::ID_TUTP;
                    $exp_items = self::TU_TP_EXP;
                    break;
            }
            if ($danDuoc[1] && $exp > $exp_items) {
                $sl_item = (int) (floor($exp / $exp_items));
                sleep(1);
                $res = $this->accountService->chuyenDo($account, $danDuocId, $sl_item);
                if ($res != 1) return;
                CanDanMessage::dispatch($account, '[Cắn Đan 2 Items]: Chuyển ' . $sl_item * $exp_items . ' bằng ' . $sl_item . ' ' . $this->name($danDuocId));
                $soLuong10 = (int) ($sl_item / 10);
                $soLuong = $sl_item % 10;
                for ($i = 0; $i < $soLuong10; $i++) {
                    $data =  $this->canDanDuoc($account, $danDuocId, 10);
                    if ((strpos($data, "Không thể dùng do dược lực quá mạnh sẽ bạo thể mà chết") !== false)) {
                        return;
                    }
                    sleep(3);
                }

                for ($a = 0; $a < $soLuong; $a++) {
                    $data = $this->canDanDuoc($account, $danDuocId, 1);
                    if ((strpos($data, "Không thể dùng do dược lực quá mạnh sẽ bạo thể mà chết") !== false)) {
                        return;
                    }
                    sleep(2);
                }
                sleep(1);
                $expMissing = $exp - ($sl_item * $exp_items);
                switch ($danDuoc[0]) {
                    case 'hnd':
                        $amount = (int) (ceil($expMissing / self::HND_EXP));
                        $danDuocId = self::ID_HND;
                        break;
                    case 'ltd':
                        $amount = (int) (ceil($expMissing / self::LTD_EXP));
                        $danDuocId = self::ID_LTD;
                        break;
                    case 'hnd2':
                        $amount = (int) (ceil($expMissing / self::HOP_NGUYEN_DAN_EXP));
                        $danDuocId = self::ID_HOP_NGUYEN_DAN;
                        break;
                    case 'dld':
                        $amount = (int) (ceil($expMissing / self::DLD_EXP));
                        $danDuocId = self::ID_DLD;
                        break;
                    case 'dhd':
                        $amount = (int) (ceil($expMissing / self::DHD_EXP));
                        $danDuocId = self::ID_DHD;
                        break;
                }
            } else {
                if ($danDuoc[1] && $exp <=  $exp_items) {
                    switch ($danDuoc[0]) {
                        case 'hnd':
                            $amount = (int) (ceil($exp / self::HND_EXP));
                            $danDuocId = self::ID_HND;
                            break;
                        case 'ltd':
                            $amount = (int) (ceil($exp / self::LTD_EXP));
                            $danDuocId = self::ID_LTD;
                            break;
                        case 'hnd2':
                            $amount = (int) (ceil($exp / self::HOP_NGUYEN_DAN_EXP));
                            $danDuocId = self::ID_HOP_NGUYEN_DAN;
                            break;
                        case 'dld':
                            $amount = (int) (ceil($exp / self::DLD_EXP));
                            $danDuocId = self::ID_DLD;
                            break;
                        case 'dhd':
                            $amount = (int) (ceil($exp / self::DHD_EXP));
                            $danDuocId = self::ID_DHD;
                            break;
                    }
                }
            }
        } else
        

        if (preg_match('/ltcp|tlhp|tltp|tlthp|tlcp|tuhp|tutp/', $danDuoc[0])) {

            if ($danDuoc[0] == 'ltcp' && $exp > 30000) {
                if ($currentExp < 150000) {
                    $capBac = 1;
                } else if ($currentExp < 400000) {
                    $capBac = 0.9;
                } else if ($currentExp < 1500000) {
                    $capBac = 0.8;
                } else if ($currentExp < 5000000) {
                    $capBac = 0.7;
                } else if ($currentExp < 15000000) {
                    $capBac = 0.6;
                }

                $exp_ltcp = self::LTCP_EXP * $capBac;
                $sl_ltcp = (int) (floor($exp / $exp_ltcp));
                sleep(1);
                CanDanMessage::dispatch($account, 'Đang chuyển ' . $exp_ltcp * $sl_ltcp . ' bằng ' . $sl_ltcp . $this->name($danDuocId));
                $res = $this->accountService->chuyenDo($account, self::ID_LTCP, $sl_ltcp);
                if ($res != 1) return;
                for ($a = 0; $a < $sl_ltcp; $a++) {
                    $this->canDanDuoc($account, self::ID_LTCP, 1);
                    sleep(2);
                }

                sleep(1);
                $expMissing = $exp - ($sl_ltcp * $exp_ltcp);
                $amount = (int) (ceil($expMissing / 1200));
                $danDuocId = self::ID_HND;
            } else {
                if ($danDuoc[0] == 'ltcp' && $exp <= 30000) {
                    $amount = (int) (ceil($exp / 1200));
                    $danDuocId = self::ID_HND;
                }
            }
            // tlhp
            if ($danDuoc[0] == 'tlhp' && $exp >= 50000) {
                if ($currentExp < 400000) {
                    $capBac = 1;
                } else if ($currentExp < 1500000) {
                    $capBac = 0.9;
                } else if ($currentExp < 5000000) {
                    $capBac = 0.8;
                }
                $exp_tlhp = self::TLHP_EXP * $capBac;
                $sl_tlhp = (int) (floor($exp / $exp_tlhp));
                CanDanMessage::dispatch($account, 'Đang chuyển ' . $exp_tlhp * $sl_tlhp . ' bằng ' . $sl_tlhp . $this->name($danDuocId));
                sleep(1);
                $res = $this->accountService->chuyenDo($account, self::ID_TLHP, $sl_tlhp);
                if ($res != 1) return;
                for ($a = 0; $a < $sl_tlhp; $a++) {
                    $this->canDanDuoc($account, self::ID_TLHP, 1);
                    sleep(2);
                }
                sleep(1);
                $expMissing = $exp - ($sl_tlhp * $exp_tlhp);
                $amount = (int) (ceil($expMissing / 4800));
                $danDuocId = self::ID_HOP_NGUYEN_DAN;
            } else {
                if ($danDuoc[0] == 'tlhp' && $exp < 50000) {
                    $amount = (int) (ceil($exp / 4800));
                    $danDuocId = self::ID_HOP_NGUYEN_DAN;
                }
            }
            //tltp
            if ($danDuoc[0] == 'tltp' && $exp >= 80000) {
                if ($currentExp < 1500000) {
                    $capBac = 1;
                } else if ($currentExp < 5000000) {
                    $capBac = 0.9;
                } else if ($currentExp < 15000000) {
                    $capBac = 0.8;
                }
                $exp_tltp = self::TLTP_EXP * $capBac;
                $sl_tltp = (int) (floor($exp / $exp_tltp));
                CanDanMessage::dispatch($account, 'Đang chuyển ' . $exp_tltp * $sl_tltp . ' bằng ' . $this->name($danDuocId));
                sleep(1);
                $res = $this->accountService->chuyenDo($account, self::ID_TL_TP, $sl_tltp);
                if ($res != 1) return;
                for ($a = 0; $a < $sl_tltp; $a++) {
                    $this->canDanDuoc($account, self::ID_TL_TP, 1);
                    sleep(2);
                }
                sleep(1);
                $expMissing = $exp - ($exp_tltp * $sl_tltp);
                $amount = (int) (ceil($expMissing / 4800));
                $danDuocId = self::ID_HOP_NGUYEN_DAN;
            } else {
                if ($danDuoc[0] == 'tltp' && $exp < 80000) {
                    $amount = (int) (ceil($exp / 4800));
                    $danDuocId = self::ID_HOP_NGUYEN_DAN;
                }
            }
            //tlthp
            if ($danDuoc[0] == 'tlthp' && $exp >= 120000) {
                if ($currentExp < 5000000) {
                    $capBac = 1;
                } else if ($currentExp < 15000000) {
                    $capBac = 0.9;
                }
                $exp_tlthp = self::TLTHP_EXP * $capBac;
                $sl_tlthp = (int) (floor($exp / $exp_tlthp));
                CanDanMessage::dispatch($account, 'Đang chuyển ' . $exp_tlthp * $sl_tlthp . ' bằng ' . $sl_tlthp . $this->name($danDuocId));
                sleep(1);
                $res = $this->accountService->chuyenDo($account, self::ID_TL_THP, $sl_tlthp);
                if ($res != 1) return;
                for ($a = 0; $a < $sl_tlthp; $a++) {
                    $this->canDanDuoc($account, self::ID_TL_THP, 1);
                    sleep(2);
                }
                sleep(1);
                $expMissing = $exp - ($exp_tlthp * $sl_tlthp);
                $amount = (int) (ceil($expMissing / 4800));
                $danDuocId = self::ID_HOP_NGUYEN_DAN;
            } else {
                if ($danDuoc[0] == 'tlthp' && $exp < 120000) {
                    $amount = (int) (ceil($exp / 4800));
                    $danDuocId = self::ID_HOP_NGUYEN_DAN;
                }
            }
            if ($danDuoc[0] == 'tlcp' && $exp >= 170000) {
                if ($currentExp < 15000000) {
                    $capBac = 1;
                }
                $exp_tlcp = self::TLCP_EXP * $capBac;
                $sl_tlcp = (int) (floor($exp / $exp_tlcp));
                CanDanMessage::dispatch($account, 'Đang chuyển ' . $exp_tlcp * $sl_tlcp . ' bằng ' . $sl_tlcp . $this->name($danDuocId));
                sleep(1);
                $res = $this->accountService->chuyenDo($account, self::ID_TL_CP, $sl_tlcp);
                if ($res != 1) return;
                sleep(1);
                $amount10 = (int) ($sl_tlcp / 10);
                $amount1 = $sl_tlcp % 10;
                for ($a = 0; $a <  $amount10; $a++) {
                    $this->canDanDuoc($account, self::ID_TL_CP, 10);
                    sleep(2);
                }
                for ($a = 0; $a <  $amount1; $a++) {
                    $this->canDanDuoc($account, self::ID_TL_CP, 1);
                    sleep(2);
                }
                $expMissing = $exp - ($exp_tlcp * $sl_tlcp);
                $amount = (int) (ceil($expMissing / self::BDQ_EXP));
                $danDuocId = self::ID_BANDAO;
                //$amount = (int) (ceil($expMissing / self::DLD_EXP));
                //$danDuocId = self::ID_DLD;
            } else {
                if ($danDuoc[0] == 'tlcp' && $exp < 170000) {
                    $amount = (int) (ceil($exp / self::BDQ_EXP));
                    $danDuocId = self::ID_BANDAO;
                    //$amount = (int) (ceil($exp / self::DLD_EXP));
                    //$danDuocId = self::ID_DLD;
                }
            }

            //tuhp
            if ($danDuoc[0] == 'tuhp' && $exp >= 230000) {
                if ($currentExp < 15000000) {
                    $capBac = 1;
                }
                $exp_tuhp = self::TU_HP_EXP * $capBac;
                $sl_tuhp = (int) (floor($exp / $exp_tuhp));
                CanDanMessage::dispatch($account, 'Đang chuyển ' . $exp_tuhp * $sl_tuhp . ' bằng ' . $sl_tuhp . $this->name($danDuocId));
                sleep(1);
                $res = $this->accountService->chuyenDo($account, self::ID_TUHP, $sl_tuhp);
                if ($res != 1) return;
                sleep(1);
                for ($a = 0; $a < $sl_tuhp; $a++) {
                    $this->canDanDuoc($account, self::ID_TUHP, 1);
                    sleep(2);
                }
                $expMissing = $exp - ($exp_tuhp * $sl_tuhp);
                $amount = (int) (ceil($expMissing / self::BDQ_EXP));
                $danDuocId = self::ID_BANDAO;
            } else {
                if ($danDuoc[0] == 'tuhp' && $exp < 230000) {
                    $amount = (int) (ceil($exp / self::BDQ_EXP));
                    $danDuocId = self::ID_BANDAO;
                }
            }
        }
        // chuyen phan exp con lai
        sleep(3);
        if ($amount == 0) {
            return 1;
        } else
            $res = $this->accountService->chuyenDo($account, $danDuocId, $amount);
        if ($res == 1) {
            CanDanMessage::dispatch($account, "Chuyển " . $amount . " " . $this->name($danDuocId));
            $amount10 = (int) ($amount / 10);
            $amount1 = $amount % 10;
            for ($i = 0; $i < $amount10; $i++) {
                $this->canDanDuoc($account, $danDuocId, 10);
                sleep(3);
            }

            for ($i = 0; $i < $amount1; $i++) {
                $this->canDanDuoc($account, $danDuocId, 1);
                sleep(3);
            }

            return true;
        } else {
            CanDanMessage::dispatch($account, $res);
            return $res;
        }
    }

    public function canDanDuoc($account, $danDuocId, $amount)
    {
        $proxies = config('dp_proxies.list');
        $proxy = Arr::random($proxies);

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
            //->withProxy($proxy['host'], $proxy['port'], 'http://', $proxy['username'], $proxy['password'])
            ->withData([
                'btnTangExpBac' => 1,
                'items' => $danDuocId,
                'txtSoLuong' => $amount
            ])
            ->withResponseHeaders()
            ->returnResponseObject()
            ->post();
        $content = $response->content;
        $content = json_decode($content, true);
        $message = isset($content['data']) ? $content['data'] : 'Không có dữ liệu';

        if (!empty($content['error'])) {
            $message = isset($content['error']) ? $content['error'] : 'Không có dữ liệu';
        } else {
            $message = isset($content['data']) ? $content['data'] : 'Không có dữ liệu';
        }
        CanDanMessage::dispatch($account, $message);
        return $message;
    }

    public function prepareDotPha($account, $vatphambatbuoc)
    {
        $service = app(AccountService::class);
        $progress = $account->progress;
        try {
            $vatphambatbuoc = explode(',', $vatphambatbuoc); // tách chuỗi thành mảng các phần tử
            foreach ($vatphambatbuoc as &$item) {
                $item = trim($item); // loại bỏ dấu cách thừa từ các phần tử
            }
            $vatphambatbuoc = implode(',', $vatphambatbuoc); // ghép mảng thành chuỗi
            //$vatphambatbuoc = '"' . $vatphambatbuoc . '"'; // thêm dấu ngoặc kép vào đầu và cuối chuỗi
        } catch (\Exception $e) {
        }
        $vatphambatbuoc = array('vatphambatbuoc' => explode(',', $vatphambatbuoc));
        $vpbatbuoc = $vatphambatbuoc['vatphambatbuoc'];
        for ($i = 0; $i < count($vpbatbuoc);) {
            $res = $this->accountService->chuyenDo($account, $this->getId($vpbatbuoc[$i]), 1);
            if ($res == 1) {
                CanDanMessage::dispatch($account, '[Done]Chuyển vp bắt buộc ' . $vpbatbuoc[$i]);
                $i++;
            } else if ((strpos($res, "Số lượng vật phẩm không đủ để chuyển") !== false ||
                strpos($res, "Bảo khố không có vật phẩm này hoặc đã sử dụng hết") !== false ||
                strpos($res, "Chưa đăng nhập bảo khố") !== false)) {
                CanDanMessage::dispatch($account, 'Lỗi chuyển ' . $vpbatbuoc[$i] . ': ' . $res);
                return $res;
            } else {
                CanDanMessage::dispatch($account, 'Lỗi chuyển ' . $vpbatbuoc[$i] . ' cho ' . $account->account_id . ' đang thử lại');
                sleep(3);
            }
            sleep(3);
        }
    }

    public function rutCongPhap(Account $account)
    {
        $keys = Redis::keys($account->account_id . "_cp_*");
        foreach ($keys as $key) {
            $id = str_replace($account->account_id . "_cp_", "", $key);
            $amount = Redis::get($key) || 0;
            $this->accountService->rutHanhTrang($account, $id, $amount);
        }
    }

    public function dotPha(Account $account, $buff = [], $isDt = false)
    {
        $proxies = config('dp_proxies.list');
        $proxy = Arr::random($proxies);
        $buff['btnDotPha'] = 1;
        if ($isDt) {
            $buff['tiledotpha'] = 0;
            $response = Curl::to('https://tutien.net/account/bang_phai/dong_thien')
                ->withHeader('authority: truyencv.com')
                ->withHeader('accept: */*')
                ->withHeader('sec-fetch-dest: empty')
                ->withHeader('x-requested-with: XMLHttpRequest')
                ->withHeader('user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.3990.0 Safari/537.36')
                ->withHeader('content-type: application/x-www-form-urlencoded; charset=UTF-8')
                ->withHeader('origin: https://tutien.net')
                ->withHeader('sec-fetch-site: same-origin')
                ->withHeader('sec-fetch-mode: cors')
                ->withHeader('referer: https://tutien.net/account/bang_phai/dong_thien')
                ->withHeader('accept-language: en-US,en;q=0.9')
                ->withHeader($account->cookie)
                ->withData($buff)
                //->withProxy($proxy['host'], $proxy['port'], 'http://', $proxy['username'], $proxy['password'])
                ->withResponseHeaders()
                ->returnResponseObject()
                ->post();
        } else {
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
                ->withData($buff)
                //->withProxy($proxy['host'], $proxy['port'], 'http://', $proxy['username'], $proxy['password'])
                ->withResponseHeaders()
                ->returnResponseObject()
                ->post();
        }

        $content = $response->content;
        if ($content == 1) {
            CanDanMessage::dispatch($account, 'Đột phá thành công');
        } else {
            if ($content != '') {
                CanDanMessage::dispatch($account, 'Tạch! ' . $content);
            } else {
                CanDanMessage::dispatch($account, 'Tạch! Có lỗi xảy ra!');
            }
        }

        //if (strpos($account->progress, 'Viên Mãn') !== false) {
        //    CanDanMessage::dispatch($account, 'Lấy công pháp từ hành trang');
        //    sleep(1);
        //    $this->rutCongPhap($account);
        //}
        app(AccountService::class)->getAccountInfo($account, $account->cookie);
    }
}

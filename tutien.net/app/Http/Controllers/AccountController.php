<?php

namespace App\Http\Controllers;

use Auth;
use App\Account;
use App\Inventory;
use App\Jobs\NopDo;
use App\Jobs\NopBac;
use App\Jobs\DotPha;
use App\Jobs\UpdateAccount;
use App\Jobs\CallNsd;
use Illuminate\Http\Request;
use App\Services\AccountService;
use App\Services\NsdService;
use App\Services\DotPhaService;
use Illuminate\Support\Arr;
use Redis;

class AccountController extends Controller
{
    private $accountService;
    private $nsdService;

    public function __construct(AccountService $accountService, NsdService $nsdService)
    {
        $this->accountService = $accountService;
        $this->nsdService = $nsdService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $capDo = $request->get('cap-do', 'all');
        $user = Auth::user();
        $accounts = $user->accounts()
            ->orderBy('is_nsd', 'desc')
            ->orderBy('progress', 'asc');

        if ($capDo == 'pn') {
            $accounts->where('progress', 'like', '%Phàm Nhân%');
        } elseif ($capDo == 'lk') {
            $accounts->where('progress', 'like', '%Luyện Khí%');
        } elseif ($capDo == 'tc') {
            $accounts->where('progress', 'like', '%Trúc Cơ%');
        } elseif ($capDo == 'kd') {
            $accounts->where('progress', 'like', '%Kim Đan%');
        } elseif ($capDo == 'na') {
            $accounts->where('progress', 'like', '%Nguyên Anh%');
        } elseif ($capDo == 'ht') {
            $accounts->where('progress', 'like', '%Hóa Thần%');
        } elseif ($capDo == 'lh') {
            $accounts->where('progress', 'like', '%Luyện Hư%');
        } elseif ($capDo == 'res') {
            $accounts->where('progress', 'not like', '%Phàm Nhân%')
                ->where('progress', 'not like', '%Luyện Khí%')
                ->where('progress', 'not like', '%Trúc Cơ%')
                ->where('progress', 'not like', '%Kim Đan%')
                ->where('progress', 'not like', '%Nguyên Anh%')
                ->where('progress', 'not like', '%Hóa Thần%')
                ->where('progress', 'not like', '%Luyện Hư%');
        }

        $accounts = $accounts->get();

        // $user = Auth::user();
        // $accounts = $user->accounts()
        //     ->orderBy('is_nsd', 'desc')
        //     ->orderBy('progress', 'asc')
        //     ->paginate(100);
        // ->get();

        $inventories = Inventory::with('account')
            ->where('amount', '>', 0)
            ->orderBy('amount', 'desc')
            ->get()
            ->groupBy('item_id');

        $bac = $user->accounts()->sum('tai_san');
        $cookies = Redis::get('cookie_dong');
        $cookies = json_decode($cookies) ?: [];
        return view('account.list', [
            'accounts' => $accounts,
            'taiSan' => $bac,
            'cookies' => $cookies,
            'inventories' => $inventories,
            'capDo' => $capDo
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function canDan(Request $request)
    {
        $capDo = $request->get('cap-do', 'all');
        $user = Auth::user();
        $accounts = $user->accounts()
            ->orderBy('is_nsd', 'desc')
            ->orderBy('progress', 'asc');

        if ($capDo == 'pn') {
            $accounts->where('progress', 'like', '%Phàm Nhân%');
        } elseif ($capDo == 'lk') {
            $accounts->where('progress', 'like', '%Luyện Khí%');
        } elseif ($capDo == 'tc') {
            $accounts->where('progress', 'like', '%Trúc Cơ%');
        } elseif ($capDo == 'kd') {
            $accounts->where('progress', 'like', '%Kim Đan%');
        } elseif ($capDo == 'na') {
            $accounts->where('progress', 'like', '%Nguyên Anh%');
        } elseif ($capDo == 'ht') {
            $accounts->where('progress', 'like', '%Hóa Thần%');
        } elseif ($capDo == 'lh') {
            $accounts->where('progress', 'like', '%Luyện Hư%');
        } elseif ($capDo == 'hopthe') {
            $accounts->where('progress', 'like', '%Hợp Thê%');
        } elseif ($capDo == 'dt') {
            $accounts->where('progress', 'like', '%Đại Thừa%');
        } elseif ($capDo == 'dk') {
            $accounts->where('progress', 'like', '%Độ Kiếp%');
        } elseif ($capDo == 'tien') {
            $accounts->where('progress', 'like', '%Tiên%');
        }

        $accounts = $accounts->get();
        return view('account.can-dan', ['accounts' => $accounts, 'capDo' => $capDo]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function canDanDuoc(Request $request, Account $account)
    {
        $phutro = join(',', $request->get('vatphamphutro'));
        $vatphamphutro = ['vatphamphutro' => explode(',', $phutro)];

        $danDuoc = join(',', $request->get('danDuoc'));
        $danDuoc2 = ['danDuoc' => explode(',', $danDuoc)];

        app(DotPhaService::class)->canDan(
            $account,
            $danDuoc2,
            $request->get('is_dt', 0),
            $vatphamphutro,
        );
        return response()->json('Ok!');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        if (!$request->has('account_name')) {
            $data['account_name'] = $request->get('account_id');
        }

        $data['user_id'] = Auth::user()->id;
        $data['is_nsd'] = 1;
        Account::create($data);
        return redirect()->route('accounts.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function show(Account $account)
    {
        return response()->json([
            'data' => Arr::only($account->toArray(), [
                'id',
                'account_id',
                'account_name',
                'progress',
                'tai_san',
            ])
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function edit(Account $account)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Account $account)
    {
        $account->update([
            'cookie' => $request->get('cookie'),
            'group' => $request->get('group')
        ]);
        flash('Cập nhật cookie thành công')->success()->important();
        return response()->json('done!');
    }

    public function updateNopBac(Request $request, Account $account)
    {
        flash('Cập nhật nộp bạc thành công')->success()->important();
        $account->update(['is_nopbac' => $request->get('is_nopbac') || false]);
        return \Redirect::back();
    }

    public function updateNopDo(Request $request, Account $account)
    {
        $account->update(['is_nopdo' => !$account->is_nopdo]);
        return response()->json('done!');
    }

    public function updateNsd(Request $request, Account $account)
    {
        $account->update(['is_nsd' => !$account->is_nsd]);
        return response()->json('done!');
    }

    public function updateDongThien(Request $request, Account $account)
    {
        flash('Cập nhật động thiên thành công')->success()->important();
        $account->update(['is_dt' => $request->get('is_dt') || false]);
        return \Redirect::back();
    }

    public function destroy(Account $account)
    {
        flash('Xóa account thành công')->success()->important();
        $account->delete();
        return \Redirect::back();
    }

    public function updateAllAccount()
    {
        flash('Đang cập nhật thông tin các account. Vui lòng chờ giây lát.....')->success()->important();
        $accounts = auth()->user()->accounts()->where('is_nsd', 1)->get();
        foreach ($accounts as $account) {
            UpdateAccount::dispatch($account, $account->cookie)->onQueue('accounts');
        }

        return \Redirect::back();
    }

    public function nopBacAllAccount()
    {
        flash('Đang nộp bạc từ tất cả account vào quỹ. Vui lòng chờ giây lát.....')->success()->important();
        $accounts = auth()->user()->accounts()
            ->where('tai_san', '>', 1000)
            ->get();
        foreach ($accounts as $account) {
            NopBac::dispatch($account)->onQueue('accounts');
        }

        return \Redirect::back();
    }

    public function nopDoAllAccount()
    {
        flash('Đang nộp đồ từ các account vào khố. Vui lòng chờ giây lát.....')->success()->important();
        $accounts = auth()->user()->accounts()->where('cookie', '!=', '')->get();
        foreach ($accounts as $account) {
            debug($account);
            NopDo::dispatch($account);
        }

        return \Redirect::back();
    }

    public function updateInfo(Account $account)
    {
        $this->accountService->getAccountInfo($account, '', false);
        flash('Cập nhật thông tin cho account: ' . $account->account_name . '....')->success()->important();
        return response()->json('done!');
    }

    public function nopBac(Account $account)
    {
        flash('Nộp bạc từ account: ' . $account->account_name . '....')->success()->important();
        $this->accountService->nopBac($account);
        //NopBac::dispatch($account)->onQueue('accounts');
        return response()->json('done!');
        // return \Redirect::back();
    }

    public function nopDo(Account $account)
    {
        flash('Nộp đồ từ account: ' . $account->account_name . '....')->success()->important();
        $this->accountService->checkRuong($account, false);
        return response()->json('done!');
    }

    public function nopDoAll(Account $account)
    {
        flash('Nộp đồ từ account: ' . $account->account_name . '....')->success()->important();
        $this->accountService->checkRuong($account, true);
        return response()->json('done!');
    }

    public function dotPha(Account $account)
    {
        $this->accountService->dotPha($account);
        return response()->json('DONE!');
    }

    // public function nsd()
    // {
    //     $accounts = Auth::user()
    //         ->accounts()
    //         ->whereIsNsd(1)
    //         ->orderBy('is_nsd', 'desc')
    //         ->orderBy('is_dt', 'desc')
    //         ->orderBy('progress', 'asc')
    //         ->get();
    //     return view('nsd.index', ['accounts' => $accounts]);
    // }

    public function callNsd(Account $account)
    {
        $this->nsdService->callNsd($account->account_id, $account->cookie);
        return response()->json([]);
    }

    public function import(Request $request)
    {
        $accounts = $request->get('accounts');
        $accounts = explode("\n", $accounts);
        foreach ($accounts as $account) {
            $accountAttrs = explode(' ', $account);
            Account::updateOrCreate([
                'account_id' => $accountAttrs[0],
                'user_id' => auth()->user()->id,
            ], [
                'account_id' => $accountAttrs[0],
                'account_name' => $accountAttrs[0],
                'cookie' => 'cookie: ' . trim($accountAttrs[1]),
                'user_id' => auth()->user()->id,
                'is_nsd' => 1,
            ]);
        }
        return \Redirect::back();
        #return redirect()->route('.index');
    }

    public function export()
    {
        $user = Auth::user();
        $accounts = $user->accounts()->whereNotNull('cookie')->get(['account_id', 'cookie','progress']);
        foreach ($accounts as $account) {
            debug($account);
            $cookie = $account->cookie;
            $cookieArr = explode('; ', $cookie);
            $userCookie = '';
            foreach ($cookieArr as $index => $item) {
                if (strpos($item, 'USER') !== false) {
                    $account->cookie = str_replace('cookie: ', '', $item);
                    continue;
                }
            }
        }

        return view('account.export')->with(compact('accounts'));
    }

    public function nopLaBan(Account $account, Request $request)
    {
        $amount = $request->get('amount', 1);
        $this->accountService->nopKho($account, AccountService::LA_BAN, $amount);
        return response()->json('Done!');
    }

    public function nopQuyGiap(Account $account, Request $request)
    {
        $amount = $request->get('amount', 1);
        $this->accountService->nopKho($account, AccountService::QUY_GIAP, $amount);
        return response()->json('Done!');
    }

    public function importDong(Request $request)
    {
        $cookies = $request->get('cookies', []) ?: '';
        $cookies = explode("\n", $cookies);
        $cookieDong = [];
        foreach ($cookies as $cookie) {
            if (trim($cookie)) {
                $cookieDong[] = trim($cookie);
            }
        }
        Redis::set('cookie_dong', json_encode($cookieDong));
        return \Redirect::back();
    }

    public function restartDong()
    {
        // exec("/usr/bin/pm2 restart all");
        shell_exec("/usr/bin/pm2 restart all >> /dev/null 2>&1");
        return response()->json('Done!');
    }

    public function congCongHien(Request $request, Account $account)
    {
        $dch = $request->get('dch', 100000);
        $this->accountService->congCongHien($account, $dch);
        return response()->json('Đã cộng ' . $dch . ' cho ' . $account->account_name);
    }
    public function changeMember(Request $request, Account $account)
    {
        sleep(2);
        $dt = $request->get('dt', 1);
        $res = $this->accountService->changeMember($account, $dt);
        //return response()->json('Đã  cho ' . $account->account_name . ' vào động');
        if ($res == 1) {
            return response()->json('Đã  cho ' . $account->account_name . ' vào động');
        } else
            return response()->json($res);
    }

    public function Move(Request $request, Account $account)
    {
        sleep(2);
        $bangPhai = $request->get('BangPhai');
        $this->accountService->Move($account, $bangPhai);
        return response()->json('Đã đổi bang');
    }
}

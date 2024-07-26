<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Account;
use App\Inventory;
use Auth;
use App\Services\AccountService;
use App\Services\CommandService;

class SettingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $inventories = Inventory::whereHas('account', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with('account')
            ->where('amount', '>', 0)
            ->whereIn('item_id', [1, 2])
            ->orderBy('amount', 'desc')
            ->get()
            ->groupBy('item_id');

        return view('setting.index', ['inventories' => $inventories]);
    }

    public function chuyenDo(Request $request)
    {
        $itemId = $request->get('itemId');
        $accountId = $request->get('accountId');
        $amount = $request->get('amount');
        $bangPhai = $request->get('bangPhai');

        $account = Account::whereAccountId($accountId)->first();
        $service = app(AccountService::class);
        $service->chuyenDo($account, $itemId, $amount);
        return response()->json('Done!');
    }

    public function nopDo(Request $request)
    {
        $vatPham = $request->get('item', '');
        if (!$vatPham) {
            return response()->json('Lỗi!');
        }

        $service = app(CommandService::class);
        $service->runBackgroundCommand("tutien:nop-vat-pham " . $vatPham);
        return response()->json('Done!!');
    }
}

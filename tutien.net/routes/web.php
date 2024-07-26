<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('/accounts');
});

Auth::routes(['register' => false]);

Route::middleware('auth')->group(function () {
    Route::resource('accounts', 'AccountController');

    Route::get('nsd', 'AccountController@nsd');
    Route::get('can-dan', 'AccountController@canDan');
    Route::get('update_all', 'AccountController@updateAllAccount');
    Route::get('nop_bac_all', 'AccountController@nopBacAllAccount');
    Route::get('nop_do_all', 'AccountController@nopDoAllAccount');
    Route::get('nop-do', 'SettingController@nopDo');
    Route::post('import', 'AccountController@import');
    Route::post('import-dong', 'AccountController@importDong');
    Route::get('restart-dong', 'AccountController@restartDong');
    Route::get('tbd', 'SettingController@index');
    Route::post('chuyen-do', 'SettingController@chuyenDo');
    Route::get('vqmm', 'TruyencvController@vqmm');
    Route::post('vqmm', 'TruyencvController@quay');

    Route::prefix('accounts/{account}/')->group(function () {
        Route::put('is_nopbac', 'AccountController@updateNopBac');
        Route::put('is_nsd', 'AccountController@updateNsd');
        Route::put('is_dt', 'AccountController@updateDongThien');
        Route::get('nop_bac', 'AccountController@nopBac');
        Route::get('update', 'AccountController@updateInfo');
        Route::put('nop_do', 'AccountController@updateNopDo');
        Route::get('nop_do', 'AccountController@nopDo');
        Route::get('nop_do_all', 'AccountController@nopDoAll');
        Route::get('dot_pha', 'AccountController@dotPha');
        Route::get('/', 'AccountController@show');
        Route::post('can_dan', 'AccountController@canDanDuoc');
        Route::post('cong_ch', 'AccountController@congCongHien');
        Route::post('vao_dong', 'AccountController@changeMember');
        Route::post('bang_phai', 'AccountController@Move');

        Route::get('nsd', 'AccountController@callNsd');
        Route::get('mo_ruong', 'TruyencvController@moRuong');

        Route::get('nop_la_ban/{amount}', 'AccountController@nopLaBan');
        Route::get('nop_quy_giap/{amount}', 'AccountController@nopQuyGiap');
    });
    Route::get('setting', 'SettingController@index');
    Route::get('export', 'AccountController@export');
    Route::post('import', 'AccountController@import');
    Route::post('accounts/can_dan', 'TruyencvController@canDan');
    Route::post('accounts/can_sam', 'TruyencvController@canSam');
    Route::post('accounts/can_dan_sll', 'TruyencvController@canDanSll');
    Route::get('log-viewer', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
});

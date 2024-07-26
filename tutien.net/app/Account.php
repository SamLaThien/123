<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

;

/**
 * @property string account_id
 */
class Account extends Model
{
    protected $fillable = [
        'user_id',
        'account_id',
        'account_name',
        'cookie',
        'tai_san',
        'progress',
        'progress_change',
        'is_nsd',
        'is_nopbac',
        'bang_phai',
        'is_dt',
        'group'
    ];

    protected $casts = [
        'is_nsd' => 'boolean',
        'is_nopbac' => 'boolean',
        'is_dt' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::saved(function ($account) {
            //Redis::set($account->account_id . '_cookie', $account->cookie);
        });
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }
}

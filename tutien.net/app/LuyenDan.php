<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LuyenDan extends Model
{
    protected $table = 'luyen_dan';

    protected $fillable = [
        'account_id',
        'dan_phuong_id',
        'dan_phuong_name',
        'lo_id',
        'nhu_id',
        'start_at',
        'end_at',
        'started',
    ];
}

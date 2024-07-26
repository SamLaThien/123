<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RewardLogs extends Model
{
    protected $fillable = [
        'account_id',
        'time_stop',
        'message',
    ];
}

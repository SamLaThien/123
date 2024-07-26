<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string account_id
 * @property string device_token
 * @property string user_agent
 */
class Reward extends Model
{
    //$table->increments('id');
    //$table->unsignedInteger('account_id');
    //$table->text('device_token')->default('');
    //$table->unsignedInteger('max_attempt')->default(8);
    //$table->unsignedInteger('total_attempt')->default(0);
    //$table->dateTime('last_attempt')->default(null);
    //$table->boolean('enable')->default(false);
    //$table->timestamps();

    protected $fillable = [
        'account_id',
        'device_token',
        'max_attempt',
        'total_attempt',
        'last_attempt',
        'enable',
        'user_agent'
    ];
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reading extends Model
{
    protected $fillable = [
        'account_id',
        'story_id',
        'current_chapter',
        'next_chapter',
        'delay',
        'reading'
    ];

    public function story()
    {
        return $this->belongsTo(Story::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}

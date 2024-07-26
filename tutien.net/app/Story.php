<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
    protected $fillable = [
        'story_id',
        'name',
        'first_chapter',
        'total_chapter',
        'url',
    ];
}

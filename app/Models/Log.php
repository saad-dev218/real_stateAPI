<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $fillable = [
        'action',
        'user_id',
        'loggable_type',
        'loggable_id'
    ];
}

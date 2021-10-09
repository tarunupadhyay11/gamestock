<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class FreeTradeCount extends Model
{
    protected $table = 'free_trade_counts';
    protected $fillable = [
        'user_id','sport_event_id'
    ];

}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class JoinedLeague extends Model
{
    protected $table = 'joined_leagues';
    public $timestamps = true;

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];
    protected $fillable = [
        '*'
    ];


    public static function insert($data)
    {
        $modalData = new JoinedLeague;
        $modalData->league_id = $data->league_id;
        $modalData->user_id = $data->user_id;
        $modalData->save();
    }

}

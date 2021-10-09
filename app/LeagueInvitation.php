<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class LeagueInvitation extends Model
{
    protected $table = 'league_invitations';
    protected $fillable = [
        '*'
    ];


    public static function insert($data)
    {
        $modalData = new LeagueInvitation;
        $modalData->league_id = $data->league_id;
        $modalData->mobile_number = $data->mobile_number;
        $modalData->save();
    }

}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use App\User;

class League extends Model
{
    use SoftDeletes;
    protected $table = 'leagues';
    protected $fillable = [
        '*'
    ];

    protected $dates = ['deleted_at'];
    protected $appends = ['image_url','league_duration','league_founder'];

    public function getImageUrlAttribute($value)
    {
        $url= $this->image?asset('/uploads/images/leagues/'.$this->image):asset('/uploads/images/leagues/default.png');
        return $url;
    }

    public function getLeagueDurationAttribute($value)
    {
        $date = Carbon::parse($this->duration);
        $now = Carbon::now();
        $duration_date = new Carbon($this->duration);
        $diff = $date->diffForHumans(null, true).' left';
        if($now > $date){
            return 'Expired';
        }
        else{
            return $diff;
        }
    }

    public function setDurationAttribute($value)
    {
        $start_date = date('Y-m-d');  
        $date = strtotime($start_date);
        $date = strtotime("+".$value, $date);
       // $effectiveDate = strtotime('+'.$value, strtotime(date("y-m-d")));
        return $this->attributes['duration'] = date('Y-m-d', $date);
        //return $this->attributes['duration'] = date('Y-m-d\TH:i', strtotime($value) ); 
    }

    public function getLeagueFounderAttribute($value)
    {
        $leaguefounder = User::find($this->founder);
        return $leaguefounder->first_name.' '.$leaguefounder->last_name;
       
    }
}

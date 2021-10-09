<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use App\User;

class Game extends Model
{
    protected $table = 'games';
    protected $fillable = [
        '*'
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute($value)
    {
        $url= $this->image?asset('/uploads/images/games/'.$this->image):asset('/uploads/images/games/default.png');
        return $url;
    }

}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class ApiRequest extends Model
{
    protected $table = 'api_requests';
    public $timestamps = true;

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];
    protected $fillable = [
        '*'
    ];


    public static function insert($data)
    {
        $modalData = new ApiRequest;
        $modalData->data = $data->data;
        $modalData->url = $data->url;
        $modalData->status = $data->status;
        $modalData->save();
    }

}

<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class nhlController extends Controller
{
   public function index()
    {
        $gamestocklist = [
            ["league"=>'NBA Trial',"price"=>'$234',"time-left"=>"4 days","joined"=>43,"creater"=>"kenny anderson","image"=>asset('images/default.jpg')],
            ["league"=>'Global Basketball Trial',"price"=>'$24',"time-left"=>"16 hours","joined"=>41243,"creater"=>"kenny anderson","image"=>asset('images/default.jpg')],
            ["league"=>'Global American Football Trial',"price"=>'$234',"time-left"=>"4 days","joined"=>43,"creater"=>"kenny anderson","image"=>asset('images/default.jpg')],
            ["league"=>'Global Baseball Trial',"price"=>'$129',"time-left"=>"4 days","joined"=>43,"creater"=>"kenny anderson","image"=>asset('images/default.jpg')],
            ["league"=>'Global Ice Hockey Trial',"price"=>'$234',"time-left"=>"4 days","joined"=>43,"creater"=>"kenny anderson","image"=>asset('images/default.jpg')],
            ["league"=>'MLB Trial',"price"=>'$200',"time-left"=>"1 Hour","joined"=>43,"creater"=>"kenny anderson","image"=>asset('images/default.jpg')],
            ["league"=>'NBA Trial',"price"=>'$134',"time-left"=>"1 days","joined"=>43,"creater"=>"kenny anderson","image"=>asset('images/default.jpg')],
            ["league"=>'NBA Trial',"price"=>'$340',"time-left"=>"4 days","joined"=>43,"creater"=>"kenny anderson","image"=>asset('images/default.jpg')],
            ["league"=>'NBA Trial',"price"=>'$204',"time-left"=>"3 days","joined"=>43,"creater"=>"kenny anderson","image"=>asset('images/default.jpg')],
            ["league"=>'NBA Trial',"price"=>'$245',"time-left"=>"1 day","joined"=>43,"creater"=>"kenny anderson","image"=>asset('images/default.jpg')]
            ];
       
        return response()->json(['success'=>true,'data'=>$gamestocklist], 201);
    }
    
    public function schedule(Request $request)
    {
        $url = '';
        $game = $request->game;
        switch($game){
                case 'NBA Trial':
                $url = 'http://api.sportradar.us/nba/trial/v7/en/games/2020/08/06/schedule.json?api_key=vyhvrku4jsfwbqbv2qr3tehn';
                $json = json_decode($this->file_get_contents_curld($url), true);
                break;
                
                case 'Global Basketball Trial':
                $url = 'http://api.sportradar.us/nba/trial/v7/en/games/2020/08/06/schedule.json?api_key=vyhvrku4jsfwbqbv2qr3tehn';
                $json = json_decode($this->file_get_contents_curld($url), true);
                break;
                
                default:
                  $url = 'http://api.sportradar.us/nba/trial/v7/en/games/2020/08/06/schedule.json?api_key=vyhvrku4jsfwbqbv2qr3tehn';
                  $json = json_decode($this->file_get_contents_curld($url), true);
                  break;
        }
       
        return response()->json(['success'=>true,'data'=>$json], 201);
    }
    
    public function phpinfo()
    {
    $json = phpinfo();
     
     // = ['schedule1','schedule2'];
        return response()->json($json, 201);
    }
    
    function file_get_contents_curld($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
}

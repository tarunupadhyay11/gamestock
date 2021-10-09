<?php
namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\League;
use App\LeagueInvitation;
use App\JoinedLeague;
use App\ApiRequest;
use App\FreeTradeCount;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use File;
use Carbon\Carbon;
use Auth;
use Edujugon\PushNotification\PushNotification;
use Twilio\Rest\Client;
use DB;
use DateTimeZone;
use DateTime;

class LeagueController extends Controller
{

    public function __construct()
    {
        //$this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $leagues=[];

        $model = League::latest()->whereNull('deleted_at')->get();
        if(!empty($model)){

            foreach($model as $key => $value)
            {
                if($value->league_duration!="Expired"){


                    $cunt = DB::table('joined_leagues')
                    ->where([
                        ['league_id','=',$value->id],
                        ['user_id','=',$user->id]

                        ])
                    ->count();
                    if($cunt > 0){
                        $model[$key]->is_joined="yes";
                    }else{
                        $model[$key]->is_joined="no";
                    }

                    $model[$key]->no_of_memebers = DB::table('joined_leagues')
                    ->where([
                        ['league_id','=',$value->id],


                        ])
                    ->count();
                    $orders = DB::table('orders')->where('user_id',$user->id)->where('league_id',$value->id)->get();
                    $mperv = 0;
                    $mdiff = 0;
                    $mcp = 0;
                    $mop = 0;
                    $mpv = 0;
                    $portfoliovalue = (int)$value->portfolio_value;
                    if(!$orders->isEmpty()){
                        foreach($orders as $orderm)
                        {
                            $gamemain= $this->callApi('timeline',null,$orderm->sport_event_id,null);
                            if(!empty($gamemain)){
                                $marketmain=json_decode($gamemain->markets_2_way);
                                $competitors=json_decode($gamemain->competitors);
                                if($competitors[0]->id==$orderm->competitor_id){
                                    if(isset($marketmain[0]->outcomes[0]->probability)){
                                        $mcp = (int)$mcp + ((int)round($marketmain[0]->outcomes[0]->probability) * (int)$orderm->share);
                                        $mop = (int)$mop + ((int)$orderm->share_rate * (int)$orderm->share);
                                        $ordersharerate = (int)$orderm->share_rate;
                                        $currentsharerate = (int)round($marketmain[0]->outcomes[0]->probability);
                                        if($ordersharerate!==$currentsharerate){
                                            $curratediff = (int)$mcp - (int)$mop;
                                            $portfoliovalue = (int)$portfoliovalue + $curratediff;
                                        }

                                    }
                                }
                                if($competitors[1]->id==$orderm->competitor_id){
                                    if(isset($marketmain[0]->outcomes[1]->probability)){
                                        $mcp = (int)$mcp + ((int)round($marketmain[0]->outcomes[1]->probability) * (int)$orderm->share);
                                        $mop = (int)$mop + ((int)$orderm->share_rate * (int)$orderm->share);
                                        $ordersharerate = (int)$orderm->share_rate;
                                        $currentsharerate = (int)round($marketmain[0]->outcomes[1]->probability);
                                        if($ordersharerate!==$currentsharerate){
                                            $curratediff = (int)$mcp - (int)$mop;
                                            $portfoliovalue = (int)$portfoliovalue + $curratediff;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $mdiff =  (int)$mcp - (int)$mop;
                    if($mdiff > 0){
                        $mpv = '+'. number_format((float)$mdiff, 2, '.', '');
                        if($mop>0){
                            $mperv =  (float)$mdiff / (float)$mop * 100;
                            $mperv = number_format((float)$mperv, 2, '.', '');
                        }else{
                            $mperv=0;
                        }
                    }
                    else{
                        $mpv = number_format((float)$mdiff, 2, '.', '');
                        if($mop>0){
                            $mperv =  (float)$mdiff / (float)$mop * 100;
                            $mperv = number_format((float)$mperv, 2, '.', '');
                        }else{
                            $mperv=0;
                        }
                    }
                    $portfolioamt = '$'.(int)$portfoliovalue;
                    $model[$key]->current_portfolio= $portfolioamt;
                    $leagues[]=$model[$key];
                }



            }
            $data['response'] = true;
            $data['message'] = 'League list';
            $data['data'] = $leagues;

        }else{

            $data['response'] = false;
            $data['message'] = 'Record not found';
            $data['data'] = [];

        }

        return response()->json($data, 200);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $verifyuser = $request->user();
        if($verifyuser){

        }
        $validator = validator()->make(request()->all(),[
            'name' => 'required|unique:leagues,name',
            'league_type' => 'required',
            'portfolio_value' => 'required',
            'duration' => 'required',
           // 'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'password' => 'sometimes',
        ]);



        if ($validator->fails()) {
            $data['response'] = false;
            $data['message'] = 'Error';
            $data['data'] = $validator->messages();
            return response()->json($data, 200);
        }



        $modalData = new League;
        if($request->filled('name')) $modalData->name = $request->name;
        if($request->filled('league_type')) $modalData->league_type = $request->league_type;
        if($request->filled('portfolio_value')) $modalData->portfolio_value = $request->portfolio_value;
        if($request->filled('duration')) $modalData->duration = $request->duration;

        if($request->filled('password')) {
            if (trim($request->password) != '') {
                $modalData->password = trim($request->password);
             }
        }

       
            $user = $request->user();
            $modalData->founder = $user->id;
            $modalData->save();

            $newleague = League::find($modalData->id);

            if($request->filled('mobile_number')) {
                    $request->request->add(['league_id' => $newleague->id]);
                    $request->request->add(['users_id' => $user->id]);
                    $this->invite($request);
            }


            $linvitation = new JoinedLeague;
            $linvitation->league_id = $newleague->id;
            $linvitation->user_id = $newleague->founder;
            $linvitation->save();

            $model = League::latest()->whereNull('deleted_at')->get();
            if(!empty($model)){

                foreach($model as $key => $value)
                {
                    if($value->league_duration!="Expired"){


                        $cunt = DB::table('joined_leagues')
                        ->where([
                            ['league_id','=',$value->id],
                            ['user_id','=',$user->id]

                            ])
                        ->count();
                        if($cunt > 0){
                            $model[$key]->is_joined="yes";
                        }else{
                            $model[$key]->is_joined="no";
                        }

                        $model[$key]->no_of_memebers = DB::table('joined_leagues')
                        ->where([
                            ['league_id','=',$value->id],


                            ])
                        ->count();


                        $leagues[]=$model[$key];
                    }



                }
                $data['response'] = true;
                $data['message'] = 'League list';
                $data['data'] = $leagues;

            }else{

                $data['response'] = false;
                $data['message'] = 'Record not found';
                $data['data'] = [];

            }
           // $data['response'] = true;
           // $data['message'] = 'League created successfully';
            return response()->json($data, 200);
    }
    private function callScheduleApi($sport_id,$date,$timezone,$time,$competition_id){
        $returnData=[];
        //$date="2021-01-24";
        //$sport_id="sr:sport:16";
        
        $convertdate = new DateTime($date." 00:00:01", new DateTimeZone($timezone));
        $convertdate->setTimezone(new DateTimeZone('UTC'));
        $fromdate = $convertdate->format('Y-m-d H:i:s');
        $start_date = $convertdate->format('Y-m-d');
        
        
       // $date= $convertdate->format('Y-m-d');
         
        $convertdate = new DateTime($date." 23:59:58", new DateTimeZone($timezone));
        $convertdate->setTimezone(new DateTimeZone('UTC'));
        $todate = $convertdate->format('Y-m-d H:i:s');
        $end_date = $convertdate->format('Y-m-d');
        
        
        
      
        
        $record=DB::table('trade')->where('sport_event_start_time','>',$fromdate)->where('sport_event_start_time','<=',$todate)
        ->where('sport_id',$sport_id)->where('competition_id',$competition_id)->get();
        if($record->count()>0 && (Carbon::parse(date('Y-m-d H:i:s'))->diffInSeconds($record[0]->generated_at)<60)){
             return $returnData=$record;
        }else{
            $maindata=[];
          
           $url1="https://api.sportradar.us/probabilities/production/v1/en/sports/$sport_id/schedules/$start_date/schedule.json?api_key=tdzzst7pyywvg6drwp9z6z5s";
            $maindata[] = json_decode($this->GetContents($url1));
           $url="https://api.sportradar.us/probabilities/production/v1/en/sports/$sport_id/schedules/$end_date/schedule.json?api_key=tdzzst7pyywvg6drwp9z6z5s";
            $maindata[] = json_decode($this->GetContents($url));
            $generatetime="";
            $itime=0;
       
            foreach ($maindata as $jsondata){
                if($jsondata && !empty($jsondata->sport_event_probabilities)){
                    /*if($itime=0){
                        $generatetime=date('Y-m-d H:i:s',strtotime($jsondata->generated_at));
                    }*/
                    foreach($jsondata->sport_event_probabilities as $game){
                        if(!empty($game->sport_event_status))
                        {
                            if(!empty($game->sport_event->sport_event_context->competition->id) && $game->sport_event->sport_event_context->competition->id==$competition_id){
                                
                            
                                
                                $gamedata=[];
                                $gamedata['competition_id']=$game->sport_event->sport_event_context->competition->id;
                                $gamedata['sport_id']=$sport_id;
                                date_default_timezone_set('UTC');
                                $gamedata['generated_at']=date('Y-m-d H:i:s',strtotime($jsondata->generated_at));
                                $gamedata['sport_event_id']=$game->sport_event->id;
                                $gamedata['schedule_date']=$date;
        
                                if(!empty($game->sport_event->start_time)){
                                    $gamedata['sport_event_start_time']=$game->sport_event->start_time;
                                }
                                
                                
                                
                                if(!empty($game->sport_event->sport_event_context->competition))
                                $gamedata['competition']=json_encode($game->sport_event->sport_event_context->competition);
                                
                                if(!empty($game->sport_event_status)){
                                    $gamedata['sport_event_status']=json_encode($game->sport_event_status);
                                
                                }
                                
                                $gamedata['competitors']=json_encode($game->sport_event->competitors);
                                $gamedata['season_id']=$game->sport_event->sport_event_context->season->id;
                                $markets =array_filter($game->markets, function ($var) {
                                    if($var->name=='2way'){
                                        return true;
                                    }else{
                                        false;
                                    }
                                });
                                $gamedata['markets_2_way']=json_encode($markets);
                                $record=DB::table('trade')->where('sport_id',$sport_id)->where('sport_event_id',$gamedata['sport_event_id'])->first();
                                if($record){
                                    DB::table('trade')->where('id',$record->id)->update($gamedata);
                                }else{
                                    DB::table('trade')->insert($gamedata);
                                }
                            }
                            
                        }
                    }
                    
                }
            }
            $returnData = DB::table('trade')->where('sport_event_start_time','>',$fromdate)->where('sport_event_start_time','<=',$todate)->where('sport_id',$sport_id)->get();
        }
        
        return $returnData;
    }
    private function callApi($type,$sport_id,$event_id,$date){
        $url="";
        $returnData=[];
        switch($type){
            case "tradelist":
                $record=DB::table('trade')->where('schedule_date',$date)->where('sport_id',$sport_id)->get();
                if($record->count()>0 && (Carbon::parse(date('Y-m-d H:i:s'))->diffInSeconds($record[0]->generated_at)<60)){
                    $returnData=$record;
                }else{
                    $url="https://api.sportradar.us/probabilities/production/v1/en/sports/$sport_id/schedules/$date/schedule.json?api_key=tdzzst7pyywvg6drwp9z6z5s";
                }
                break;
            case "tradedetail":
                $record=DB::table('trade_detail')->where('sport_event_id',$event_id)->first();
                if(!empty($record) && (Carbon::parse(date('Y-m-d H:i:s'))->diffInSeconds($record->generated_at)<60 || json_decode($record->sport_event_status)->status=='ended')){
                    $returnData=$record;
                }else{
                    $url="https://api.sportradar.us/probabilities/production/v1/en/sport_events/$event_id/probabilities.json?api_key=tdzzst7pyywvg6drwp9z6z5s";  
                }
                break;
            case "timeline":
                $record=DB::table('trade_detail')->where('sport_event_id',$event_id)->first();
                if(!empty($record) && (Carbon::parse(date('Y-m-d H:i:s'))->diffInSeconds($record->generated_at)<60 || json_decode($record->sport_event_status)->status=='ended')){
                    $returnData=$record;
                }else{
                    $url="https://api.sportradar.us/probabilities/production/v1/en/sport_events/$event_id/timeline.json?api_key=tdzzst7pyywvg6drwp9z6z5s";
                }
                break;
        }
        if($url!=""){
            $jsondata = json_decode($this->GetContents($url));
            if($type=='tradelist'){
                if($jsondata && !empty($jsondata->sport_event_probabilities)){
                    foreach($jsondata->sport_event_probabilities as $game){
                        if(!empty($game->sport_event_status)){
                            
                            $gamedata=[];
                            $gamedata['sport_id']=$sport_id;
                            $gamedata['generated_at']=date('Y-m-d H:i:s',strtotime($jsondata->generated_at));
                            $gamedata['sport_event_id']=$game->sport_event->id;
                            $gamedata['schedule_date']=$date;
    
                            if(!empty($game->sport_event->start_time))
                            $gamedata['sport_event_start_time']=$game->sport_event->start_time;
                            
                            if(!empty($game->sport_event->sport_event_context->competition->id))
                            $gamedata['competition_id']=$game->sport_event->sport_event_context->competition->id;
                            
                            if(!empty($game->sport_event->sport_event_context->competition))
                            $gamedata['competition']=json_encode($game->sport_event->sport_event_context->competition);
                            
                            if(!empty($game->sport_event_status)){
                                $gamedata['sport_event_status']=json_encode($game->sport_event_status);
                            
                            }
                            
                            $gamedata['competitors']=json_encode($game->sport_event->competitors);
                            $gamedata['season_id']=$game->sport_event->sport_event_context->season->id;
                            $markets =array_filter($game->markets, function ($var) {
                                if($var->name=='2way'){
                                    return true;
                                }else{
                                    false;
                                }
                            });
                            $gamedata['markets_2_way']=json_encode($markets);
                            
                            $record=DB::table('trade')->where('sport_id',$sport_id)->where('sport_event_id',$gamedata['sport_event_id'])->first();
                            if($record){
                                DB::table('trade')->where('id',$record->id)->update($gamedata);
                            }else{
                                DB::table('trade')->insert($gamedata);
                            }
                            
                        }
                    }
                    $returnData= DB::table('trade')->where('schedule_date',$date)->where('sport_id',$sport_id)->get();
                    
                }
            }elseif($type=='tradedetail' || $type=='timeline'){
                if(!empty($jsondata) && !empty($jsondata->sport_event)){
                    $game=$jsondata->sport_event;
                    $gamedata=[];
                    $gamedata['sport_id']=$game->sport_event_context->sport->id;
                    $gamedata['generated_at']=date('Y-m-d H:i:s',strtotime($jsondata->generated_at));
                    $gamedata['sport_event_id']=$game->id;
                    $gamedata['sport_event_start_time']=$game->start_time;
                    $gamedata['competition_id']=$game->sport_event_context->competition->id;
                    $gamedata['competition']=json_encode($game->sport_event_context->competition);
                    $gamedata['sport_event_status']=json_encode($jsondata->sport_event_status);
                    $gamedata['competitors']=json_encode($game->competitors);
                    $gamedata['season_id']=$game->sport_event_context->season->id;
                    $markets=[];
                    if(!empty($jsondata->markets)){
                        $markets =array_filter($jsondata->markets, function ($var) {
                            if($var->name=='2way'){
                                return true;
                            }else{
                                false;
                            }
                        });
                    }else{
                        $markets =array_filter($jsondata->timeline, function ($var) {
                            if($var->name=='2way'){
                                return true;
                            }else{
                                false;
                            }
                        });
                    }
                    $gamedata['markets_2_way']=json_encode($markets);
                    if(empty($markets)){
                        $recordlist=DB::table('trade')->where('sport_event_id',$game->id)->first();
                        if(!empty($recordlist) && $recordlist->markets_2_way!=="[]"){
                             $gamedata['markets_2_way']=$recordlist->markets_2_way;
                        }
                    }
                   
                    $record=DB::table('trade_detail')->where('sport_event_id',$gamedata['sport_event_id'])->first();
                    if($record){
                        DB::table('trade_detail')->where('id',$record->id)->update($gamedata);
                    }else{
                        DB::table('trade_detail')->insert($gamedata);
                    }
                    $returnData=(object)$gamedata;
                    
                }
            }

        }
        return $returnData;
    }
    public function invite(Request $request)
    {
        $user = $request->user();
        if($request->filled('mobile_number') && $request->filled('league_id')) {
            foreach($request->mobile_number as $mobile){
                $mobileArr = explode("-",$mobile);
                $countrycode = $mobileArr[0];
                $mobile_number = $mobileArr[1];

                $invitation = new LeagueInvitation;
                $invitation->league_id = $request->league_id;
                $invitation->user_id = $request->users_id ? $request->users_id : $user->id;
                $invitation->mobile_number = $countrycode.$mobile_number;
                $invitation->save();
               // LeagueInvitation::insert($invitation);
                $user = User::where('mobile',$mobile_number)->first();
                $league = League::find($request->league_id);
                $founder = User::find($league->founder);
                if($user){
                    // initialize message array
                    $sid    = env( 'TWILIO_SID' );
                    $token  = env( 'TWILIO_TOKEN' );
                    $client = new Client( $sid, $token );

                    $message = "You are invited to ".ucfirst($league->name)." which will start on ".date('Y-m-d',strtotime($league->created_at))." hosted by ".ucfirst($founder->first_name)." ".ucfirst($founder->last_name)." please join and play with your favourite team \r\n\r\nThanks\r\nGAMESTOCK ";
                    try{
                    $sms_response = $client->messages->create(
                        $countrycode.$mobile_number,
                        [
                            'from' => env( 'TWILIO_FROM' ),
                            'body' => $message,
                        ]
                    );
                   }catch(Exception $e){
                   $data['response'] = true;
                   $data['message'] = $e->getMessage();
                   return response()->json($data, 200);
                    //echo $e->getCode() . ' : ' . $e->getMessage()."<br>";
                   }

                    $push = new PushNotification('apn');
                    $pr = $push->setMessage([
                        'aps' => [
                            'alert' => [
                                'title' => 'Gamestock Notification',
                                'body' => $message
                            ],
                            'sound' => 'default',
                            'badge' => 1

                        ],
                        'extraPayLoad' => [
                            'custom' => 'Join my league',
                        ]
                    ])
                    ->setDevicesToken($user->device_token)
                    ->send()
                    ->getFeedback();
                    $data['response'] = true;
                    $data['message'] = 'League invitation send successfully';
                    //$data['push-response'] = $pr;
                    return response()->json($data, 200);
                }else{
                    $sid    = env( 'TWILIO_SID' );
                    $token  = env( 'TWILIO_TOKEN' );
                    $client = new Client( $sid, $token );
                    $message = "You are invited to ".ucfirst($league->name)." which will start on ".date('Y-m-d',strtotime($league->created_at))." hosted by ".ucfirst($founder->first_name)." ".ucfirst($founder->last_name)." please join and play with your favourite team \r\n\r\nThanks\r\nGAMESTOCK ";
                    try{
                    $sms_response = $client->messages->create(
                        $countrycode.$mobile_number,
                        [
                            'from' => env( 'TWILIO_FROM' ),
                            'body' => $message
                        ]
                    );
                   }catch(Exception $e){
                     $data['response'] = true;
                     $data['message'] = $e->getMessage();
                     return response()->json($data, 200);
                     //echo $e->getCode() . ' : ' . $e->getMessage()."<br>";
                    }

                    $data['response'] = true;
                    $data['message'] = 'League invitation send successfully';
                    return response()->json($data, 200);
                }


            }


        }else{
            $data['response'] = true;
            $data['message'] = 'No League && User found';
            return response()->json($data, 200);
        }


    }

    public function join(Request $request)
    {
        $validator = validator()->make(request()->all(),[
            'league_id' => 'required',
        ]);



        if ($validator->fails()) {
            $data['response'] = false;
            $data['message'] = 'Error';
            $data['data'] = $validator->messages();
            return response()->json($data, 200);
        }
        $user = $request->user();
        $leagueJoined = JoinedLeague::where('user_id',$user->id)->where('league_id',$request->league_id)->first();
        if($leagueJoined){
            $data['response'] = false;
            $data['message'] = 'Already joined';
            return response()->json($data, 200);
        }
                $app = app();
                $invitation = $app->make('stdClass');
                $invitation->league_id = $request->league_id;
                $invitation->user_id = $user->id;
                JoinedLeague::insert($invitation);

                $League = League::find($request->league_id);

                $order["user_id"]=$user->id;
                $order["comment"]="League joined";
                $order["amount"]=$League->portfolio_value;
                $order["txn_type"]="credit";

                $id=DB::table('accounts')->insertGetId($order);



            $data['response'] = true;
            $data['message'] = 'League joined successfully';
            return response()->json($data, 200);
    }

    public function detail(Request $request)
    {
            $model = League::find($request->id);
            $data['response'] = true;
            $data['message'] = 'League details';
            $data['data']= $model;
            return response()->json($data, 200);
    }

    public function userLeagues(Request $request)
    {
            $user = $request->user();
            $model = League::where('founder',$user->id)->whereNull('deleted_at')->get();
            $leagues=[];
            if(!empty($model)){

                foreach($model as $key => $value)
                {
                    if($value->league_duration!="Expired"){


                        $cunt = DB::table('joined_leagues')
                        ->where([
                            ['league_id','=',$value->id],
                            ['user_id','=',$user->id]

                            ])
                        ->count();
                        if($cunt > 0){
                            $model[$key]->is_joined="yes";
                        }else{
                            $model[$key]->is_joined="no";
                        }

                        $model[$key]->no_of_memebers = DB::table('joined_leagues')
                        ->where([
                            ['league_id','=',$value->id],


                            ])
                        ->count();

                        $orders = DB::table('orders')->where('user_id',$user->id)->where('league_id',$value->id)->get();
                        $mperv = 0;
                        $mdiff = 0;
                        $mcp = 0;
                        $mop = 0;
                        $mpv = 0;
                        $portfoliovalue = (int)$value->portfolio_value;
                        if(!$orders->isEmpty()){
                            foreach($orders as $orderm)
                            {
                                $gamemain= $this->callApi('timeline',null,$orderm->sport_event_id,null);
                                if(!empty($gamemain)){
                                    $marketmain=json_decode($gamemain->markets_2_way);
                                    
                                    if(!empty($marketmain) && count($marketmain) > 1){
                                        $market_array=[];
                                        $market_array[]=end($marketmain);
                                        $marketmain=$market_array;
            
                                    }
                                    
                                    
                                    $competitors=json_decode($gamemain->competitors);
                                    if($competitors[0]->id==$orderm->competitor_id){
                                        if(isset($marketmain[0]->outcomes[0]->probability)){
                                            $mcp = (int)$mcp + ((int)round($marketmain[0]->outcomes[0]->probability) * (int)$orderm->share);
                                            $mop = (int)$mop + ((int)$orderm->share_rate * (int)$orderm->share);
                                            $ordersharerate = (int)$orderm->share_rate;
                                            $currentsharerate = (int)round($marketmain[0]->outcomes[0]->probability);
                                            if($ordersharerate!==$currentsharerate){
                                                $curratediff = (int)$mcp - (int)$mop;
                                                $portfoliovalue = (int)$portfoliovalue + $curratediff;
                                            }
    
                                        }
                                    }
                                    if($competitors[1]->id==$orderm->competitor_id){
                                        if(isset($marketmain[0]->outcomes[1]->probability)){
                                            $mcp = (int)$mcp + ((int)round($marketmain[0]->outcomes[1]->probability) * (int)$orderm->share);
                                            $mop = (int)$mop + ((int)$orderm->share_rate * (int)$orderm->share);
                                            $ordersharerate = (int)$orderm->share_rate;
                                            $currentsharerate = (int)round($marketmain[0]->outcomes[1]->probability);
                                            if($ordersharerate!==$currentsharerate){
                                                $curratediff = (int)$mcp - (int)$mop;
                                                $portfoliovalue = (int)$portfoliovalue + $curratediff;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        $mdiff =  (int)$mcp - (int)$mop;
                        if($mdiff > 0){
                            $mpv = '+'. number_format((float)$mdiff, 2, '.', '');
                            if($mop>0){
                                $mperv =  (float)$mdiff / (float)$mop * 100;
                                $mperv = number_format((float)$mperv, 2, '.', '');
                            }else{
                                $mperv=0;
                            }
                        }
                        else{
                            $mpv = number_format((float)$mdiff, 2, '.', '');
                            if($mop>0){
                                $mperv =  (float)$mdiff / (float)$mop * 100;
                                $mperv = number_format((float)$mperv, 2, '.', '');
                            }else{
                                $mperv=0;
                            }
                        }
                        $portfolioamt = '$'.(int)$portfoliovalue;
                        $model[$key]->current_portfolio= $portfolioamt;
                        $leagues[]=$model[$key];
                    }



                }
                $data['response'] = true;
                $data['message'] = 'My League';
                $data['data'] = $leagues;

            }else{

                $data['response'] = false;
                $data['message'] = 'Record not found';
                $data['data'] = [];

            }
            
            return response()->json($data, 200);
    }

    public function userJoinedLeagues(Request $request)
    {
            $user = $request->user();
            $joinedLeagues = JoinedLeague::where('user_id',$user->id)->pluck('league_id')->toArray();
            $model = League::whereIn('id',$joinedLeagues)->whereNull('deleted_at')->orderBy('id','desc')->get();
            $leagues=[];
            if(!empty($model)){

                foreach($model as $key => $value)
                {
                    
                    if($value->league_duration!="Expired"){


                        $cunt = DB::table('joined_leagues')
                        ->where([
                            ['league_id','=',$value->id],
                            ['user_id','=',$user->id]

                            ])
                        ->count();
                        if($cunt > 0){
                            $model[$key]->is_joined="yes";
                        }else{
                            $model[$key]->is_joined="no";
                        }

                        $model[$key]->no_of_memebers = DB::table('joined_leagues')
                        ->where([
                            ['league_id','=',$value->id],


                            ])
                        ->count();

                    //    $orders = DB::table('orders')->where('user_id',$user->id)->where('league_id',$value->id)->get();
                        $orders = DB::table('orders')->select('*', DB::raw('sum(share) as totalshare'), DB::raw('sum(share_paid_amount) as totalshare_paid_amount'))->where('user_id',$user->id)->where('league_id',$value->id)->groupBy('competitor_id')->orderBy('updated_at')->get();
                        $mperv = 0;
                        $mdiff = 0;
                        $mcp = 0;
                        $mop = 0;
                        $mpv = 0;
                        $portfoliovalue = (int)$value->portfolio_value;
                        if(!$orders->isEmpty()){
                            foreach($orders as $orderm)
                            {
                                $gamemain= $this->callApi('tradedetail',null,$orderm->sport_event_id,null);
                                if(!empty($gamemain)){
                                    $marketmain=json_decode($gamemain->markets_2_way);
                                    $competitors=json_decode($gamemain->competitors);
                                    // if($competitors[0]->id==$orderm->competitor_id){
                                    //     if(isset($marketmain[0]->outcomes[0]->probability)){
                                    //         $mcp = (int)$mcp + ((int)round($marketmain[0]->outcomes[0]->probability) * (int)$orderm->share);
                                    //         $mop = (int)$mop + ((int)$orderm->share_rate * (int)$orderm->share);
                                    //         $ordersharerate = (int)$orderm->share_rate;
                                    //         $currentsharerate = (int)round($marketmain[0]->outcomes[0]->probability);
                                    //         if($ordersharerate!==$currentsharerate){
                                    //             $curratediff = (int)$mcp - (int)$mop;
                                    //             $portfoliovalue = (int)$portfoliovalue + $curratediff;
                                    //         }
    
                                    //     }
                                    // }
                                    // if($competitors[1]->id==$orderm->competitor_id){
                                    //     if(isset($marketmain[0]->outcomes[1]->probability)){
                                    //         $mcp = (int)$mcp + ((int)round($marketmain[0]->outcomes[1]->probability) * (int)$orderm->share);
                                    //         $mop = (int)$mop + ((int)$orderm->share_rate * (int)$orderm->share);
                                    //         $ordersharerate = (int)$orderm->share_rate;
                                    //         $currentsharerate = (int)round($marketmain[0]->outcomes[1]->probability);
                                    //         if($ordersharerate!==$currentsharerate){
                                    //             $curratediff = (int)$mcp - (int)$mop;
                                    //             $portfoliovalue = (int)$portfoliovalue + $curratediff;
                                    //         }
                                    //     }
                                    // }
                            if($competitors[0]->id==$orderm->competitor_id){
                                    if(isset($marketmain[0]->outcomes[0]->probability)){
                                        $mcp = (int)$mcp + ((int)round($marketmain[0]->outcomes[0]->probability) * (int)$orderm->totalshare);
                                        $mop = (int)$mop + (int)$orderm->totalshare_paid_amount;
                                        $ordersharerate = (int)$orderm->share_rate;
                                        $currentsharerate = (int)round($marketmain[0]->outcomes[0]->probability);
                                        if($ordersharerate!==$currentsharerate){
                                            $gcp = (int)round($marketmain[0]->outcomes[0]->probability) * (int)$orderm->totalshare;
                                            $gpp = (int)$orderm->totalshare_paid_amount;
                                            $curratediff = (int)$gcp - (int)$gpp;
                                            $portfoliovalue = (int)$portfoliovalue + $curratediff;
                                        }
        
                                    }
                                }
                                if($competitors[1]->id==$orderm->competitor_id){
                                    if(isset($marketmain[0]->outcomes[1]->probability)){
                                        $mcp = (int)$mcp + ((int)round($marketmain[0]->outcomes[1]->probability) * (int)$orderm->totalshare);
                                    
                                        $mop = (int)$mop + (int)$orderm->totalshare_paid_amount;
                                        $ordersharerate = (int)$orderm->share_rate;
                                        $currentsharerate = (int)round($marketmain[0]->outcomes[1]->probability);
                                        if($ordersharerate!==$currentsharerate){
                                            $gcp = (int)round($marketmain[0]->outcomes[1]->probability) * (int)$orderm->totalshare;
                                    
                                            $gpp =  (int)$orderm->totalshare_paid_amount;
                                            $curratediff = (int)$gcp - (int)$gpp;
                                            $portfoliovalue = (int)$portfoliovalue + $curratediff;
                                        }
                                    }
                                }
                                        }
                                    }
                                }
                        $mdiff =  (int)$mcp - (int)$mop;
                        if($mdiff > 0){
                            $mpv = '+'. number_format((float)$mdiff, 2, '.', '');
                            if($mop>0){
                                $mperv =  (float)$mdiff / (float)$mop * 100;
                                $mperv = number_format((float)$mperv, 2, '.', '');
                            }else{
                                $mperv=0;
                            }
                        }
                        else{
                            $mpv = number_format((float)$mdiff, 2, '.', '');
                            if($mop>0){
                                $mperv =  (float)$mdiff / (float)$mop * 100;
                                $mperv = number_format((float)$mperv, 2, '.', '');
                            }else{
                                $mperv=0;
                            }
                        }
                        $portfolioamt = '$'.(int)$portfoliovalue;
                        $model[$key]->current_portfolio= $portfolioamt;
                        $leagues[]=$model[$key];
                    }



                }
                $data['response'] = true;
                $data['message'] = 'Joined League';
                $data['data'] = $leagues;

            }else{

                $data['response'] = false;
                $data['message'] = 'Record not found';
                $data['data'] = [];

            }
         
            return response()->json($data, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        League::find($id)->delete();
        return response()->json(['success'=>'League deleted successfully.']);
    }

    
    function GetCurl($url) {
        $checkApiUrl = ApiRequest::where('url',$url)->first();
        if($checkApiUrl){
            $date = $checkApiUrl->updated_at;
            $currentDate = strtotime($date);
            $futureDate = $currentDate+(60*1);
            $nextDate = date("Y-m-d H:i:s", $futureDate);

            $now = date("Y-m-d H:i:s");

            if(strtotime($now) >= strtotime($nextDate)){
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_URL, $url);
                $data = curl_exec($ch);
                curl_close($ch);
                $model = ApiRequest::where('url',$url)->first();
                $model->data = $data;
                $model->url = $url;
                $model->save();
                return json_decode($data);
                exit;
            }
            else{
                $data = $checkApiUrl->data;
                return json_decode($data);
                exit;
            }

        }
        else{
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            $data = curl_exec($ch);
            curl_close($ch);

           $model = new ApiRequest;
           $model->data = $data;
           $model->url = $url;
           $model->save();
           return json_decode($data);
           exit;
        }
    }
    private function GetContents($url){
        
        $curl_handle=curl_init();
        curl_setopt($curl_handle, CURLOPT_URL,$url);
        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Your application name');
        $query = curl_exec($curl_handle);
        curl_close($curl_handle);
        //echo $query;die;
        return $query;
    }
    public function lederboard(Request $request)
    {
        $user = $request->user();

        if(!empty($request->league_id))
        {
            $league = DB::table('leagues')
            ->select("leagues.id",'leagues.founder',"leagues.name","leagues.league_type",'leagues.duration','leagues.portfolio_value')
            ->where("leagues.id",$request->league_id)
            ->first();

        }else{
            $league = DB::table('joined_leagues')
            ->select("leagues.id",'leagues.founder',"leagues.name","leagues.league_type",'leagues.duration','leagues.portfolio_value','leagues.created_at')
            ->join('leagues','leagues.id','=','joined_leagues.league_id')
            ->where("joined_leagues.user_id",$user->id)
            ->latest()
            ->first();
        }
        if(!empty($league)){
            $league->position ="4/10";
            $league->joined_user=[];

               $joinedlist = DB::table('joined_leagues')
                ->select('users.id as user_id','users.first_name','users.last_name','users.image')
                ->join('users','users.id','=','joined_leagues.user_id')
                ->where("joined_leagues.league_id",$league->id)
                ->get();
                $counter=1;
                $position=0;

                foreach($joinedlist as $row)
                {
                  //  $orders = DB::table('orders')->where('user_id',$row->user_id)->where('league_id',$league->id)->get();
                  $orders = DB::table('orders')->select('*', DB::raw('sum(share) as totalshare'), DB::raw('sum(share_paid_amount) as totalshare_paid_amount'))->where('user_id',$row->user_id)->where('league_id',$league->id)->groupBy('competitor_id')->orderBy('updated_at')->get();
                    $mperv = 0;
                    $mdiff = 0;
                    $mcp = 0;
                    $mop = 0;
                    $mpv = 0;
                    $portfoliovalue = (int)$league->portfolio_value;
                    if(!$orders->isEmpty()){
                    foreach($orders as $orderm)
                    {
                        $gamemain= $this->callApi('timeline',null,$orderm->sport_event_id,null);
                        if(!empty($gamemain)){
                            $marketmain=json_decode($gamemain->markets_2_way);
                            if(!empty($marketmain) && count($marketmain) > 1){
                                $market_array=[];
                                $market_array[]=end($marketmain);
                                $marketmain=$market_array;
    
                            }
                            $competitors=json_decode($gamemain->competitors);
                            // if($competitors[0]->id==$orderm->competitor_id){
                            //     if(isset($marketmain[0]->outcomes[0]->probability)){
                            //         $mcp = (int)$mcp + ((int)round($marketmain[0]->outcomes[0]->probability) * (int)$orderm->share);
                            //         $mop = (int)$mop + ((int)$orderm->share_rate * (int)$orderm->share);
                            //         $ordersharerate = (int)$orderm->share_rate;
                            //         $currentsharerate = (int)round($marketmain[0]->outcomes[0]->probability);
                            //         if($ordersharerate!==$currentsharerate){
                            //             $curratediff = (int)$mcp - (int)$mop;
                            //             $portfoliovalue = (int)$portfoliovalue + $curratediff;
                            //         }

                            //     }
                            // }
                            // if($competitors[1]->id==$orderm->competitor_id){
                            //     if(isset($marketmain[0]->outcomes[1]->probability)){
                            //         $mcp = (int)$mcp + ((int)round($marketmain[0]->outcomes[1]->probability) * (int)$orderm->share);
                            //         $mop = (int)$mop + ((int)$orderm->share_rate * (int)$orderm->share);
                            //         $ordersharerate = (int)$orderm->share_rate;
                            //         $currentsharerate = (int)round($marketmain[0]->outcomes[1]->probability);
                            //         if($ordersharerate!==$currentsharerate){
                            //             $curratediff = (int)$mcp - (int)$mop;
                            //             $portfoliovalue = (int)$portfoliovalue + $curratediff;
                            //         }
                            //     }
                            // }
                             if($competitors[0]->id==$orderm->competitor_id){
                            if(isset($marketmain[0]->outcomes[0]->probability)){
                                $mcp = (int)$mcp + ((int)round($marketmain[0]->outcomes[0]->probability) * (int)$orderm->totalshare);
                                $mop = (int)$mop + (int)$orderm->totalshare_paid_amount;
                                $ordersharerate = (int)$orderm->share_rate;
                                $currentsharerate = (int)round($marketmain[0]->outcomes[0]->probability);
                                if($ordersharerate!==$currentsharerate){
                                    $gcp = (int)round($marketmain[0]->outcomes[0]->probability) * (int)$orderm->totalshare;
                                    $gpp = (int)$orderm->totalshare_paid_amount;
                                    $curratediff = (int)$gcp - (int)$gpp;
                                    $portfoliovalue = (int)$portfoliovalue + $curratediff;
                                }

                            }
                        }
                        if($competitors[1]->id==$orderm->competitor_id){
                            if(isset($marketmain[0]->outcomes[1]->probability)){
                                $mcp = (int)$mcp + ((int)round($marketmain[0]->outcomes[1]->probability) * (int)$orderm->totalshare);
                            
                                $mop = (int)$mop + (int)$orderm->totalshare_paid_amount;
                                $ordersharerate = (int)$orderm->share_rate;
                                $currentsharerate = (int)round($marketmain[0]->outcomes[1]->probability);
                                if($ordersharerate!==$currentsharerate){
                                    $gcp = (int)round($marketmain[0]->outcomes[1]->probability) * (int)$orderm->totalshare;
                            
                                    $gpp =  (int)$orderm->totalshare_paid_amount;
                                    $curratediff = (int)$gcp - (int)$gpp;
                                    $portfoliovalue = (int)$portfoliovalue + $curratediff;
                                }
                            }
                        }
                        }
                    }
                    $mdiff =  (int)$mcp - (int)$mop;
                    if($mdiff > 0){
                        $mpv = '+'. number_format((float)$mdiff, 2, '.', '');
                        if($mop>0){
                            $mperv =  (float)$mdiff / (float)$mop * 100;
                            $mperv = number_format((float)$mperv, 2, '.', '');
                        }else{
                            $mperv=0;
                        }
                    }
                    else{
                        $mpv = number_format((float)$mdiff, 2, '.', '');
                        if($mop>0){
                            $mperv =  (float)$mdiff / (float)$mop * 100;
                            $mperv = number_format((float)$mperv, 2, '.', '');
                        }else{
                            $mperv=0;
                        }
                    }
                    $portfolioamt = '$'.(int)$portfoliovalue;

                   // $portfolio_status = $mpv." (".$mperv."%)";
                    $portfolio_average = $mpv;
                  }
                  else{
                    $portfolioamt = '$'.(int)$league->portfolio_value;
                   // $resdata['portfolio_value']="0";
                   $portfolio_average="0.00";
                }

                    $total_leagues_joined=DB::table('joined_leagues')->select('*')->where("user_id",$row->user_id)->count();
                    $league->joined_user[]=[
                        "user_id"=>$row->user_id,
                        "name"=>$row->first_name." ".$row->last_name,
                        "image"=> asset('uploads/images/user/'.$row->image),
                        "first_place_finishes"=> (string)rand(1,9),
                        "total_leagues_joined"=> (string)$total_leagues_joined,
                        "avarage_portfolio"=> $portfolio_average,
                        "amount"=> $portfolioamt,
                        "is_premium"=>(string)rand(0,1),
                    ];

                    if($row->user_id==$user->id){
                        $position=$counter;
                    }
                    $counter++;
                }
                $league->position=$position."/".count($joinedlist);
                $data['response'] = true;
                $data['message'] = 'Record found';
                $data['data']= $league;
        }else{
            $data['response'] = false;
            $data['message'] = 'Record not found';

        }
        return response()->json($data, 200);
    }
    public function games(Request $request)
    {
        $user = $request->user();

        $games = DB::table('games')
        ->select("*")
        ->get();
        if(!empty($games)){

                $data['response'] = true;
                $data['message'] = 'Record found';
                $data['data']= $games;
        }else{
            $data['response'] = false;
            $data['message'] = 'Record not found';

        }
        return response()->json($data, 200);
    }
    public function premiumUpdate(Request $request)
    {
        $user = $request->user();


        $premium["user_id"]=$user->id;
        $premium["title"]=$request->title;
        $premium["amount"]=$request->amount;
        $premium["start_date"]=$request->start_date;
        $premium["currency"]=$request->currency;
        $premium["end_date"]=$request->end_date;

        $id=DB::table('membership')->insertGetId($premium);

        if(!empty($id))
        {

                $data['response'] = true;
                $data['message'] = 'Successful';

        }else{
            $data['response'] = false;
            $data['message'] = 'failure';

        }
        return response()->json($data, 200);
    }
    public function accountDetail(Request $request)
    {
        $user = $request->user();
        //print_r( $user);
        $data=[];

        $resdata['free_trade_count'] =$user->free_trade_count;
        $resdata['account_balance'] =$this->accountBalance($user->id);
        $resdata['is_membership'] ='FREE';
        $resdata['mem_start_date'] ='';
        $resdata['mem_end_date'] ='';
        $current_date=date("Y-m-d");

        $membership = DB::table('membership')
                        ->select('*')
                        ->where([
                                ["user_id","=",$user->id],
                                ["start_date","<=",$current_date],
                                ["end_date",">=",$current_date],
                            ])
                        ->latest('id')->first();

        if(!empty($membership))
        {
            $resdata['is_membership'] ='PAID';
            $resdata['mem_start_date'] =$membership->start_date;
            $resdata['mem_end_date'] =$membership->end_date;
        }

        $data['response'] = true;
        $data['message'] = 'success';
        $data['data'] = $resdata ;
        return response()->json($data, 200);
    }
    public function accountBalance($user_id)
    {

        $res=DB::select(DB::raw("select sum(case when txn_type = 'credit' then amount else -amount end) as balance from accounts where user_id = $user_id"));
       // print_r( $res);
        if(empty($res[0]->balance)){
            return "0.00";

        }else{
            return (string)$res[0]->balance;
        }
    }
    
    public function home(Request $request)
    {
        $user = $request->user();
        $resdata=[];
        if($request->league_id){
            $league =  League::select('leagues.*')->join('joined_leagues','leagues.id','=','joined_leagues.league_id')
            ->where("joined_leagues.user_id","=",$user->id )
            ->where("joined_leagues.league_id","=",$request->league_id )
            ->orderBy('leagues.created_at')
            ->first();

        }else{

            $league =  League::select('leagues.*')->join('joined_leagues','leagues.id','=','joined_leagues.league_id')
            ->where("joined_leagues.user_id","=",$user->id )
            ->latest('leagues.created_at')
            ->first();

        }

        if($league && $league->league_duration!="Expired") 
        {
            
           // $orders = DB::table('orders')->where('user_id',$user->id)->where('league_id',$league->id)->get();
            $orders = DB::table('orders')->select('*', DB::raw('sum(share) as totalshare'), DB::raw('sum(share_paid_amount) as totalshare_paid_amount'))->where('user_id',$user->id)->where('league_id',$league->id)->groupBy('competitor_id')->orderBy('updated_at')->get();
            if(!$orders->isEmpty()){
                $buybalance = DB::table('orders')
                        ->where('user_id',$user->id)
                        ->where('league_id',$request->league_id)
                        ->where('txn_type', '=', 'buy')
                        ->sum('share_paid_amount');
               // $resdata['portfolio_value'] = (int)$buybalance;
                $mperv = 0;
                $mdiff = 0;
                $mcp = 0;
                $mop = 0;
                $mpv = 0;
                $ptdf = '';
                $portfoliovalue = (int)$league->portfolio_value;
                foreach($orders as $orderm)
                {
                    $gamemain=$this->callApi('tradedetail',null,$orderm->sport_event_id,null);
                    if(!empty($gamemain)){
                        $marketmain=json_decode($gamemain->markets_2_way);
                        if(!empty($marketmain) && count($marketmain) > 1){
                            $market_array=[];
                            $market_array[]=end($marketmain);
                            $marketmain=$market_array;
                        }
                        $competitors=json_decode($gamemain->competitors);
                        if($competitors[0]->id==$orderm->competitor_id){
                            if(isset($marketmain[0]->outcomes[0]->probability)){
                                $mcp = (int)$mcp + ((int)round($marketmain[0]->outcomes[0]->probability) * (int)$orderm->totalshare);
                                $mop = (int)$mop + (int)$orderm->totalshare_paid_amount;
                                $ordersharerate = (int)$orderm->share_rate;
                                $currentsharerate = (int)round($marketmain[0]->outcomes[0]->probability);
                                if($ordersharerate!==$currentsharerate){
                                    $gcp = (int)round($marketmain[0]->outcomes[0]->probability) * (int)$orderm->totalshare;
                                    $gpp = (int)$orderm->totalshare_paid_amount;
                                    $curratediff = (int)$gcp - (int)$gpp;
                                    $portfoliovalue = (int)$portfoliovalue + $curratediff;
                                }

                            }
                        }
                        if($competitors[1]->id==$orderm->competitor_id){
                            if(isset($marketmain[0]->outcomes[1]->probability)){
                                $mcp = (int)$mcp + ((int)round($marketmain[0]->outcomes[1]->probability) * (int)$orderm->totalshare);
                            
                                $mop = (int)$mop + (int)$orderm->totalshare_paid_amount;
                                $ordersharerate = (int)$orderm->share_rate;
                                $currentsharerate = (int)round($marketmain[0]->outcomes[1]->probability);
                                if($ordersharerate!==$currentsharerate){
                                    $gcp = (int)round($marketmain[0]->outcomes[1]->probability) * (int)$orderm->totalshare;
                            
                                    $gpp =  (int)$orderm->totalshare_paid_amount;
                                    $curratediff = (int)$gcp - (int)$gpp;
                                    $portfoliovalue = (int)$portfoliovalue + $curratediff;
                                }
                            }
                        }
                    }
                }
                $mdiff =  (int)$mcp - (int)$mop;
                if($mdiff > 0){
                    $mpv = '+'. number_format((float)$mdiff, 2, '.', '');
                    if($mop>0){
                        $mperv =  (float)$mdiff / (float)$mop * 100;
                        $mperv = number_format((float)$mperv, 2, '.', '');
                    }else{
                        $mperv=0;
                    }
                }
                else{
                    $mpv = number_format((float)$mdiff, 2, '.', '');
                    if($mop>0){
                        $mperv =  (float)$mdiff / (float)$mop * 100;
                        $mperv = number_format((float)$mperv, 2, '.', '');
                    }else{
                        $mperv=0;
                    }
                }
                $resdata['portfolio_value'] = '$'.(int)$portfoliovalue;
                $resdata['portfolio_status']= $mpv." (".$mperv."%)";
                $resdata['portfolio_difference']= (int)$mpv;

                $ordersnew = DB::table('orders')->select('*', DB::raw('sum(share) as totalshare'), DB::raw('sum(share_paid_amount) as totalshare_paid_amount'))->where('user_id',$user->id)->where('league_id',$league->id)->groupBy('competitor_id')->orderBy('updated_at')->get();
                $competitor = array();
                foreach($ordersnew as $order)
                {
                    
                    $game=$this->callApi('tradedetail',null,$order->sport_event_id,null);
                    if(!empty($game)){
                        $market=json_decode($game->markets_2_way);
                        if(!empty($market) && count($market) > 1){
                            $market_array=[];
                            $market_array[]=end($market);
                            $market=$market_array;

                        }
                        $competitors=json_decode($game->competitors);
                        if($competitors[0]->id==$order->competitor_id){

                            if(isset($market[0]->outcomes[0]->probability)){
                                $current_portfolio = (int)round($market[0]->outcomes[0]->probability) * (int)$order->totalshare;
                            
                                $old_portfolio = (int)$order->totalshare_paid_amount;
                                $old_portfolio_mp =  (int)$current_portfolio - (int)$old_portfolio;

                                $currentPortfolio =(int)round($market[0]->outcomes[0]->probability) * (int)$order->totalshare;
                                if($old_portfolio_mp > 0){
                                    $pv = '+'. number_format((float)$old_portfolio_mp, 2, '.', '');
                                    if($old_portfolio>0){
                                        $perv =  (float)$old_portfolio_mp / (float)$old_portfolio * 100;
                                        $perv = number_format((float)$perv, 2, '.', '');
                                    }else{
                                        $perv=0;
                                    }
                                }
                                else{
                                    $pv = number_format((float)$old_portfolio_mp, 2, '.', '');
                                    if($old_portfolio>0){
                                        $perv =  (float)$old_portfolio_mp / (float)$old_portfolio * 100;
                                        $perv = number_format((float)$perv, 2, '.', '');
                                    }else{
                                        $perv=0;
                                    }
                                }

                                $gamesTable = DB::table('games')->where('sport_id',$order->sport_id)->where('competition_id',$order->competition_id)->first();
                                $gameimage = $gamesTable ? asset('uploads/images/games/'.$gamesTable->image) : '';
                                $portfolio_status = $pv." (".$perv."%)";
                                $currentsharerate = (int)round($market[0]->outcomes[0]->probability);
                                $team = $competitors[0]->abbreviation.' vs. '.$competitors[1]->abbreviation;
                                $team1sharerate = (int)round($market[0]->outcomes[0]->probability);
                                $team2sharerate = (int)round($market[0]->outcomes[1]->probability);
                                $sporteventstatus = json_decode($game->sport_event_status);

                                if(!empty($market)){
                                    $win_loss_probabilities = array_values(array_slice($market, -1))[0];
                                    $win_loss = round($win_loss_probabilities->outcomes[0]->probability);
                                }
                                else{
                                    $win_loss = 0;
                                }
                                if(is_string($sporteventstatus)){
                                    $sporteventstatus=json_decode($sporteventstatus);
                                }
                                $sport_start_time=$game->sport_event_start_time?$game->sport_event_start_time:'';
                                $competitor[] = ['name'=>$competitors[0]->name,'sport_start_time'=>$sport_start_time,'team'=>$team,'win_loss'=>$win_loss,'sporteventstatus'=>$sporteventstatus,'team1_share_rate'=>$team1sharerate,'team2_share_rate'=>$team2sharerate,'current_portfolio'=>$currentPortfolio,'current_share_rate'=>(int)$currentsharerate,'order_share'=>$order->totalshare,'old_portfolio'=>(int)$old_portfolio,'old_share_rate'=>(int)$order->share_rate,'image'=>$gameimage];

                            }


                        }
                        if($competitors[1]->id==$order->competitor_id){
                            if(isset($market[0]->outcomes[1]->probability)){
                                $current_portfolio = (int)round($market[0]->outcomes[1]->probability) * (int)$order->totalshare;
                                
                                $old_portfolio = (int)$order->totalshare_paid_amount;
                                $old_portfolio_mp =  (float)$current_portfolio - (float)$old_portfolio;
                                if($old_portfolio_mp > 0){
                                    $pv = '+'. number_format((float)$old_portfolio_mp, 2, '.', '');
                                    $perv =  (float)$old_portfolio_mp / (float)$old_portfolio * 100;
                                    $perv = number_format((float)$perv, 2, '.', '');
                                }
                                else{
                                    $pv = number_format((float)$old_portfolio_mp, 2, '.', '');
                                    $perv =  (float)$old_portfolio_mp / (float)$old_portfolio * 100;
                                    $perv = number_format((float)$perv, 2, '.', '');
                                }
                                
                                $gamesTable = DB::table('games')->where('sport_id',$order->sport_id)->where('competition_id',$order->competition_id)->first();
                                $gameimage = $gamesTable ? asset('uploads/images/games/'.$gamesTable->image) : '';
                                $portfolio_status = $pv." (".$perv."%)";
                                $currentsharerate = (int)round($market[0]->outcomes[1]->probability);
                                $team = $competitors[0]->abbreviation.' vs. '.$competitors[1]->abbreviation;
                                $team1sharerate = (int)round($market[0]->outcomes[0]->probability);
                                $team2sharerate = (int)round($market[0]->outcomes[1]->probability);
                                $sporteventstatus = $game->sport_event_status;

                                if(!empty($market)){
                                    $win_loss_probabilities = array_values(array_slice($market, -1))[0];
                                    $win_loss = round($win_loss_probabilities->outcomes[1]->probability);
                                }
                                else{
                                    $win_loss = 0;
                                }
                                if(is_string($sporteventstatus)){
                                    $sporteventstatus=json_decode($sporteventstatus);
                                }
                                $sport_start_time=$game->sport_event_start_time?$game->sport_event_start_time:'';
                                $competitor[] = ['name'=>$competitors[1]->name,'sport_start_time'=>$sport_start_time,'team'=>$team,'win_loss'=>$win_loss,'sporteventstatus'=>$sporteventstatus,'team1_share_rate'=>$team1sharerate,'team2_share_rate'=>$team2sharerate,'current_portfolio'=>$current_portfolio,'current_share_rate'=>(int)$currentsharerate,'order_share'=>$order->totalshare,'old_portfolio'=>(int)$old_portfolio,'old_share_rate'=>(int)$order->share_rate,'image'=>$gameimage];
                            }


                        }
                    }

                }

                $resdata['league']=$league;
                $resdata['orders']=$competitor;

               $data['response'] = true;
               $data['message'] = 'success';
               $data['data'] = $resdata ;
               return response()->json($data, 200);
            }
            else{
                $resdata['portfolio_value'] = '$'.(int)$league->portfolio_value;
               
                $resdata['portfolio_status']="+0.00 (0.0%)";
                $resdata['league']=$league;
                $resdata['orders']=[];

                $data['response'] = true;
                $data['message'] = 'success';
                $data['data'] = $resdata ;
                return response()->json($data, 200);
            }

        }else{
            $data['response'] = false;
            $data['message'] = 'Record not found';
            $data['data'] =[];
            return response()->json($data, 200);
        }
    }

    public function freeTradeCount(Request $request)
    {
        $validator = validator()->make(request()->all(),[
            'sport_event_id' => 'required',
        ]);


        if ($validator->fails()) {
            $data['response'] = false;
            $data['message'] = 'Sports event missing';
            $data['data']= $validator->messages();
            return response()->json($data);
       }

        $user = $request->user();
        $matchThese = ['user_id' => $user->id, 'sport_event_id' => $request->sport_event_id];
  

        $freetradecount = FreeTradeCount::where($matchThese)->count();
        $resdata['free_trade_count'] =$freetradecount;
        $resdata['is_membership'] ='FREE';
        $resdata['mem_start_date'] ='';
        $resdata['mem_end_date'] ='';
        $current_date=date("Y-m-d");

        $membership = DB::table('membership')
                        ->select('*')
                        ->where([
                                ["user_id","=",$user->id],
                                ["start_date","<=",$current_date],
                                ["end_date",">=",$current_date],
                            ])
                        ->latest('id')->first();

        if(!empty($membership))
        {
            $resdata['is_membership'] ='PAID';
            $resdata['mem_start_date'] =$membership->start_date;
            $resdata['mem_end_date'] =$membership->end_date;
        }
        $data['response'] = true;
        $data['message'] = 'success';
        $data['data'] = $resdata;
        return response()->json($data, 200);
    }

    public function BuyShare(Request $request)
    {
        $user = $request->user();

        $current_date=date("Y-m-d");
        $is_paid=false;
        $membership = DB::table('membership')
                        ->select('*')
                        ->where([
                                ["user_id","=",$user->id],
                                ["start_date","<=",$current_date],
                                ["end_date",">=",$current_date],
                            ])
                        ->latest('id')->first();

        if(!empty($membership))
        {
            $is_paid=true;
        }

        $league = League::find($request->league_id);
        if(!empty($league)){

            $order["user_id"]=$user->id;
            $order["league_id"]=$league->id;
            $game= DB::table("games")->where("id",$request->game_id)->first();
            if($game)
            {
                $order["game_id"]= $game->id;
                $order["sport_id"]=$game->sport_id;
                $order["competition_id"]=$game->competition_id;
                $order["sport_event_id"]=$request->sport_event_id;
                $order["season_id"]=$request->season_id;
                $order["competitor_id"]=$request->competitor_id;
                $order["txn_type"]="buy";
                $order["share"]=(int)$request->share;
                $order["share_rate"]=(float)$request->share_rate;
                $order["share_paid_amount"]=(float)$request->share_paid_amount;
                if($is_paid){
                    
                    $id=DB::table('orders')->insertGetId($order);
                    $data['response'] = true;
                    $data['message'] = 'success';
                    $data['data'] =$order;
                }else
                {
                   /* $order_res= DB::table("orders")->where("user_id",$user->id)->first();
                    if($order_res)
                    {
                        if($order_res->league_id==$league->id){
                            $id=DB::table('orders')->insertGetId($order);
                            $data['response'] = true;
                            $data['message'] = 'success';
                            $data['data'] =$order;
                        }else{
                            $event= DB::table("orders")->where(["user_id"=>$user->id,"league_id"=>$order_res->league_id,"sport_event_id"=>$request->sport_event_id])->first();
                            if ($event) {
                                $id=DB::table('orders')->insertGetId($order);
                                $data['response'] = true;
                                $data['message'] = 'success';
                                $data['data'] =$order;
                            } else {
                                $data['response'] = false;
                                $data['message'] = 'upgrade membership';
                                $data['data'] =$order;
                            }
                        }

                    }else{
                        $id=DB::table('orders')->insertGetId($order);
                        $data['response'] = true;
                        $data['message'] = 'success';
                        $data['data'] =$order;
                    } */

                    $event= DB::table("orders")->where(["user_id"=>$user->id,"league_id"=>$league->id,"sport_event_id"=>$request->sport_event_id])->first();
                    if ($event) {
                        $data['response'] = false;
                        $data['message'] = 'upgrade membership';
                        $data['data'] =$order;
                        
                    } else {
                        $id=DB::table('orders')->insertGetId($order);
                        $data['response'] = true;
                        $data['message'] = 'success';
                        $data['data'] =$order;
                    }


                }
               
                /*$userLimit = $request->user();
                if($userLimit){
                    $userLimit->free_trade_count = 0;
                    $userLimit->save();
                    $freetradecount = FreeTradeCount::where('user_id',$userLimit->id)->where('sport_event_id',$request->sport_event_id)->get()->count();
                    if($freetradecount == 0){
                        $ftc = new FreeTradeCount;
                        $ftc->user_id = $userLimit->id;
                        $ftc->sport_event_id = $request->sport_event_id;
                        $ftc->save();
                    }
                } 
                $data['response'] = true;
                $data['message'] = 'success';
                $data['data'] =$order;  */

            }else{
                $data['response'] = false;
                $data['message'] = 'Game not found';
                $data['data'] =[];
            }
        }else{
            $data['response'] = false;
            $data['message'] = 'League not found';
            $data['data'] =[];
        }
        return response()->json($data, 200);
    }

    public function sellShare(Request $request)
    {
        $user = $request->user();

        $league = League::find($request->league_id);
        if(!empty($league)){

            $order["user_id"]=$user->id;
            $order["league_id"]=$league->id;

            $game= DB::table("games")->where("id",$request->game_id)->first();
            if($game)
            {
                $order["game_id"]= $game->id;
                $order["sport_id"]=$game->sport_id;
                $order["competition_id"]=$game->competition_id;
                $order["sport_event_id"]=$request->sport_event_id;
                $order["season_id"]=$request->season_id;
                $order["competitor_id"]=$request->competitor_id;
                $order["txn_type"]="sell";
                $order["share"]=(int)$request->share;
                $order["share_rate"]=(float)$request->share_rate;
                $order["share_paid_amount"]=(float)$request->share_paid_amount;
                $id=DB::table('orders')->insertGetId($order);

                $data['response'] = true;
                $data['message'] = 'success';
                $data['data'] =$order;

            }else{
                $data['response'] = false;
                $data['message'] = 'Game not found';
                $data['data'] =[];
            }
        }else{
            $data['response'] = false;
            $data['message'] = 'League not found';
            $data['data'] =[];
        }
        return response()->json($data, 200);
    }

    public function invitationList(Request $request)
    {
        $user = $request->user();

        $leaguedata=[];

            $invitations= DB::table("league_invitations")
            ->where("league_invitations.mobile_number","=", $user->country_code.$user->mobile)
            ->latest()
            ->get();
            if($invitations)
            {
                foreach($invitations as $row){

                     $league= League::find($row->league_id);
                     if(!empty($league)){
                        $league->mobile_number=$row->mobile_number;
                        $leaguedata[]=$league;
                     }

                }


                $data['response'] = true;
                $data['message'] = 'success';
                $data['data'] =$leaguedata;

            }else{
                $data['response'] = false;
                $data['message'] = 'Game not found';
                $data['data'] =[];
            }

        return response()->json($data, 200);
    }

    public function homeGraph(Request $request)
    {
            $user = $request->user();
            $leaguedata = [];
            $joinedLeague = JoinedLeague::where('league_id',$request->league_id)->where('user_id',$user->id)->first();
            if($joinedLeague)
            {
                $league = League::find($request->league_id);
                $orders = DB::table('orders')->select('*', DB::raw('sum(share) as totalshare'), DB::raw('sum(share_paid_amount) as totalshare_paid_amount'))->where('user_id',$user->id)->where('league_id',$league->id)->groupBy('competitor_id')->orderBy('updated_at')->get();

                $mperv = 0;
                $mdiff = 0;
                $mcp = 0;
                $mop = 0;
                $mpv = 0;
                $ptdf = '';
                $portfoliovalue = (int)$league->portfolio_value;
                $marketprobabilities = [];
                $timelineoutcomes = [];

                $leaguedata['league_name'] = $league ? $league->name : '';
                $leaguedata['portfolio_value'] = $league ? $league->portfolio_value : '';
                $leaguedata['joined_date'] = $joinedLeague->created_at->format('Y-m-d H:i:s');

                foreach($orders as $i => $orderm)
                {
                    $gametimeline=$this->callApi('timeline',null,$orderm->sport_event_id,null);
                    if(!empty($gametimeline)){
                        $markettimeline=json_decode($gametimeline->markets_2_way);
                        
                        if(!empty($markettimeline) && count($markettimeline) > 1){
                            $market_array=[];
                            $market_array[]=end($markettimeline);
                            $markettimeline=$market_array;

                        }

                        
                        
                        
                        $competitors=json_decode($gametimeline->competitors);
                        if(!empty($markettimeline) || $markettimeline!=''){
                        
                            foreach($markettimeline as $mt){
                                $portfolio_timeline = 0;
                                if($competitors[0]->id==$orderm->competitor_id){
                                    $currentPortfolioTimeline = (int)round($mt->outcomes[0]->probability) * (int)$orderm->share;
                                    $oldPortfolioTimeline = round($orderm->share_rate * $orderm->share);
                                    $curratediffTimeline = (int)$currentPortfolioTimeline - (int)$oldPortfolioTimeline;
                                    $portfolio_timeline = (int)$league->portfolio_value + $curratediffTimeline;
                                }
                                if($competitors[1]->id==$orderm->competitor_id){
                                    $currentPortfolioTimeline = (int)round($mt->outcomes[1]->probability) * (int)$orderm->share;
                                    $oldPortfolioTimeline = round($orderm->share_rate * $orderm->share);
                                    $curratediffTimeline = (int)$currentPortfolioTimeline - (int)$oldPortfolioTimeline;
                                    $portfolio_timeline = (int)$league->portfolio_value + $curratediffTimeline;
                            }

                            $marketprobabilities[] = ['portfoliovalue'=>$portfolio_timeline,'last_updated'=>date('Y-m-d H:i:s',strtotime($mt->last_updated))];

                            }
                        }
                    }
                }
                $leaguedata['probabilities'] = array_reverse($marketprobabilities);

                $data['response'] = true;
                $data['message'] = 'success';
                $data['data'] =$leaguedata;

            }else{
                $data['response'] = false;
                $data['message'] = 'Record not found';
                $data['data'] =[];
            }

        return response()->json($data, 200);
    }


    public function tradeDetailGraph(Request $request)
    {
        $validator = validator()->make(request()->all(),[
            'sport_event_id' => 'required',
            'competitor_id' => 'required',
        ]);

        if ($validator->fails()) {
            $data['response'] = false;
            $data['message'] = 'The sport event id  and competitor id  required.';
            $data['data']= $validator->messages();
            return response()->json($data);
        }

            $user = $request->user();
            $leaguedata = [];
            $marketprobabilities = [];
            $gametimeline=$this->callApi('timeline',null,$request->sport_event_id,null);
            if(!empty($gametimeline)){
                $markettimeline=json_decode($gametimeline->markets_2_way);
                
                if(!empty($markettimeline) && count($markettimeline) > 1){
                            $market_array=[];
                            $market_array[]=end($markettimeline);
                            $markettimeline=$market_array;

                }
                $competitors=json_decode($gametimeline->competitors);
                if(!empty($markettimeline)){
                    
                    if($competitors[0]->id==$request->competitor_id){
                        foreach($markettimeline as $mt){
                            $marketprobabilities[] = ['probability'=>round($mt->outcomes[0]->probability),'live'=>isset($mt->live)?$mt->live:false,'last_updated'=>date('Y-m-d H:i:s',strtotime($mt->last_updated))];
                        }
                    }

                    if($competitors[1]->id==$request->competitor_id){
                        foreach($markettimeline as $mt){
                            $marketprobabilities[] = ['probability'=>round($mt->outcomes[1]->probability),'live'=>isset($mt->live)?$mt->live:false,'last_updated'=>date('Y-m-d H:i:s',strtotime($mt->last_updated))];
                        }
                    }
                }
            }

            $leaguedata['probabilities'] = array_reverse($marketprobabilities);

            $data['response'] = true;
            $data['message'] = 'success';
            $data['data'] =$leaguedata;

        return response()->json($data, 200);
    }
    
    public function tradelist(Request $request){
        $user = $request->user();
        
       /* $sbdata=[];
         $sbdata['data']="$request->year-$request->month-$request->day";
        DB::table('api_requests')->insert( $sbdata);
        */
        //date_default_timezone_set($request->timezone);
        //$data=$this->callApi('tradelist',$request->sport_id,null,"$request->year-$request->month-$request->day");
        $data=$this->callScheduleApi($request->sport_id,"$request->year-$request->month-$request->day",$request->timezone,$request->time,$request->competition_id);
        if($data){
            $league_id=$request->league_id;
            $response=[];
            if($data->count()>0){
                $response['response']=true;
                $response['message']='Successfully';
                $returndata=[];
                foreach($data as $key=>$row){
                    if($row->competition_id==$request->competition_id){
                        $gamedata=[];
                        $gamedata['sport_event_id']=$row->sport_event_id;
                        $gamedata['sport_start_time']=$row->sport_event_start_time;
                        $gamedata['season_id']=$row->season_id;
                        $gamedata['sport_event_status']=json_decode($row->sport_event_status);
                        
                        $competition=json_decode($row->competition);
                        $markets_2_way=json_decode($row->markets_2_way);
                        if(!empty($markets_2_way) && count($markets_2_way) > 1){
                            $market_array=[];
                            $market_array[]=end($markets_2_way);
                            $markets_2_way=$market_array;

                        }


                        $competitors=json_decode($row->competitors);
                        if($markets_2_way){
                            if(!empty($competitors[0]) && $competitors[0]->qualifier=='home'){
                                $orders = DB::table('orders')->where('user_id',$user->id)->where('league_id',$league_id)->where('competitor_id',$competitors[0]->id)->get();
                                $orderscount = DB::table('orders')->where('user_id',$user->id)->where('league_id',$league_id)->where('competitor_id',$competitors[0]->id)->where('txn_type','sell')->get()->count();
                                if($orderscount > 0){
                                    $orders = null;
                                }
                                $cr = 0;
                                $cp = 0;
                                $op = 0;
                                $opp = 0;
                                $opv = 0;
                                if($orders){
                                    foreach($orders as $order){
                                        $current_portfolio = (int)round($markets_2_way[0]->outcomes[0]->probability) * (int)$order->share;
                                        $cp = (int)$cp + (int) $current_portfolio;
                                        $old_portfolio = (int)$order->share_rate * (int)$order->share;
                                        $op = (int) $op + (int) $old_portfolio;
                                        $old_portfolio_mp =  (int)$current_portfolio - (int)$old_portfolio;
                                        $opp = (int) $opp + (int)$old_portfolio_mp;
                                        $cr = (int) $cr + ((int)$order->share * (int)round($markets_2_way[0]->outcomes[0]->probability));
                                    }
                                }
                                $current_rate = (int)$cr;
                                if($opp!=0 && $op){
                                    $perv =  (float)$opp / (float)$op * 100;
                                }
                                else{
                                    $perv = 0;
                                }
                                $perv = number_format((float)$perv, 2, '.', '');
                                if(!empty($markets_2_way)){
        
                                    $win_loss_probabilities = array_values(array_slice($markets_2_way, -1))[0];
                                    $win_loss_home = round($win_loss_probabilities->outcomes[0]->probability);
                                }
                                else{
                                    $win_loss_home = 0;
                                }
                                $gamedata['hometeam']=[
                                    "competitor_id"=>$competitors[0]->id,
                                    "name"=>$competitors[0]->name,
                                    "trade_rate"=>$perv,
                                    "trade_amount"=>$current_rate,
                                    "current_rate"=>(int)round($markets_2_way[0]->outcomes[0]->probability),
                                    "win_loss" => $win_loss_home
                                ];
                                $orders1 = DB::table('orders')->where('user_id',$user->id)->where('league_id',$league_id)->where('competitor_id',$competitors[1]->id)->get();
                                $orderscount1 = DB::table('orders')->where('user_id',$user->id)->where('league_id',$league_id)->where('competitor_id',$competitors[1]->id)->where('txn_type','sell')->get()->count();
                                if($orderscount1 > 0){
                                    $orders1 = null;
                                }
                                if($orders1){
                                    $cr1 = 0;
                                    $cp1 = 0;
                                    $op1 = 0;
                                    $opp1 = 0;
                                    $opv1 = 0;
                                    foreach($orders1 as $order1){
                                        $current_portfolio1 = (int)round($markets_2_way[0]->outcomes[1]->probability) * (int)$order1->share;
                                        $cp1 = (int)$cp1 + (int) $current_portfolio1;
                                        $old_portfolio1 = (int)$order1->share_rate * (int)$order1->share;
                                        $op1 = (int) $op1 + (int) $old_portfolio1;
                                        $old_portfolio_mp1 =  (int)$current_portfolio1 - (int)$old_portfolio1;
                                        $opp1 = (int) $opp1 + (int)$old_portfolio_mp1;
                                        $cr1 = (int) $cr1 + ((int)$order1->share * (int)round($markets_2_way[0]->outcomes[1]->probability));
                                    }
                                }
                                $current_rate1 = (int)$cr1;
                                if($opp1!=0  && $op){
        
                                    $perv1 =  (float)$opp1 / (float)$op1 * 100;
                                }
                                else{
                                    $perv1 = 0;
                                }
        
                                $perv1 = number_format((float)$perv1, 2, '.', '');
                                if(!empty($markets_2_way)){
                                    $win_loss_probabilities = array_values(array_slice($markets_2_way, -1))[0];
                                    $win_loss_away = round($win_loss_probabilities->outcomes[1]->probability);
                                }
                                else{
                                    $win_loss_away = 0;
                                }
                                $gamedata['awayteam']=[
                                    "competitor_id"=>$competitors[1]->id,
                                    "name"=>$competitors[1]->name,
                                    "trade_rate"=>$perv1,
                                    "trade_amount"=>$current_rate1,
                                    "current_rate"=>(int)round($markets_2_way[0]->outcomes[1]->probability),
                                    "win_loss" => $win_loss_away
                                ];
                                if(!empty($markets_2_way)){
                                    $name0=$markets_2_way[0]->outcomes[0]->name;
                                    $gamedata['last_updated']=date("i:s",strtotime($markets_2_way[0]->last_updated));
                                    if($name0=='home_team_winner'){
                                        $gamedata['home_team_probability']=(int)round($markets_2_way[0]->outcomes[0]->probability);
                                        $gamedata['away_team_probability']=(int)round($markets_2_way[0]->outcomes[1]->probability);
                                    }else{
                                        $gamedata['home_team_probability']=(int)round($markets_2_way[0]->outcomes[1]->probability);
                                        $gamedata['away_team_probability']=(int)round($markets_2_way[0]->outcomes[0]->probability);
        
                                    }
                                }
                                
                            }else{
                                $orders = DB::table('orders')->where('user_id',$user->id)->where('league_id',$league_id)->where('competitor_id',$competitors[1]->id)->get();
                                $orderscount = DB::table('orders')->where('user_id',$user->id)->where('league_id',$league_id)->where('competitor_id',$competitors[1]->id)->where('txn_type','sell')->get()->count();
                                if($orderscount > 0){
                                    $orders = null;
                                }
                                $cr = 0;
                                $cp = 0;
                                $op = 0;
                                $opp = 0;
                                $opv = 0;
                                if($orders){
                                    foreach($orders as $order){
                                        $current_portfolio = (int)round($markets_2_way[0]->outcomes[0]->probability) * (int)$order->share;
                                        $cp = (int)$cp + (int) $current_portfolio;
                                        $old_portfolio = (int)$order->share_rate * (int)$order->share;
                                        $op = (int) $op + (int) $old_portfolio;
                                        $old_portfolio_mp =  (int)$current_portfolio - (int)$old_portfolio;
                                        $opp = (int) $opp + (int)$old_portfolio_mp;
                                        $cr = (int) $cr + ((int)$order->share * (int)round($markets_2_way[0]->outcomes[0]->probability));
                                    }
                                }
        
                                $current_rate = (int)$cr;
                                if($opp!=0 && $op>0){
                                    $perv =  (float)$opp / (float)$op * 100;
                                }
                                else{
                                    $perv = 0;
                                }
        
                                $perv = number_format((float)$perv, 2, '.', '');
                                if(!empty($markets_2_way)){
                                    $win_loss_probabilities = array_values(array_slice($markets_2_way, -1))[0];
                                    $win_loss_home = round($win_loss_probabilities->outcomes[0]->probability);
                                }
                                else{
                                    $win_loss_home = 0;
                                }
                                $gamedata['hometeam']=[
                                    "name"=>$competitors[1]->name,
                                    "trade_rate"=>$perv,
                                    "trade_amount"=>$current_rate,
                                    "current_rate"=>(int)round($markets_2_way[0]->outcomes[0]->probability),
                                    "win_loss"=>$win_loss_home
                                ];
                                $orders1 = DB::table('orders')->where('user_id',$user->id)->where('league_id',$league_id)->where('competitor_id',$competitors[0]->id)->get();
                                $orderscount1 = DB::table('orders')->where('user_id',$user->id)->where('league_id',$league_id)->where('competitor_id',$competitors[0]->id)->where('txn_type','sell')->get()->count();
                                if($orderscount1 > 0){
                                    $orders1 = null;
                                }
                                if($orders1){
                                    $cr1 = 0;
                                    $cp1 = 0;
                                    $op1 = 0;
                                    $opp1 = 0;
                                    $opv1 = 0;
                                    foreach($orders1 as $order1){
                                        $current_portfolio1 = (int)round($markets_2_way[0]->outcomes[1]->probability) * (int)$order1->share;
                                        $cp1 = (int)$cp1 + (int) $current_portfolio1;
                                        $old_portfolio1 = (int)$order1->share_rate * (int)$order1->share;
                                        $op1 = (int) $op1 + (int) $old_portfolio1;
                                        $old_portfolio_mp1 =  (int)$current_portfolio1 - (int)$old_portfolio1;
                                        $opp1 = (int) $opp1 + (int)$old_portfolio_mp1;
                                        $cr1 = (int) $cr1 + ((int)$order1->share * (int)round($markets_2_way[0]->outcomes[1]->probability));
                                    }
                                }
                                $current_rate1 = (int)$cr1;
                                if($opp1!=0 && $op1){
                                    $perv1 =  (float)$opp1 / (float)$op1 * 100;
                                }
                                else{
                                    $perv1 = 0;
                                }
                                $perv1 = number_format((float)$perv1, 2, '.', '');
                                if(!empty($markets_2_way)){
                                    $win_loss_probabilities = array_values(array_slice($markets_2_way, -1))[0];
                                    $win_loss_away = round($win_loss_probabilities->outcomes[0]->probability);
                                }
                                else{
                                    $win_loss_away = 0;
                                }
        
                                $gamedata['awayteam']=[
                                    "name"=>$competitors[0]->name,
                                    "trade_rate"=>$perv1,
                                    "trade_amount"=>$current_rate1,
                                    "current_rate"=>(int)round($markets_2_way[0]->outcomes[1]->probability),
                                    "win_loss"=>$win_loss_away
                                ];
                                $gamedata['home_team_probability']="NA";
                                $gamedata['away_team_probability']="NA";
                                if(!empty($markets_2_way)){
                                    $name0=$market[0]->outcomes[0]->name;
                                    if($name0=='home_team_winner'){
                                        $gamedata['home_team_probability']=(int)round($markets_2_way[0]->outcomes[0]->probability);
                                        $gamedata['away_team_probability']=(int)round($markets_2_way[0]->outcomes[1]->probability);
                                    }else{
                                        $gamedata['home_team_probability']=(int)round($markets_2_way[0]->outcomes[1]->probability);
                                        $gamedata['away_team_probability']=(int)round($markets_2_way[0]->outcomes[0]->probability);
        
                                    }
                                }
                            }
                        }
                        $gamedata['round']='1st';
                        $returndata[]=$gamedata;
                    }
                }
                $response['data']=$returndata;
            }else{
                $response['response'] = false;
                $response['message'] = 'No records found';
                $response['data'] =[];
            }
            return response()->json($response, 200);
        }else{
            return response()->json(['response'=>false,'message'=>'No records found','data'=>[]], 200);
        }
    }
    
    public function trade_detail(Request $request){
        $gamedata=[];
        $response=[];
        $user = $request->user();
        $league = League::find($request->league_id);
        if(!empty($league)){
            $isgame= DB::table("games")->where("id", $request->game_id)->first();
            if($isgame){
                $data=$this->callApi('tradedetail',null,$request->sport_event_id,null);
                if(!empty($data)){
                    $league_id=$request->league_id;
                    $game_id=$request->game_id;
                    $competitors=json_decode($data->competitors);
                    $market=json_decode($data->markets_2_way);
                    if(!empty($market) && count($market) > 1){
                        $market_array=[];
                        $market_array[]=end($market);
                        $market=$market_array;

                    }
                    $gamedata['league_id']=$league_id;
                    $gamedata['game_id']=$game_id;
                    $gamedata['sport_id']=$data->sport_id;
                    $gamedata['competition_id']=$data->competition_id;
                    $gamedata['sport_event_id']=$data->sport_event_id;
                    $gamedata['season_id']=$data->season_id;
                    $gamedata['sport_event_status']=json_decode($data->sport_event_status);

                    $gamesTable = DB::table('games')->where('sport_id',$data->sport_id)->where('competition_id',$data->competition_id)->first();
                    $gameimage = $gamesTable ? asset('uploads/images/games/'.$gamesTable->image) : '';
                    $gamedata['image']=$gameimage;
                    $qualifier0=$competitors[0]->qualifier;
                    if($qualifier0=='home'){
                        $orders = DB::table('orders')->where('user_id',$user->id)->where('league_id',$league_id)->where('sport_event_id',$data->sport_event_id)->where('competitor_id',$competitors[0]->id)->get();
                        $orderscount = DB::table('orders')->where('user_id',$user->id)->where('league_id',$league_id)->where('sport_event_id',$data->sport_event_id)->where('competitor_id',$competitors[0]->id)->where('txn_type','sell')->get()->count();
                        if($orderscount > 0){
                            $orders = null;
                        }
                        $cr = 0;
                        $cp = 0;
                        $op = 0;
                        $opp = 0;
                        $opv = 0;
                        $shres = 0;
                        if($orders){
                            foreach($orders as $order){
                                $current_portfolio = (int)round($market[0]->outcomes[0]->probability) * (int)$order->share;
                                $cp = (int)$cp + (int) $current_portfolio;
                                $old_portfolio = (int)$order->share_rate * (int)$order->share;
                                $op = (int) $op + (int) $old_portfolio;
                                $old_portfolio_mp =  (int)$current_portfolio - (int)$old_portfolio;
                                $opp = (int) $opp + (int)$old_portfolio_mp;
                                $cr = (int) $cr + ((int)$order->share * (int)round($market[0]->outcomes[0]->probability));
                                $shres = (int) $shres + (int)$order->share;
                            }
                        }
                        $current_rate = (int)$cr;
                        if($opp!=0){
                            $perv =  (float)$opp / (float)$op * 100;
                        }
                        else{
                            $perv = 0;
                        }

                        $perv = number_format((float)$perv, 2, '.', '');

                        $oldbalance = DB::table('orders')
                        ->where('user_id',$user->id)
                        ->where('league_id',$league_id)
                        ->where('game_id', '=', $game_id)
                        ->where('txn_type', '=', 'buy')
                        ->sum('share_paid_amount');
                        $portfolio_balance = (int)$league->portfolio_value -  (int)$oldbalance;
                        if(!empty($market)){
                            $win_loss_probabilities = array_values(array_slice($market, -1))[0];
                            $win_loss_home = round($win_loss_probabilities->outcomes[0]->probability);
                        }else{
                            $win_loss_home = 0;
                        }
                        $gamedata['hometeam']=[
                            "competitor_id"=>$competitors[0]->id,
                            "name"=>$competitors[0]->name,
                            "trade_amount"=>$current_rate,
                            "trade_rate"=>$perv,
                            "current_rate"=>$current_rate,
                            "buy_portfolio_value"=>(int)$portfolio_balance,
                            "sell_portfolio_value"=>(int)$oldbalance,
                            "share"=>$shres,
                            "win_loss"=> $win_loss_home
                        ];
                        $orders1 = DB::table('orders')->where('user_id',$user->id)->where('league_id',$league_id)->where('sport_event_id',$data->sport_event_id)->where('competitor_id',$competitors[1]->id)->get();
                        $orderscount1 = DB::table('orders')->where('user_id',$user->id)->where('league_id',$league_id)->where('sport_event_id',$data->sport_event_id)->where('competitor_id',$competitors[1]->id)->where('txn_type','sell')->get()->count();
                        if($orderscount1 > 0){
                            $orders1 = null;
                        }
                        if($orders1){
                            $cr1 = 0;
                            $cp1 = 0;
                            $op1 = 0;
                            $opp1 = 0;
                            $opv1 = 0;
                            $shres1 = 0;
                            if(!empty($market[0])){
                                foreach($orders1 as $order1){
                                    $current_portfolio1 = (int)round($market[0]->outcomes[1]->probability) * (int)$order1->share;
                                    $cp1 = (int)$cp1 + (int) $current_portfolio1;
                                    $old_portfolio1 = (int)$order1->share_rate * (int)$order1->share;
                                    $op1 = (int) $op1 + (int) $old_portfolio1;
                                    $old_portfolio_mp1 =  (int)$current_portfolio1 - (int)$old_portfolio1;
                                    $opp1 = (int) $opp1 + (int)$old_portfolio_mp1;
                                    $cr1 = (int) $cr1 + ((int)$order1->share * (int)round($market[0]->outcomes[1]->probability));
                                    $shres1 = (int) $shres1 + (int)$order1->share;
                                }
                            }
                        }
                        $current_rate1 = (int)$cr1;
                        if($opp1!=0){
                            $perv1 =  (float)$opp1 / (float)$op1 * 100;
                        }
                        else{
                            $perv1 = 0;
                        }

                        $perv1 = number_format((float)$perv1, 2, '.', '');
                        $oldbalance1 = DB::table('orders')
                        ->where('user_id',$user->id)
                        ->where('league_id',$league_id)
                        ->where('game_id', '=', $game_id)
                        ->where('txn_type', '=', 'buy')
                        ->sum('share_paid_amount');
                    
                        $portfolio_balance1 = (int)$league->portfolio_value - (int)$oldbalance1;

                        if(!empty($market)){
                            $win_loss_probabilities = array_values(array_slice($market, -1))[0];
                            $win_loss_away = round($win_loss_probabilities->outcomes[1]->probability);
                        }
                        else{
                            $win_loss_away = 0;
                        }
                        $gamedata['awayteam']=[
                            "competitor_id"=>$competitors[1]->id,
                            "name"=>$competitors[1]->name,
                            "trade_amount"=>$current_rate1,
                            "trade_rate"=>$perv1,
                            "current_rate"=>$current_rate1,
                            "buy_portfolio_value"=>(int)$portfolio_balance1,
                            "sell_portfolio_value"=>(int)$oldbalance1,
                            "share"=>$shres1,
                            "win_loss"=>$win_loss_away
                        ];
                        $gamedata['home_team_probability']="NA";
                        $gamedata['away_team_probability']="NA";
                        $gamedata['last_updated']="NA";
                        $gamedata['round']="1st";
                        if(!empty($market)){
                            $name0=$market[0]->outcomes[0]->name;
                            $gamedata['last_updated']=date("i:s",strtotime($market[0]->last_updated));
                            if($name0=='home_team_winner'){
                                $gamedata['home_team_probability']=(int)round($market[0]->outcomes[0]->probability);
                                $gamedata['away_team_probability']=(int)round($market[0]->outcomes[1]->probability);
                            }else{
                                $gamedata['home_team_probability']=(int)round($market[0]->outcomes[1]->probability);
                                $gamedata['away_team_probability']=(int)round($market[0]->outcomes[0]->probability);

                            }
                        }

                    }else{
                        $cr = 0;
                        $cp = 0;
                        $op = 0;
                        $opp = 0;
                        $opv = 0;
                        $shres = 0;
                        $orders = DB::table('orders')->where('user_id',$user->id)->where('league_id',$league_id)->where('competitor_id',$competitors[1]->id)->get();
                        if($orders){
                            if(!empty($market[0])){
                                foreach($orders as $order){
                                    $current_portfolio = (int)round($market[0]->outcomes[0]->probability) * (int)$order->share;
                                    $cp = (int)$cp + (int) $current_portfolio;
                                    $old_portfolio = (int)$order->share_rate * (int)$order->share;
                                    $op = (int) $op + (int) $old_portfolio;
                                    $old_portfolio_mp =  (int)$current_portfolio - (int)$old_portfolio;
                                    $opp = (int) $opp + (int)$old_portfolio_mp;
                                    $cr = (int) $cr + ((int)$order->share * (int)round($market[0]->outcomes[0]->probability));
                                    $shres = (int) $shres + (int)$order->share;
                                }
                            }
                        }

                        $current_rate = (int)$cr;
                        if($opp!=0){

                            $perv =  (float)$opp / (float)$op * 100;
                        }
                        else{
                            $perv = 0;
                        }

                        $perv = number_format((float)$perv, 2, '.', '');
                        $oldbalance = DB::table('orders')
                        ->where('user_id',$user->id)
                        ->where('league_id',$league_id)
                        ->where('game_id', '=', $game_id)
                        ->where('txn_type', '=', 'buy')
                        ->sum('share_paid_amount');
                    
                        $portfolio_balance = (int)$league->portfolio_value -  (int)$oldbalance;

                        if(!empty($market)){
                            $win_loss_probabilities = array_values(array_slice($market, -1))[0];
                            $win_loss_home = round($win_loss_probabilities->outcomes[1]->probability);
                        }
                        else{
                            $win_loss_home = 0;
                        }
                        $gamedata['hometeam']=[
                            "competitor_id"=>$competitors[1]->id,
                            "name"=>$competitors[1]->name,
                            "trade_amount"=>$current_rate,
                            "trade_rate"=>$perv,
                            "current_rate"=>$current_rate,
                            "buy_portfolio_value"=>(int)$portfolio_balance,
                            "sell_portfolio_value"=>(int)$oldbalance,
                            "share"=>$shres,
                            "win_loss" => $win_loss_home
                        ];
                        $orders1 = DB::table('orders')->where('user_id',$user->id)->where('league_id',$league_id)->where('competitor_id',$competitors[0]->id)->get();
                        if($orders1){
                            $cr1 = 0;
                            $cp1 = 0;
                            $op1 = 0;
                            $opp1 = 0;
                            $opv1 = 0;
                            $shres1 = 0;
                            if(!empty($market)){
                                foreach($orders1 as $order1){
                                    $current_portfolio1 = (int)round($market[0]->outcomes[1]->probability) * (int)$order1->share;
                                    $cp1 = (int)$cp1 + (int) $current_portfolio1;
                                    $old_portfolio1 = (int)$order1->share_rate * (int)$order1->share;
                                    $op1 = (int) $op1 + (int) $old_portfolio1;
                                    $old_portfolio_mp1 =  (int)$current_portfolio1 - (int)$old_portfolio1;
                                    $opp1 = (int) $opp1 + (int)$old_portfolio_mp1;
                                    $cr1 = (int) $cr1 + ((int)$order1->share * (int)round($market[0]->outcomes[1]->probability));
                                    $shres1 = (int) $shres1 + (int)$order1->share;
                                }
                            }
                        }

                        $current_rate1 = (int)$cr1;

                        if($opp1!=0){

                            $perv1 =  (float)$opp1 / (float)$op1 * 100;
                        }
                        else{
                            $perv1 = 0;
                        }

                        $perv1 = number_format((float)$perv1, 2, '.', '');

                        $oldbalance1 = DB::table('orders')
                        ->where('user_id',$user->id)
                        ->where('league_id',$league_id)
                        ->where('game_id', '=', $game_id)
                        ->where('txn_type', '=', 'buy')
                        ->sum('share_paid_amount');
                
                        $portfolio_balance1 = (int)$league->portfolio_value -  (int)$oldbalance1;

                        if(!empty($market)){
                            $win_loss_probabilities = array_values(array_slice($market, -1))[0];
                            $win_loss_away = round($win_loss_probabilities->outcomes[0]->probability);
                        }
                        else{
                            $win_loss_away = 0;
                        }

                        $gamedata['awayteam']=[
                            "competitor_id"=>$competitors[0]->id,
                            "name"=>$competitors[0]->name,
                            "trade_amount"=>$current_rate1,
                            "trade_rate"=>$perv1,
                            "current_rate"=>$current_rate1,
                            "buy_portfolio_value"=>(int)$portfolio_balance1,
                            "sell_portfolio_value"=>(int)$oldbalance1,
                            "share"=>$shres1,
                            "win_loss" => $win_loss_away
                        ];
                        $gamedata['home_team_probability']="NA";
                        $gamedata['away_team_probability']="NA";

                        if(!empty($market)){
                            $name0=$market[0]->outcomes[0]->name;
                            if($name0=='home_team_winner'){
                                $gamedata['home_team_probability']=(int)round($market[0]->outcomes[0]->probability);
                                $gamedata['away_team_probability']=(int)round($market[0]->outcomes[1]->probability);

                            }else{
                                $gamedata['home_team_probability']=(int)round($market[0]->outcomes[1]->probability);
                                $gamedata['away_team_probability']=(int)round($market[0]->outcomes[0]->probability);

                            }
                        }
                    }
                    $response['response'] = true;
                    $response['message'] = 'success';
                    $response['data'] =$gamedata;
                }else{
                    $response['response'] = false;
                    $response['message'] = 'Invalid Events';
                    $response['data'] =[];
                }
            }else{
                $response['response'] = false;
                $response['message'] = 'Invalid Game';
                $response['data'] =[];
            }
        }else{
            $response['response'] = false;
            $response['message'] = 'Invalid league ';
            $response['data'] =[];

        }
        return response()->json($response, 200);
    }
    
}

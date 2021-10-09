<?php
namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\League;
use App\LeagueInvitation;
use App\JoinedLeague;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use File;
use Carbon\Carbon;
use Auth;
use Edujugon\PushNotification\PushNotification;
use Twilio\Rest\Client;
use DB;
class NotificationController extends Controller
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
        $model = DB::table('notifications')->select('notifications.*',"users.image")
        ->join('users','users.id','=','notifications.user_id')->where('user_id',$user->id)->get();
        if(!empty($model)){
            foreach($model as $key=>$row){
                $model[$key]->image =asset('uploads/images/user/'.$row->image);
            }
        }
        $data['response'] = true;
        $data['message'] = 'League details';
        $data['data']= $model;
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
        $validator = validator()->make(request()->all(),[
            'name' => 'required|unique:leagues,name',
            'league_type' => 'required',
            'portfolio_value' => 'required',
            'duration' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
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

        if($request->hasFile('image')) {
            $file = $request->file('image');
            $path = public_path() . '/uploads/images/leagues/';
            $getFileExt   = $file->getClientOriginalExtension();
            $uploadedFile =   'league_'.time().'.'.$getFileExt;
            $file->move($path, $uploadedFile);
            $image = $uploadedFile;
            $modalData->image = trim($image);
       }
            $user = $request->user();
            $modalData->founder = $user->id;
            $modalData->save();

            if($request->filled('mobile_number')) {
                    $request->request->add(['league_id' => $modalData->id]);
                    $this->invite($request);
            }

            $model = League::latest()->whereNull('deleted_at')->get();
            $data['response'] = true;
            $data['message'] = 'League created successfully';
            return response()->json($data, 200);
    }

    public function invite(Request $request)
    {
        if($request->filled('mobile_number') && $request->filled('league_id')) {
            foreach($request->mobile_number as $mobile){
                $app = app();
                $invitation = $app->make('stdClass');
                $invitation->league_id = $request->league_id;
                $mobileArr = explode("-",$mobile);
                $countrycode = $mobileArr[0];
                $mobile_number = $mobileArr[1];
                $invitation->mobile_number = $countrycode.$mobile_number;
                LeagueInvitation::insert($invitation);
                $user = User::where('mobile',$mobile_number)->first();
                $league = League::find($request->league_id);
                $founder = User::find($league->founder);
                if($user){
                    // initialize message array 
                    $sid    = env( 'TWILIO_SID' );
                    $token  = env( 'TWILIO_TOKEN' );
                    $client = new Client( $sid, $token );
                    
                    $message = "You are invited to ".ucfirst($league->name)." which will start on ".date('Y-m-d',strtotime($league->created_at))." hosted by ".ucfirst($founder->first_name)." ".ucfirst($founder->last_name)." please join and play with your favourite team \r\n\r\nThanks\r\nGAMESTOCK ";
              
                    $sms_response = $client->messages->create(
                        $countrycode.$mobile_number,
                        [
                            'from' => env( 'TWILIO_FROM' ),
                            'body' => $message,
                        ]
                    );

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
                    $sms_response = $client->messages->create(
                        $countrycode.$mobile_number,
                        [
                            'from' => env( 'TWILIO_FROM' ),
                            'body' => $message
                        ]
                    );

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
            $data['response'] = true;
            $data['message'] = 'League details';
            $data['data']= $model;
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
}

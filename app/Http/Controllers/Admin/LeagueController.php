<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\League;
use App\LeagueInvitation;
use App\JoinedLeague;
use Illuminate\Support\Facades\Hash;
use File;
use Carbon\Carbon;

class LeagueController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(request()->ajax())
        {
            return datatables()->of(League::latest()->whereNull('deleted_at')->get())
                    ->addColumn('image', function ($league) { 
                        $url= $league->image?asset('/uploads/images/leagues/'.$league->image):asset('/uploads/images/leagues/default.png');
                        return '<img src="'.$url.'" border="0" width="40" class="img-rounded" align="center" />';
                    })
                    ->addColumn('founder', function ($league) { 
                        $founder = User::find($league->founder);
                        return ucfirst($founder->first_name.' '.$founder->last_name);
                    })
                    ->addColumn('portfolio_value', function ($league) { 
                        return  '$'.$league->portfolio_value;
                    })
                    ->addColumn('duration', function ($league) { 
                        $date = Carbon::parse($league->duration);
                        $now = Carbon::now();
                       // $diff = $date->diffInDays($now);
                        //$diff = Carbon::parse($league->duration)->diffForHumans(Carbon::now());
                        //$diff = $date->diffInHours($now);
                        $duration_date = new Carbon($league->duration);
                        $diff = $duration_date->diffForHumans(null, true).' left';
                        if($now > $date){
                            return 'Expired';
                        }
                        else{
                            return $diff;
                        }
                        
                    })
                    ->addColumn('action', function($data){
                        $button = '<div class="text-center"><button type="button" name="edit" id="'.$data->id.'" onclick="editRow(\''.addslashes($data->id).'\')" class="edit btn btn-primary btn-sm">Edit</button>';
                        $button .= '&nbsp;&nbsp;';
                        $button .= '<button type="button" name="delete" id="'.$data->id.'" onclick="deleteRow('.$data->id.')" class="delete btn btn-danger btn-sm">Delete</button>';
                        $button .= '&nbsp;&nbsp;';
                        $button .= '&nbsp;&nbsp;</div>';
                        return $button;
                    })
                    ->rawColumns(['image','founder','duration','portfolio_value','action'])
                    ->make(true);
        }
        return view('admin.leagues.league-list');
    }


    public function leagueInvitations()
    {
        if(request()->ajax())
        {
            return datatables()->of(LeagueInvitation::latest()->get())
                    ->addColumn('league', function ($leagueinvitation) { 
                        $league = League::find($leagueinvitation->league_id);
                        return ucfirst($league->name);
                    })
                    ->addColumn('founder', function ($leagueinvitation) { 
                        $league = League::find($leagueinvitation->league_id);
                        $founder = User::find($league->founder);
                        return ucfirst($founder->first_name.' '.$founder->last_name);
                    })
                    ->addColumn('created_at', function ($leagueinvitation) { 
                        $invitation_date = date('Y-m-d H:i:s',strtotime($leagueinvitation->created_at));
                        return ucfirst($invitation_date);
                    })
                    ->addColumn('action', function($data){
                        $button = '<div class="text-center">';
                        $button .= '<button type="button" name="delete" id="'.$data->id.'" onclick="deleteRow('.$data->id.')" class="delete btn btn-danger btn-sm">Delete</button>';
                        $button .= '&nbsp;&nbsp;</div>';
                        return $button;
                    })
                    ->rawColumns(['league','founder','action'])
                    ->make(true);
        }
        return view('admin.leagues.league-invitation-list');
    }

    public function leagueJoined()
    {
        if(request()->ajax())
        {
            return datatables()->of(JoinedLeague::latest()->get())
                    ->addColumn('league', function ($joinedleague) { 
                        $league = League::find($joinedleague->league_id);
                        return ucfirst($league->name);
                    })
                    ->addColumn('user', function ($joinedleague) { 
                        $user = User::find($joinedleague->user_id);
                        return ucfirst($user->first_name.' '.$user->last_name);
                    })
                    ->addColumn('created_at', function ($joinedleague) { 
                        $joined_date = date('Y-m-d H:i:s',strtotime($joinedleague->created_at));
                        return ucfirst($joined_date);
                    })
                    ->addColumn('action', function($data){
                        $button = '<div class="text-center">';
                        $button .= '<button type="button" name="delete" id="'.$data->id.'" onclick="deleteRow('.$data->id.')" class="delete btn btn-danger btn-sm">Delete</button>';
                        $button .= '&nbsp;&nbsp;</div>';
                        return $button;
                    })
                    ->rawColumns(['league','user','action'])
                    ->make(true);
        }
        return view('admin.leagues.league-joined-list');
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
            'name' => 'required',
            'league_type' => 'required',
            'portfolio_value' => 'required',
            'duration' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'password' => 'sometimes',
        ]);

        

        if ($validator->fails()) {    
            return response()->json([ "statusCode"=>201,'error'=>$validator->messages()], 200);
        }

        $request->except('_token');

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
        
         $modalData->founder = auth()->id();
         $modalData->save();
        return response()->json([ "statusCode"=>200,'data'=>$request->all()], 200);
    }


    public function leagueDetail(Request $request)
    {
        $data = League::find($request->id);
        return response()->json($data);
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
    public function updateLeague(Request $request)
    {
        $validator = validator()->make(request()->all(),[
            'eid' => 'required',
            'ename' => 'required',
            'eleague_type' => 'required',
            'eportfolio_value' => 'required',
            'eduration' => 'required',
            'eimage' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'epassword' => 'sometimes',
        ]);

        

        if ($validator->fails()) {    
            return response()->json([ "statusCode"=>201,'error'=>$validator->messages()], 200);
        }

        $request->except('_token');

        $modalData = League::find($request->eid);
        if($request->filled('ename')) $modalData->name = $request->ename;
        if($request->filled('eleague_type')) $modalData->league_type = $request->eleague_type;
        if($request->filled('eportfolio_value')) $modalData->portfolio_value = $request->eportfolio_value;
        if($request->filled('eduration')) $modalData->duration = $request->eduration;
      
        if($request->filled('epassword')) {
            if (trim($request->epassword) != '') {
                $modalData->password = trim($request->epassword);
             }
        }

        if($request->hasFile('eimage')) {
            $file = $request->file('eimage');
            $path = public_path() . '/uploads/images/leagues/';
            $getFileExt   = $file->getClientOriginalExtension();
            $uploadedFile =   'league_'.time().'.'.$getFileExt;
            $file->move($path, $uploadedFile);
            $image = $uploadedFile;
            $modalData->image = trim($image);
       }
        
      
         $modalData->save();
        return response()->json([ "statusCode"=>200,'data'=>$request->all()], 200);
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

    public function leagueinvitationdelete(Request $request)
    {
        $validator = validator()->make(request()->all(),[
            'id' => 'required',
        ]);

        

        if ($validator->fails()) {    
            return response()->json([ "statusCode"=>201,'error'=>$validator->messages()], 200);
        }
        LeagueInvitation::find($request->id)->delete();
        return response()->json(['success'=>'League invitation deleted successfully.']);
    }

    public function leaguejoineddelete(Request $request)
    {
        $validator = validator()->make(request()->all(),[
            'id' => 'required',
        ]);

        

        if ($validator->fails()) {    
            return response()->json([ "statusCode"=>201,'error'=>$validator->messages()], 200);
        }
        JoinedLeague::find($request->id)->delete();
        return response()->json(['success'=>'League joined deleted successfully.']);
    }
}

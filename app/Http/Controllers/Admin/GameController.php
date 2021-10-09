<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\League;
use App\LeagueInvitation;
use App\JoinedLeague;
use App\Game;
use Illuminate\Support\Facades\Hash;
use File;
use Carbon\Carbon;

class GameController extends Controller
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
            return datatables()->of(Game::latest()->get())
                    ->addColumn('image', function ($game) { 
                        $url= $game->image?asset('/uploads/images/games/'.$game->image):asset('/uploads/images/games/default.png');
                        return '<img src="'.$url.'" border="0" width="40" class="img-rounded" align="center" />';
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
        return view('admin.games.games-list');
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

    
}

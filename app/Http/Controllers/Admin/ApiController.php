<?php

namespace App\Http\Controllers\Admin;

use App\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApiController extends Controller
{
     /**
     * Create a new controller instance.
     *
     * @return void
     */
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
            return datatables()->of(Api::latest()->whereNull('deleted_at')->get())
                    ->addColumn('action', function($data){
                        $button = '<div class="text-center"><button type="button" name="edit" id="'.$data->id.'" onclick="editRow(\''.addslashes($data->id).'\',\''.addslashes($data->name).'\',\''.addslashes($data->description).'\',\''.addslashes($data->key).'\')" class="edit btn btn-primary btn-sm">Edit</button>';
                        $button .= '&nbsp;&nbsp;';
                        $button .= '<button type="button" name="delete" id="'.$data->id.'" onclick="deleteRow('.$data->id.')" class="delete btn btn-danger btn-sm">Delete</button>';
                        $button .= '&nbsp;&nbsp;';
                        //$button .= '<button type="button" name="edit" id="'.$data->id.'" class="edit btn btn-primary btn-sm">List Apis</button>';
                        $button .= '&nbsp;&nbsp;</div>';
                        return $button;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
        return view('admin.apis.api-list');
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
            'name' => 'required|unique:apis,name',
            'key' => 'required',
        ]);

        $request->request->add(['slug' => str_slug($request->name, '-')]);

        if ($validator->fails()) {    
            return response()->json([ "statusCode"=>201,'error'=>$validator->messages()], 200);
        }
        //Api::create($request->all());
        Api::updateOrCreate(['id' => $request->id],
        ['name' => $request->name,'description' => $request->description, 'key' => $request->key,'slug' => str_slug($request->name, '-')]);
        return response()->json([ "statusCode"=>200,'data'=>$request->all()], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Api  $api
     * @return \Illuminate\Http\Response
     */
    public function show(Api $api)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Api  $api
     * @return \Illuminate\Http\Response
     */
    public function edit(Api $api)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Api  $api
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Api $api)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Api  $api
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Api::find($id)->delete();
        return response()->json(['success'=>'Api deleted successfully.']);
    }
}

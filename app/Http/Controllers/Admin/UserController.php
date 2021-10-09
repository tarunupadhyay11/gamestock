<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use File;

class UserController extends Controller
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
            return datatables()->of(User::latest()->whereNull('deleted_at')->where('id','!=',1)->get())
                    ->addColumn('action', function($data){
                        $button = '<div class="text-center"><button type="button" name="edit" id="'.$data->id.'" onclick="editRow(\''.addslashes($data->id).'\',\''.addslashes($data->first_name).'\',\''.addslashes($data->last_name).'\',\''.addslashes($data->mobile).'\',\''.addslashes($data->email).'\')" class="edit btn btn-primary btn-sm">Edit</button>';
                        $button .= '&nbsp;&nbsp;';
                        $button .= '<button type="button" name="delete" id="'.$data->id.'" onclick="deleteRow('.$data->id.')" class="delete btn btn-danger btn-sm">Delete</button>';
                        $button .= '&nbsp;&nbsp;';
                        $button .= '&nbsp;&nbsp;</div>';
                        return $button;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
        return view('admin.users.users-list');
    }

    public function profile()
    {
        return view('admin.users.profile');
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $this->validate($request, [
            'first_name' => 'required',
            'email' => 'unique:users,email,'.$user->id,
            'country_code' => 'required',
            'mobile' => 'required|unique:users,mobile,'.$user->id,
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
           // 'mobile' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
         ]);

       
       
       if($request->filled('username')) $user->username = $request->username;
       if($request->filled('first_name')) $user->first_name = $request->first_name;
       $user->last_name = $request->last_name;
       if($request->filled('country_code')) $user->country_code = $request->country_code;
       if($request->filled('mobile')) $user->mobile = $request->mobile;
       if($request->filled('email')) $user->email = $request->email;
       $user->address = $request->address;
       if($request->filled('account_type')) $user->account_type = $request->account_type;

       if($request->hasFile('image')) {
            $file = $request->file('image');
            $path = public_path() . '/uploads/images/user/';
            $getFileExt   = $file->getClientOriginalExtension();
            $uploadedFile =   'profile_'.time().'.'.$getFileExt;
            if($file->move($path, $uploadedFile)){
                $image_path = $path.$request->user()->image;
                if (File::exists($image_path)) {
                    File::delete($image_path);
                }
            }
            $image = $uploadedFile;

            $user->image = trim($image);
       }
       
       if($request->filled('password')) {
        if (trim($request->password) != '') {
            $user->password = trim($request->password);
         }
       }
        
        $user->save();
        return back()->with('success', 'Profile updated successfully');
    }

    public function changePassword()
    {
        return view('admin.users.change-password');
    }

    public function updatePassword(Request $request)
    {
        $this->validate($request, [
            'current_password' => 'required|password',
            'new_password' => 'required|string|min:8',
            'confirm_password' => 'required|same:new_password',
         ]);


        $user = auth()->user();
        if($user){
            if (trim($request->new_password) != '') {
                $user->password = trim($request->new_password);
                $user->save();
            }
        }
        
        return back()->with('success', 'Password changed successfully');
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
            'first_name' => 'required',
            'mobile' => 'required',
            'email' => 'required|email|unique:users,email,'.$request->id,
            'password' => 'sometimes',
        ]);

        

        if ($validator->fails()) {    
            return response()->json([ "statusCode"=>201,'error'=>$validator->messages()], 200);
        }
        //User::create($request->all());
        $userData = $request->only(["first_name","last_name","mobile","email"]);
        if (trim($request->password) != '') {
            $userData['password'] = trim($request->password);
         }

        User::updateOrCreate(['id' => $request->id],$userData);
        return response()->json([ "statusCode"=>200,'data'=>$request->all()], 200);
        return response()->json([ "statusCode"=>200,'data'=>$request->all()], 200);
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
        User::find($id)->delete();
        return response()->json(['success'=>'User deleted successfully.']);
    }
}

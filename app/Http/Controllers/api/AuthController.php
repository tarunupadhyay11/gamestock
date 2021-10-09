<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Auth;
use Illuminate\Support\Facades\Password;
use UniqueCode;
use File;
use Twilio\Rest\Client;
use Illuminate\Validation\Rule;

use DB;
class AuthController extends Controller
{

    public function login(Request $request)
    {
        try {
            // $request->validate([
            // 'mobile' => 'sometimes',
            // 'email' => 'sometimes',
            // 'password' => 'sometimes',
            // 'device_token' => 'required'
            // ]);

            $validator = validator()->make(request()->all(),[
                'mobile' => 'sometimes',
                'email' => 'sometimes',
                'password' => 'sometimes',
                'device_token' => 'required'
            ]);

            if ($validator->fails()) {
                $data['response'] = false;
                $data['message'] = 'Mobile or Password incorrect';
                $data['data']= $validator->messages();
                return response()->json($data);
          }

            if($request->email){
                $credentials = request(['email', 'password']);
            }
            else{
                $credentials = request(['mobile', 'password']);
            }

            if (!Auth::attempt($credentials)) {
                $data['response'] = false;
                $data['message'] = 'Mobile or Password incorrect';
            return response()->json($data);
            }
            if($request->email){
             $user = User::where('email', $request->email)->first();
            }
            else{
                $user = User::where('mobile', $request->mobile)->first();
            }

            if(!$user){
                $data['response'] = false;
                $data['message'] = 'No user found';
                return response()->json($data);
            }
            else{
                if(!$user->mobile_verified){
                    $data['response'] = false;
                    $data['isNotVerified'] = true;
                    $data['message'] = 'Mobile number is unverified';
                    return response()->json($data);
                }else{
                    if ( ! Hash::check($request->password, $user->password, [])) {
                        throw new \Exception('Error in Login');
                    }
                    $user->tokens()->delete();
                    $tokenResult = $user->createToken('authToken')->plainTextToken;
                    $user->device_token =$request->device_token;
                    $user->save();
                    $data['response'] = true;
                    $data['message'] = 'Logged in successfully';
                    $data['data'] = $user;
                    $data['data']['token'] = $tokenResult;
                    $data['data']['token_type'] = 'Bearer';
                    return response()->json($data,200);
                }
            }


        } catch (Exception $error) {
            $data['response'] = false;
            $data['message'] = 'Logged in failed';
            $data['data'] = $error;
            return response()->json($data);
        }
    }

    public function userLogout(Request $request)
    {
        $user = $request->user();
        $user->device_token = null;
        $user->save();
        $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();
        $data['response'] = true;
        $data['message'] = 'Logout succesfully.';
        return response()->json($data, 200);

    }


    public function register(Request $request)
    {
        $validator = validator()->make(request()->all(),[
            'country_code' => 'required',
            'mobile' => 'required|unique:users,mobile',
            'password' => 'sometimes',
            'device_token' => 'required',
        ]);



        if ($validator->fails()) {
                $data['response'] = false;
                $data['message'] = 'Error';
                $data['data']= $validator->messages();
                return response()->json($data);
        }

        $user = User::where('mobile', $request->mobile)->first();

        if($user){
            $data['response'] = false;
            $data['message'] = 'Mobile number already exists';
            return response()->json($data, 200);
        }
        else{
            $otp = UniqueCode::OTP(5);
            $mobile = $request->country_code.$request->mobile;
            $sid    = env( 'TWILIO_SID' );
            $token  = env( 'TWILIO_TOKEN' );
            $client = new Client( $sid, $token );
            $message = 'Your one time password (OTP) is '.$otp;

            try {
                $sms_response = $client->messages->create(
                    $mobile,
                    [
                        'from' => env( 'TWILIO_FROM' ),
                        'body' => $message,
                    ]
                );
                $request->request->add(['mobile_verification_code' => $otp]);
               // request()->merge([ 'mobile_verification_code' => '11111' ]);
                User::create($request->all());
                $data['response'] = true;
                $data['message'] = 'Verification code sent via SMS succesfully.';
                $data['data'] = ['otp'=>$otp];
                return response()->json($data, 200);

            } catch (\Exception $e) {
                $data['response'] = false;
                $data['message'] = 'Unverfied mobile number';
                return response()->json($data,200);
            }


        }

       // $request->request->add(['email' => '']);

    }

    public function verifyOTP(Request $request) {
        $credentials = request()->validate([
            'otp' => 'required',
        ]);

        $user = User::where('mobile_verification_code', $request->otp)->first();
        if($user){
            User::where('mobile_verification_code', $request->otp)->update(['mobile_verification_code' => null,'mobile_verified' => 1]);

            //
            $authuser = User::find($user->id);
            Auth::login($authuser, true);
            $tokenResult = $authuser->createToken('authToken')->plainTextToken;
            $data['response'] = true;
            $data['message'] = 'Logged in successfully';
            $data['data'] = $authuser;
            $data['data']['token'] = $tokenResult;
            $data['data']['token_type'] = 'Bearer';
            return response()->json($data);
        }
        else{
            $data['response'] = false;
            $data['message'] = 'Invalid otp';
            return response()->json($data);
        }

        // $reset_password_status = Password::reset($credentials, function ($user, $password) {
        //     $user->password = $password;
        //     $user->save();
        // });

        // if ($reset_password_status == Password::INVALID_TOKEN) {
        //     $data['response'] = true;
        //     $data['message'] = 'Invalid token provided';
        //     return response()->json($data, 400);
        // }

    }

    public function forgot(Request $request) {
        $credentials = request()->validate(['mobile' => 'required']);


        $user = User::where('mobile', $request->mobile)->first();
        //$credentials = request()->validate(['email' => 'required|email']);
        //Password::sendResetLink($credentials);
        //$data['message'] = 'Reset password link sent on your email id.';
        if($user){
            $otp = UniqueCode::OTP(5);
            $mobile = $user->country_code.$user->mobile;
            $sid    = env( 'TWILIO_SID' );
            $token  = env( 'TWILIO_TOKEN' );
            $client = new Client( $sid, $token );
            $message = 'Your one time password (OTP) is '.$otp;

            try {
                $sms_response = $client->messages->create(
                    $mobile,
                    [
                        'from' => env( 'TWILIO_FROM' ),
                        'body' => $message,
                    ]
                );

                User::where('mobile', $request->mobile)->update(['mobile_verification_code' => $otp]);
                $data['response'] = true;
                $data['otp'] = $otp;
                $data['message'] = 'Verification code sent via SMS succesfully.';

            } catch (\Exception $e) {
                $data['response'] = false;
                $data['message'] = 'Unverfied mobile number';
                return response()->json($data,200);
            }
            // User::where('mobile', $request->mobile)->update(['mobile_verification_code' => $otp]);
            // $data['response'] = true;
            // $data['otp'] = $otp;
            // $data['message'] = 'Verification code sent via SMS succesfully.';
            // return response()->json($data,200);
        }else{
            $data['response'] = false;
            $data['message'] = 'Mobile number does not exists';
            return response()->json($data,200);
        }

    }

    public function reset(Request $request) {
        $validator = validator()->make(request()->all(),[
            'otp' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            $data['response'] = false;
            $data['message'] = 'OTP or Password incorrect';
            $data['data']= $validator->messages();
            return response()->json($data);
       }

        $user = User::where('mobile_verification_code', $request->otp)->first();
        if($user){
            $password = Hash::make($request->password);
            User::where('mobile_verification_code', $request->otp)->update(['password' => $password]);
            $data['response'] = true;
            $data['message'] = 'Password has been successfully changed';
            return response()->json($data);
        }else{
            $data['response'] = true;
            $data['message'] = 'Mobile number does not exits';
            return response()->json($data);
        }

        // $credentials = request()->validate([
        //     'email' => 'required|email',
        //     'token' => 'required|string',
        //     'password' => 'required|string|confirmed'
        // ]);

        // $reset_password_status = Password::reset($credentials, function ($user, $password) {
        //     $user->password = $password;
        //     $user->save();
        // });

        // if ($reset_password_status == Password::INVALID_TOKEN) {
        //     $data['response'] = true;
        //     $data['message'] = 'Invalid token provided';
        //     return response()->json($data, 400);
        // }

    }

    public function userDetail(Request $request)
    {
        $user = $request->user();
        $data['response'] = true;
        $data['message'] = 'User data';
        $data['data'] = $user;
        return response()->json($data, 200);
    }

    public function userUpdate(Request $request)
    {
        $autuser = $request->user();
        $validator = validator()->make(request()->all(),[
            'email' => 'unique:users,email,'.$autuser->id,
        ]);

        if ($validator->fails()) {
            $data['response'] = false;
            $data['message'] = 'Email address required';
            $data['data']= $validator->messages();
            return response()->json($data);
       }


       $user = User::find($autuser->id);

       if($user){
        // $user = new User();
        // $user->exists = true;
        // $user->id = $autuser->id; //already exists in database.
        if($request->filled('username')) $user->username = $request->username;
        if($request->filled('first_name')) $user->first_name = $request->first_name;
        if($request->filled('last_name')) $user->last_name = $request->last_name;
        if($request->filled('email')) $user->email = $request->email;
        if($request->filled('account_type')) $user->account_type = $request->account_type;

        if($request->filled('password')) {
         if (trim($request->password) != '') {
             $user->password = trim($request->password);
          }
        }

         $user->save();
         //if(!$user->email_verified_at) $user->sendEmailVerificationNotification();

        $data['response'] = true;
        $data['message'] = 'User updated successfully';
        $data['data'] = $user;
        return response()->json($data, 200);
       }
       else{
        $data['response'] = true;
        $data['message'] = 'No user found';
        $data['data'] = '';
        return response()->json($data, 200);
       }


    }

    public function changePassword(Request $request)
    {
        $validator = validator()->make(request()->all(),[
            'current_password' => 'required|password',
            'new_password' => 'required|string|min:8',
            'confirm_password' => 'required|same:new_password',
        ]);


        if ($validator->fails()) {
            $data['response'] = false;
            $data['message'] = 'Current password incorrect or Confirm password mismatch';
            $data['data']= $validator->messages();
            return response()->json($data);
       }

        $user = $request->user();
        if($user){
            if (trim($request->new_password) != '') {
                $user->password = trim($request->new_password);
                $user->save();
            }
        }

        $data['response'] = true;
        $data['message'] = 'Password changed successfully';
        $data['data'] = $user;
        return response()->json($data, 200);
    }

    public function resendOTP(Request $request)
    {
        $validator = validator()->make(request()->all(),[
            'country_code' => 'required',
            'mobile' => 'required',
        ]);

            if ($validator->fails()) {
                $data['response'] = false;
                $data['message'] = 'Mobile and Country Code required';
                $data['data']= $validator->messages();
                return response()->json($data);
           }

            $otp = UniqueCode::OTP(5);
            $mobile = $request->country_code.$request->mobile;
            $sid    = env( 'TWILIO_SID' );
            $token  = env( 'TWILIO_TOKEN' );
            $client = new Client( $sid, $token );
            $message = 'Your one time password (OTP) is '.$otp;

            try {
                $user = User::where('mobile', $request->mobile)->first();
                if($user){
                    $sms_response = $client->messages->create(
                        $mobile,
                        [
                            'from' => env( 'TWILIO_FROM' ),
                            'body' => $message,
                        ]
                    );
                    $user->mobile_verification_code = trim($otp);
                    $user->save();
                    $data['response'] = true;
                    $data['message'] = 'Verification code sent via SMS succesfully.';
                    $data['data'] = ['otp'=>$otp];
                    return response()->json($data, 200);
                }
                else{
                    $data['response'] = true;
                    $data['message'] = 'Mobile number does not exits.';
                    $data['data'] = ['otp'=>$otp];
                    return response()->json($data, 200);
                }

            } catch (\Exception $e) {
                $data['response'] = false;
                $data['message'] = 'Unverfied mobile number';
                return response()->json($data,200);
            }




    }

    public function uplodadProfileImage(Request $request) {

        $validator = validator()->make(request()->all(),[
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);


        if ($validator->fails()) {
            $data['response'] = false;
            $data['message'] = 'Image is required';
            $data['data']= $validator->messages();
            return response()->json($data);
       }

        if(!$request->hasFile('image')) {
            return response()->json(['upload_file_not_found'], 400);
        }
        $file = $request->file('image');
        if(!$file->isValid()) {
            return response()->json(['invalid_file_upload'], 400);
        }
        $path = public_path() . '/uploads/images/user/';
        $getFileExt   = $file->getClientOriginalExtension();
        $uploadedFile =   time().'.'.$getFileExt;
        if($file->move($path, $uploadedFile)){
            $image_path = $path.$request->user()->image;
            if (File::exists($image_path)) {
                File::delete($image_path);
             }
        }
        $image = $uploadedFile;
        $user = $request->user();
        $user->image = trim($image);
        $user->save();
        $data['response'] = true;
        $data['message'] = 'Profile image uploaded successfully.';
        $data['data'] = $user;
        return response()->json($data, 200);
     }
}

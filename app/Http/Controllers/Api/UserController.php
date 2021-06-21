<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Store;
use App\Models\Purchase;
use Validator;
use Image;
use QrCode;
use App\Services\SmsService;
use App\Http\Resources\Shop\ProfileResource;

class UserController extends Controller
{
    //API Login
    public function login(Request $request)
    {

    	$validator = Validator::make($request->all(), [
            'phone' => 'required|numeric',
            'password' => 'required|string'
        ]);

        if ($validator->fails())
		{
			$message = $validator->errors()->first();
		    return response()->json(['statusCode'=>200,'success'=>false,'message'=>$message], 200);
		}
		if(Auth::attempt(['phone' => $request->phone, 'password' => $request->password,'role'=>'SHOPKEEPER']))
        {
            $user = Auth::User();
            $tokenData =  $user->createToken('MyShopApp');
            $token = $tokenData->token;
            $user->accessToken = $tokenData->accessToken;
            return response()->json(['statusCode'=>200,'success'=>true,'message'=>'User Login','data' => $user],200);
        }
        else
        {
            return response()->json(['statusCode'=>200,'success'=>false,'message'=>'authentication failed.'], 200);
        }
    }

    //Function for user registeration
   	public function register(Request $request)
	{
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'email|unique:users',
            'phone'=>'required|numeric|unique:users',
            'password' => 'required',
            'c_password' => 'required|same:password',
            'lat' => 'required',
            'lng' => 'required',
            'otp'=>'required|numeric'
        ]);

		if ($validator->fails())
		{
			$message = $validator->errors()->first();
		    return response()->json(['statusCode'=>200,'success'=>false,'message'=>$message], 200);
		}

        $checkOtp = SmsService::verifyOTP($request->phone,$request->otp);
        if(!$checkOtp) {
            return response()->json(['statusCode'=>200,'success'=>false,'message'=>'Invalid OTP', 'otp_error' => true], 200);
        }

        $user = new User();
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->email = $request->email;
        $user->lat = $request->lat;
        $user->lng = $request->lng;
        $user->role = 'SHOPKEEPER';
        $user->password = bcrypt($request->password);
        $user->save();
        $tokenData =  $user->createToken('MyShopApp');
        $user->accessToken = $tokenData->accessToken;
		return response()->json(['statusCode'=>200,'success'=>true,'message'=>'User Successfully Registered','data'=>$user], 200);
	}

    //User Logout
    public function logout()
    {
        $user = Auth::User()->token();
        if( $user->revoke() )
        {
            return response()->json(['statusCode'=>200,'success'=>true,'message'=>'User Successfully Logout'], 200);
        }
    }

    //update user shop profile
    public function updateShopProfile(Request $request)
    {
        $user = Auth::User();
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'logo'=> 'image|max:2048'
        ]);

        if ($validator->fails())
        {
            $message = $validator->errors()->first();
            return response()->json(['statusCode'=>200,'success'=>false,'message'=>$message], 200);
        }

        $store = Store::where('user_id',$user->id)->first();
        if(!$store)
        {
            $store = new Store();
        }

        $store->user_id = $user->id;
        $store->name = $request->name;
        $store->slug =  Str::slug($request->name);
        $store->address = $request->address;
        $store->about = $request->about;
        $store->registration_date = $request->registration_date;
        $store->open_at = $request->open_at;
        $store->close_at = $request->close_at;

        if($request->hasFile('logo') )
        {
            $store->logo = saveFile(config("constants.disks.STORE"), $store->slug, $request->file('logo'), true);
        }

        if ( $store->save() )
        {
            return response()->json(['statusCode'=>200,'success'=>true,'message'=>'User Shop Profile updated'],200);
        }
        else
        {
            return response()->json(['statusCode'=>200,'success'=>false,'message'=>'Oops! Something went wrong.Please try again later.'],200);
        }
    }

    //Update user profile
    public function updateProfile(Request $request)
    {
        $user = Auth::User();
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'phone'=> 'required|numeric|unique:users,phone,'.$user->id,
            'password' => 'nullable',
            'c_password' => 'nullable|same:password',
            'image'=> 'image|max:2048'
        ]);

        if ($validator->fails())
        {
            $message = $validator->errors()->first();
            return response()->json(['statusCode'=>200,'success'=>false,'message'=>$message], 200);
        }

        if($request->hasFile('image') ) {
            $imageName = saveFile(config("constants.disks.PROFILE"), 'profile', $request->file('image'), true);
        }
        $user = User::where('id',$user->id)->first();
        // $user = Auth::guard('api')->user();
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->email = $request->email;
        $user->image = $imageName ?? null;
        if( !empty($request->password) )
        {
            $user->password = bcrypt($request->password);
            // $user->token()->revoke();
            // $token = $user->createToken('MyShopApp')->accessToken;
        }

        if ( $user->save() )
        {
            return response()->json(['statusCode'=>200,'success'=>true,'message'=>'User Profile updated','data'=> $user],200);
        }
        else
        {
            return response()->json(['statusCode'=>200,'success'=>false,'message'=>'Oops! Something went wrong.Please try again later.'],200);
        }
    }

    //Get user profile data.
    public function getUserProfile()
    {
        $user = Auth::User();
        $data = User::with('store')->where('id',$user->id)->first();

        if(!$data) {
            return response()->json(['statusCode'=>200,'success'=>false,'message'=>'User not found.'],200);
        }
        return (new ProfileResource($data))->additional([
            "message" => "User Profile",
        ]);
    }

    //This function is used to confirm user password for changing settings.
    public function confirmPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required',
        ]);

        if ($validator->fails())
        {
            $message = $validator->errors()->first();
            return response()->json(['statusCode'=>200,'success'=>false,'message'=>$message], 200);
        }

        $user = Auth::User();
        if( Hash::check($request->password,$user->password) )
        {
            return response()->json(['statusCode'=>200,'success'=>true,'message'=>'password match'],200);
        }
        else
        {
            return response()->json(['statusCode'=>200,'success'=>false,'message'=>'password did not match'],200);
        }
    }

    public function getShopQR()
    {

        $user = Auth::user();
        $user->load('store');
        if(empty($user->store)){
          abort(404);
        }
        $url = config("constants.ECOM_APP_URL").$user->id.'-'.$user->store->slug;
        $qr = QrCode::size(500)->format('png')->generate($url);

        header('Content-Type: image/png');
        header('Content-Disposition: attachment; filename="myshopQR.png"');
        echo $qr; exit();
    }
}

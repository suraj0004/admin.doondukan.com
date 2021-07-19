<?php

namespace App\Http\Controllers\Api\Ecommerce;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserAddress;
use Validator;
use App\Http\Requests\Api\Ecommerce\UpdateProfileRequest;
use App\Services\SmsService;

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
        if(Auth::attempt(['phone' => $request->phone, 'password' => $request->password,'role'=>'USER']))
        {
            $user = Auth::User();
            $token =  $user->createToken('MyShopApp')->accessToken;

            return response()->json([
                'statusCode'=>200,
                'success'=>true,
                'message'=>'User Login',
                'data' => [
                    "user" => $user,
                    "token" => $token
                ]
            ],200);
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
            'phone'=>'required|numeric|unique:users,phone',
            'password' => 'required',
            'c_password' => 'required|same:password',
            'otp'=>'required|numeric'
        ]);

        if ($validator->fails())
        {
            $message = $validator->errors()->first();
            return response()->json(['statusCode'=>200,'success'=>false,'message'=>$message], 200);
        }
        $checkOtp = SmsService::verifyOTP($request->phone,$request->otp);
        if(!$checkOtp) {
            return response()->json(['statusCode'=>200,'success'=>false,'message'=>'Invalid OTP'], 200);
        }
        $user = new User();
        $user->phone = $request->phone;
        $user->role = 'USER';
        $user->password = bcrypt($request->password);
        $user->save();
        $token =  $user->createToken('MyShopApp')->accessToken;
        return response()->json([
            'statusCode'=>200,
            'success'=>true,
            'message'=>
            'User Successfully Registered',
            'data' => [
                "user" => $user,
                "token" => $token
            ]
        ], 200);
    }

    public function logout()
    {
        $user = Auth::User()->token();
        if( $user->revoke() )
        {
            return response()->json(['statusCode'=>200,'success'=>true,'message'=>'User Successfully Logout'], 200);
        }
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = Auth::user();
        $user->update($request->getData());
        return response()->json([
            'statusCode'=>200,
            'success'=>true,
            'message'=>'Profile updated successfully.',
            'data'=>$user
        ], 200);
    }

    public function addUserAddress(Request $request) 
    {
         $validator = Validator::make($request->all(), [
            'mobile'=>'required|numeric|digits:10',
            'name' => 'required|string',
            'city' => 'required|string',
            'state'=>'required|string',
            'pincode'=>'required|numeric|digits:6',
            'address'=>'required|string'
        ]);

        if ($validator->fails()) {
            $message = $validator->errors()->first();
            return response()->json(['statusCode'=>200,'success'=>false,'message'=>$message], 200);
        }
        $user = Auth::User();

        $userAddress = new UserAddress();
        $userAddress->user_id = $user->id;
        $userAddress->mobile = $request->mobile;
        $userAddress->name = $request->name;
        $userAddress->city = $request->city;
        $userAddress->state = $request->state;
        $userAddress->pincode = $request->pincode;
        $userAddress->address = $request->address;
        
        if($userAddress->save()) {
            return response()->json(['statusCode'=>200,'success'=>true,'message'=>'Address added successfully.'], 200);
        }
        
        return response()->json(['statusCode'=>200,'success'=>false,'message'=>'Oops! Something thing went wrong. Please try again later.'], 200);
    }

    public function updateUserAddress(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'address_id'=>'required',
            'mobile'=>'required|numeric|digits:10',
            'name' => 'required|string',
            'city' => 'required|string',
            'state'=>'required|string',
            'pincode'=>'required|numeric|digits:6',
            'address'=>'required|string'
        ]);

        if ($validator->fails()) {
            $message = $validator->errors()->first();
            return response()->json(['statusCode'=>200,'success'=>false,'message'=>$message], 200);
        }
        
        $user = Auth::User();
        $userAddress = UserAddress::where('user_id',$user->id)
                      ->where('id',$request->address_id)->first();
        
        if(!$userAddress) {
            return response()->json(['statusCode'=>200,'success'=>false,'message'=>'Address not found.'], 200);
        }
        
        $userAddress->user_id = $user->id;
        $userAddress->mobile = $request->mobile;
        $userAddress->name = $request->name;
        $userAddress->city = $request->city;
        $userAddress->state = $request->state;
        $userAddress->pincode = $request->pincode;
        $userAddress->address = $request->address;
        
        if($userAddress->save()) {
            return response()->json(['statusCode'=>200,'success'=>true,'message'=>'Address updated successfully.'], 200);
        }
        
        return response()->json(['statusCode'=>200,'success'=>false,'message'=>'Oops! Something thing went wrong. Please try again later.'], 200);
    }

    public function deleteAddress($id) 
    {
        
        $user = Auth::User();
        $userAddress = UserAddress::where('user_id',$user->id)
                       ->where('id',$id)->first();
        
        if(!$userAddress) {
            return response()->json(['statusCode'=>200,'success'=>false,'message'=>'Address not found.'], 200);
        }

        if($userAddress->delete()) {
            return response()->json(['statusCode'=>200,'success'=>true,'message'=>'Address deleted succefully.'], 200);
        }

        return response()->json(['statusCode'=>200,'success'=>false,'message'=>'Oops! Something thing went wrong. Please try again later.'], 200);
    }

    public function getUserAddresses()
    {
       $user = Auth::User();
       $userAddresses = UserAddress::where('user_id',$user->id)->get();
       if($userAddresses->isEmpty()) {
            return response()->json(['statusCode'=>200,'success'=>false,'message'=>'Address not found.'], 200);
       }

       return response()->json(['statusCode'=>200,'success'=>true,'message'=>'User Addresses.','data'=>$userAddresses], 200);
    }
}

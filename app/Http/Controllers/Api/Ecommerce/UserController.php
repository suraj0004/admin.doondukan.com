<?php

namespace App\Http\Controllers\Api\Ecommerce;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use Validator;
use App\Http\Requests\Api\Ecommerce\UpdateProfileRequest;

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
        if(Auth::attempt(['phone' => $request->phone, 'password' => $request->password]))
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
        ]);

        if ($validator->fails())
        {
            $message = $validator->errors()->first();
            return response()->json(['statusCode'=>200,'success'=>false,'message'=>$message], 200);
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
}

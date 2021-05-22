<?php

namespace App\Http\Controllers\Api\Ecommerce;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use Validator;

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
            'phone'=>'required|numeric|unique:users',
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
        $tokenData =  $user->createToken('MyShopApp');
        $user->accessToken = $tokenData->accessToken;
        return response()->json(['statusCode'=>200,'success'=>true,'message'=>'User Successfully Registered','data'=>$user], 200);
    }

    public function logout()
    {
        $user = Auth::User()->token();
        if( $user->revoke() )
        {
            return response()->json(['statusCode'=>200,'success'=>true,'message'=>'User Successfully Logout'], 200);
        }
    }
}

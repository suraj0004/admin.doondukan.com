<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use Validator;

class UserController extends Controller
{
    public $successStatus = 200;

    //API Login
    public function login(Request $request) 
    { 
    	
    	$validator = Validator::make($request->all(), [ 
            'email' => 'required|string|email', 
            'password' => 'required|string' 
        ]);

        if ($validator->fails())
		{ 
			$message = $validator->errors()->first();
		    return response()->json(['statusCode'=>401,'success'=>false,'message'=>$message], 401);            
		}
		if(Auth::attempt(['email' => $request->email, 'password' => $request->password]))
        { 
            $user = Auth::User(); 
            $tokenData =  $user->createToken('MyShopApp');
            $token = $tokenData->token;
            $user->accessToken = $tokenData->accessToken; 
            return response()->json(['statusCode'=>$this->successStatus,'success'=>true,'message'=>'User Login','data' => $user], $this->successStatus); 
        } 
        else
        { 
            return response()->json(['statusCode'=>422,'success'=>false,'message'=>'Unauthorised User'], 422); 
        } 
    }

    //Function for user registeration
   	public function register(Request $request) 
	{ 
        $validator = Validator::make($request->all(), [ 
            'name' => 'required', 
            'email' => 'required|email|unique:users',
            'phone'=>'required|numeric', 
            'password' => 'required', 
            'c_password' => 'required|same:password', 
        ]);

		if ($validator->fails())
		{ 
			$message = $validator->errors()->first();
		    return response()->json(['statusCode'=>401,'success'=>false,'message'=>$message], 401);            
		}

        $user = new User();
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->email= $request->email;
        $user->password = bcrypt($request->password);
        $user->save(); 
        $tokenData =  $user->createToken('MyShopApp'); 
        $user->accessToken = $tokenData->accessToken;
		return response()->json(['statusCode'=>$this->successStatus,'success'=>true,'message'=>'User Successfully Registered','data'=>$user], $this->successStatus); 
	}

    public function logout()
    {
        $user = Auth::user()->token();
        if( $user->revoke() ) 
        {
            return response()->json(['statusCode'=>$this->successStatus,'success'=>true,'message'=>'User Successfully Logout'], $this->successStatus); 
        }
    }
}
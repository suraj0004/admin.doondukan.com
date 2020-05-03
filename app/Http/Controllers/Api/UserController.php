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

    public function login(Request $request) 
    { 
    	$request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);

		if(Auth::attempt(['email' => $request->email, 'password' => $request->password]))
        { 
            $user = Auth::User(); 
            $tokenData =  $user->createToken('MyShopApp');
            $token = $tokenData->token;
            $user->accessToken = $tokenData->accessToken;
            if ($request->remember_me) 
            {
            	$token->expires_at = Carbon::now()->addWeeks(1);
        		$token->save();
        	} 
            return response()->json(['statusCode'=>$this->successStatus,'status'=>true,'message'=>'User Login','data' => $user], $this->successStatus); 
        } 
        else
        { 
            return response()->json(['statusCode'=>401,'status'=>false,'message'=>'Unauthorised User'], 401); 
        } 
    }

   	public function register(Request $request) 
	{ 
        $validator = Validator::make($request->all(), [ 
            'name' => 'required', 
            'email' => 'required|email',
            'phone'=>'required|numeric', 
            'password' => 'required', 
            'c_password' => 'required|same:password', 
        ]);

		if ($validator->fails())
		{ 
		    return response()->json(['statusCode'=>401,'status'=>false,'message'=>$validator->errors()], 401);            
		}

        $user = new User;
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->email= $request->email;
        $user->password = bcrypt($request->password);
        $user->save(); 
        $tokenData =  $user->createToken('MyShopApp'); 
        $user->accessToken = $tokenData->accessToken;
		return response()->json(['statusCode'=>$this->successStatus,'status'=>true,'message'=>'User Successfully Registered','data'=>$user], $this->successStatus); 
	}
}

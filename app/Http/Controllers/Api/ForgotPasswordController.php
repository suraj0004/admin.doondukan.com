<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SmsService;
use App\Models\User;
use Validator;

class ForgotPasswordController extends Controller
{
    public function resetPassword(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|numeric',
            'otp' => 'required|numeric',
            'password' => 'required|confirmed',
        ]);

        if($validator->fails()) {
            $message = $validator->errors()->first();
            return response()->json(['statusCode'=>200,'success'=>false,'message'=>$message], 200);
        }

        $user = User::where('phone',$request->mobile)->first();
        if(!$user) {
            return response()->json(['statusCode'=>200,'success'=>false,'message'=>'Mobile number not found.'], 200);
        }
        
        $checkOtp = SmsService::verifyOTP($request->mobile,$request->otp);
        if(!$checkOtp) {
            return response()->json(['statusCode'=>200,'success'=>false,'message'=>'Invalid OTP'], 200);
        }

        $user->password = bcrypt($request->password);
        $user->save();
        return response()->json(['statusCode'=>200,'success'=>true,'message'=>'Password updated successfully.Redirecting....'], 200);
    }
}

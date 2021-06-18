<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SmsService;
use App\Models\Otp;
use Validator;
use App\Models\User;

class OtpController extends Controller
{
    public function sendOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|numeric',
            'type' => 'required|in:forget_password,sign_up_otp'
         ]);

        if ($validator->fails()) {
            $message = $validator->errors()->first();
            return response()->json(['statusCode'=>200,'success'=>false,'message'=>$message], 200);
        }
        $otp = rand(126000,999999);

        $checkMobileExits = User::select('id')->where('phone',$request->mobile)->exists();
        if($request->type == "forget_password" && !$checkMobileExits) {
            return response()->json(['statusCode'=>200,'success'=>false,'message'=>'Mobile number not found.'], 200);
        }else if($request->type == "sign_up_otp" && $checkMobileExits){
            return response()->json(['statusCode'=>200,'success'=>false,'message'=>'Mobile already exists.'], 200);
        }
        $otpData = Otp::where('mobile',$request->mobile)->firstOrNew();
        $otpData->mobile = $request->mobile;
        $otpData->otp = $otp;
        $otpData->save();
        $message = "Your DoonDunkan OTP is ".$otp;
        SmsService::sendSms($request->mobile,$message);
        return response()->json(['statusCode'=>200,'success'=>true,'message'=>'Otp successfully sent.'], 200);
    }
}

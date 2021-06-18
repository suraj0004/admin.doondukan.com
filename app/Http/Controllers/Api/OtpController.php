<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SmsService;
use App\Models\Otp;
class OtpController extends Controller
{
    public function sendOTP(Request $request)
    {   
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|numeric',
         ]);

        if ($validator->fails()) {
            $message = $validator->errors()->first();
            return response()->json(['statusCode'=>200,'success'=>false,'message'=>$message], 200);
        }
        $otp = rand(126000,999999);
        if($request->type == "forget_password") {
            $checkMobileExits = User::select('id')->where('mobile',$request->mobile)-first();
            if(!$checkMobileExits) {
                return response()->json(['statusCode'=>200,'success'=>false,'message'=>'Mobile number not found.'], 200);
            }
        }
        $otpData = Otp::where('mobile',$request->mobile)->firstOrNew();
        $otpData->mobile = $request->mobile;
        $otpData->otp = $request->otp;
        $otpData->save();
        $message = "Your DoonDunkan OTP is ".$otp;
        SmsService::sendSms($request->mobile,$message);
        return response()->json(['statusCode'=>200,'success'=>true,'message'=>'Otp successfully sent.'], 200);
    }
}

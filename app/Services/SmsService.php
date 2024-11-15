<?php
namespace App\Services;
use Illuminate\Support\Facades\Http;
use App\Models\Otp;
use App;

class SmsService {
    public static function sendSms(string $mobile, string $sms, string $template_id){
        if (App::environment('local')) {
            return true;
        }
        $response = Http::get(config('constants.SMS.SMS_API_ENDPOINT'), [

            'user' => config('constants.SMS.SMS_USER'),
            'authkey' => config('constants.SMS.AUTH_KEY'),
            'sender' => config('constants.SMS.SENDER_ID'),
            'mobile' => $mobile,
            'text' => $sms,
            'entityid' => config('constants.SMS.ENTITY_ID'),
            'templateid' => $template_id,
            'rpt' => config('constants.SMS.SMS_API_ENDPOINT'),

        ]);
    }

    public static function verifyOTP($mobile,$otp)
    {
        $check = Otp::where('mobile',$mobile)->where('otp',$otp)->first();
        if(!$check) {
            return false;
        }
        $check->delete();
        return true;
    }
}
?>

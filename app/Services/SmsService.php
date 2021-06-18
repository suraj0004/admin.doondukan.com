<?php
namespace App\Services;
use Illuminate\Support\Facades\Http;

class SmsService {
    public static function sendSms(string $mobile, string $sms){

        $response = Http::get(config('constants.SMS.SMS_API_ENDPOINT'), [

            'user' => config('constants.SMS.SMS_USER'),
            'authkey' => config('constants.SMS.AUTH_KEY'),
            'sender' => config('constants.SMS.SENDER_ID'),
            'mobile' => $mobile,
            'text' => $sms,
            'entityid' => config('constants.SMS.ENTITY_ID'),
            'templateid' => config('constants.SMS.TEMPLATE.TEST'),
            'rpt' => config('constants.SMS.SMS_API_ENDPOINT'),

        ]);

    }
}
?>

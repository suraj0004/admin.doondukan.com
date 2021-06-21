<?php
namespace App\Services;

class SmsTemplateService{

    const
    SELLER_NAME = "_SELLER_NAME_",
    BUYER_NAME = "_BUYER_NAME_",
    ORDER_ID = "_ORDER_ID_",
    LINK = "_LINK_",
    OTP = "_OTP_";

    public static function orderPlaced(string $SELLER_NAME, string $ORDER_ID, string $LINK){
        $template = config("constants.SMS.TEMPLATE.ORDER_PLACED.CONTENT");
        $template = str_replace(self::SELLER_NAME, $SELLER_NAME, $template);
        $template = str_replace(self::ORDER_ID, $ORDER_ID, $template);
        $template = str_replace(self::LINK, $LINK, $template);
        return $template;
    }

    public static function orderConfirmed(string $BUYER_NAME, string $SELLER_NAME, string $ORDER_ID){
        $template = config("constants.SMS.TEMPLATE.ORDER_CONFIRMED.CONTENT");
        $template = str_replace(self::BUYER_NAME, $BUYER_NAME, $template);
        $template = str_replace(self::SELLER_NAME, $SELLER_NAME, $template);
        $template = str_replace(self::ORDER_ID, $ORDER_ID, $template);
        return $template;
    }

    public static function forgotPassword(string $OTP){
        $template = config("constants.SMS.TEMPLATE.FORGOT_PASSWORD.CONTENT");
        $template = str_replace(self::OTP, $OTP, $template);
        return $template;
    }

    public static function signUpVerification(string $OTP){
        $template = config("constants.SMS.TEMPLATE.SIGN_UP_VERIFICATION.CONTENT");
        $template = str_replace(self::OTP, $OTP, $template);
        return $template;
    }
}

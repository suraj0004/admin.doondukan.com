<?php
return [
    "disks" => [
        "CATEGORY" => 'categories',
        "PRODUCT" => 'products',
        "STORE" => 'stores',
        "PROFILE" => 'profile',
    ],
    "DEFAULT_IMAGE_PATH" => "image/default_image.png",

    'ORDERSTATUS' => [
        'PLACED' => 0,
        'CONFIRM' => 1,
        'COMPLETED' => 2,
        'CANCEL' => 3,
    ],

    "ECOM_APP_URL" => env('ECOM_APP_URL'),
    "DEFAULT_QTY" => 10,

    "SMS" => [
        "SMS_API_ENDPOINT" => env('SMS_API_ENDPOINT'),
        "SMS_USER" => env('SMS_USER'),
        "AUTH_KEY" => env('AUTH_KEY'),
        "SENDER_ID" => env('SENDER_ID'),
        "ENTITY_ID" => env('ENTITY_ID'),
        "RPT" => env('RPT'),
        "TEMPLATE" => [
            "ORDER_PLACED" => [
                "ID" => "1707162410725072439",
                "CONTENT" => "Hi _SELLER_NAME_ a new order has been placed, Order Id _ORDER_ID_ . Click here _LINK_ to confirm. ITSIND",
            ],
            "ORDER_CONFIRMED" => [
                "ID" => "1707162410738179768",
                "CONTENT" => "Hi _BUYER_NAME_ your Order _ORDER_ID_ has been confirmed with _SELLER_NAME_ and will be ready shortly. ITSIND",
            ],
            "FORGOT_PASSWORD" => [
                "ID" => "1707162410743805317",
                "CONTENT" => "Forgot password: _OTP_ is your OTP for password reset.OTP is valid for 10 minutes ITSIND",
            ],
            "SIGN_UP_VERIFICATION" => [
                "ID" => "1707162410751059819",
                "CONTENT" => "_OTP_ is your DoonDukan OTP, valid for 10 minutes. ITSIND",
            ],
        ],
    ],

    "BASE64_IMAGE_EXTENSION" => [
        "data:image/png;base64,",
        "data:image/jpg;base64,",
        "data:image/jpeg;base64,",
    ],
];

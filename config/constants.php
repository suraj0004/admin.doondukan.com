<?php
return [
    "disks" => [
        "CATEGORY" => 'categories',
        "PRODUCT" => 'products',
        "STORE" => 'stores',
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
            "TEST" => "1707161786814434614"
        ]
    ],
];

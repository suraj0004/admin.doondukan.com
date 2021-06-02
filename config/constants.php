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
        'CANCEL' => 3
    ],

    "ECOM_APP_URL"=>env('ECOM_APP_URL'),
    "DEFAULT_QTY" => 10,
];

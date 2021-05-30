<?php
return [
    "disks" => [
        "CATEGORY" => 'categories',
        "PRODUCT" => 'products',
        "STORE" => 'stores',
    ],
    "DEFAULT_IMAGE_PATH" => "image/default_image.png",

    'ORDERSTATUS' => [
        'PLACED' => 1,
        'CONFIRM' => 2,
        'COMPLETED' => 3
    ],

    "ECOM_APP_URL"=>env('ECOM_APP_URL'),
];

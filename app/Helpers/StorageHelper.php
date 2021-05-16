<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

function saveImage($disk, $image_prefix = "", $image, $thumbnail = false, $delete_file = null)
{

    $image_name = $image_prefix . '_' . time() . '_' . mt_rand(1, 9999) . '.' . $image->getClientOriginalExtension();

    Storage::disk($disk)->putFileAs(null, $image, $image_name);
    if($thumbnail){
        createThumbnail($disk,$image_name);
    }

    if (!empty($delete_file)) {
        Storage::disk($disk)->delete($delete_file);
    }

    return $image_name;
}

function createThumbnail($disk, $image_name, $delete_file = null)
{
    $image = Image::make(Storage::disk($disk)->url($image_name))->fit(300, 300);
    Storage::disk($disk)->put('thumb_' . $image_name, $image->encode());

    if (!empty($delete_file)) {
        Storage::disk($disk)->delete("thumb_".$delete_file);
    }
}

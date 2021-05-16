<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

function saveFile($disk, $file_prefix = "", $file, $thumbnail = false, $delete_file = null)
{

    $file_name = $file_prefix . '_' . time() . '_' . mt_rand(1, 9999) . '.' . $file->getClientOriginalExtension();

    Storage::disk($disk)->putFileAs(null, $file, $file_name);
    if($thumbnail){
        createThumbnail($disk,$file_name);
    }

    if (!empty($delete_file)) {
        Storage::disk($disk)->delete($delete_file);
    }

    return $file_name;
}

function createThumbnail($disk, $image_name, $delete_file = null)
{
    $image = Image::make(Storage::disk($disk)->url($image_name))->fit(300, 300);
    Storage::disk($disk)->put('thumb_' . $image_name, $image->encode());

    if (!empty($delete_file)) {
        Storage::disk($disk)->delete("thumb_".$delete_file);
    }
}

function getFileUrl($disk, $file, $default_file_path = null)
{
    $fileUrl = asset($default_file_path ?? config("constants.DEFAULT_IMAGE_PATH"));
    if (Storage::disk($disk)->exists($file)) {
        $fileUrl = Storage::disk($disk)->url($file);
    }
    return ($fileUrl);
}

<?php

namespace App\Traits;

trait ImageUpload
{
    protected function handleImageUpload($image, $folder)
    {
        $ext = $image->getClientOriginalExtension();
        $filename = uniqid($folder) . '.' . $ext;
        $image->move(public_path('assets/img/'.$folder.'/'), $filename);

        return $filename;
    }
}

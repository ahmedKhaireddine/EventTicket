<?php

namespace App\Traits;

use Illuminate\Http\UploadeFile;
use Illuminate\Support\Facades\Storage;

trait UploadTrait
{
    public function uploadOne($uploadeFile, $folder, $disk = 'public', $filename = null){

        $name = ! is_null($filename) ? $filename.'_'.time() : str_random(25).'_'.time();

        $filePath = "http://localhost/api/public{$folder}{$name}.{$uploadeFile->getClientOriginalExtension()}";

        $file = $uploadeFile->storeAs($folder, $name. '.' . $uploadeFile->getClientOriginalExtension(), $disk);

        return $filePath;
    }

}
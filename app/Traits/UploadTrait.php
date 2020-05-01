<?php

namespace App\Traits;

use Illuminate\Http\UploadeFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait UploadTrait
{
    /**
     * Upload image.
     *
     * @param  object  $uploadeFile
     * @param  string  $folder
     * @param  string  $disk
     * @param  int  $id
     * @return string
     */
    public function uploadOne($uploadeFile, string $folder, string $disk = 'public', int $id)
    {
        $name = 'event_'. $id . '_' . time();

        $filePath = "http://localhost/api/public{$folder}{$name}.{$uploadeFile->getClientOriginalExtension()}";

        $file = $uploadeFile->storeAs($folder, "{$name}.{$uploadeFile->getClientOriginalExtension()}", $disk);

        return $filePath;
    }
}
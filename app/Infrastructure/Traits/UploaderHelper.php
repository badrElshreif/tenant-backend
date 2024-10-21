<?php

namespace App\Infrastructure\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;

//use Image;
trait UploaderHelper
{

    public function generateFileRandomName($extension): string
    {
        $time = time();
        $str_random = Str::random(8);
        return "{$time}_{$str_random}.{$extension}";
    }

    public function handleUploadImg($file, $folderName = 'uploads')
    {
        $extention = $file->extension();
        $fileName = $this->generateFileRandomName($extention);
        $tenantDir = getTenant()->slug;
        Storage::disk('public')->put($tenantDir . "/" . $folderName . '/' . $fileName, File::get($file));
        // $fullPath = $this->getFileFullPath($fileName, $folderName);
        return $fileName;
    }

    /**
     * Get file full path
     * @param file_path
     * @return string
     */
    public function getFileFullPath($fileName, $folderName = 'uploads')
    {
        return Storage::disk('public')->url($folderName . '/' . $fileName);
    }

    public function getFileRelativePath($fileName, $folderName = 'uploads'): string
    {
        $filePath = Storage::url($folderName . '/' . $fileName);
        return $filePath;
    }

    public function deleteFile($fileName, $folderName = 'uploads'): bool
    {
        $file = Storage::disk('public')->delete($folderName . '/' . $fileName);
        //$x = unlink(storage_path('app/public/'.$folderName.'/'.$fileName));
        //dd($x);
        return $file;
    }

    function uploadImage($image, $image_path, $width = null, $height = null): array
    {
        //$extension = $image->getClientOriginalExtension();
        $extension = $image->extension();

        $fileName = $this->generateFileRandomName($extension);
        $type = null;
        $videos = ['mpeg', 'ogg', 'mp4', 'webm', '3gp', 'mov', 'flv', 'avi', 'wmv', 'ts'];
        $files = ['csv', 'txt', 'xlx', 'xls', 'pdf'];
        if (in_array($extension, $videos)) {
            $type = 'video';
        } else if (in_array($extension, $files)) {
            $type = 'pdf';
        } else {
            $type = 'image';
        }

        if ($type == 'image') {
            $img = Image::read($image);
            //$img = Image::make($image->getRealPath());

            // RESIZE IMAGE
            if (isset($width) || isset($height))
                $img->resize($width, $height, function ($ratio) {
                    $ratio->aspectRatio();
                });

            $fullPath = Storage::disk('public')->path($image_path . '/' . $fileName);
            $dirPath = Storage::disk('public')->path($image_path);
            if (!File::exists($dirPath)) {
                File::makeDirectory($dirPath, '0755', true, true);
            }
            // dd(Storage::disk('public/'.$image_path . '/' . $fileName));
            $img->save($fullPath);

            //  Storage::disk('public')->put($image_path . '/' . $fileName, $img);
        } else {
            Storage::disk('public')->put($image_path . '/' . $fileName, file_get_contents($image));
        }

        return [
            'name' => $fileName,
            'file' => $this->getFileFullPath($fileName, $image_path),
            'type' => $type,
            'folder' => $image_path
        ];
    }

    public function moveFile($fileName, $folderName)
    {
        //try {
        $from_path = storage_path('app/public/uploads') . '/' . $fileName;
        $to_directory = storage_path('app/public/' . $folderName);

        $to_path = $to_directory . '/' . $fileName;


        if (File::exists($from_path)) {
            if (!File::exists($to_directory)) {
                File::makeDirectory($to_directory, '0755', true, true);
            }
            File::move($from_path, $to_path);
            $this->removeFile($from_path);
            return true;
        }

        return false;
        // } catch (League\Flysystem\FileNotFoundException $ex) {
        //     return false;
        // }
    }

    public function moveFile2($fileName, $folderName)
    {
        $from_path = storage_path('app/public/uploads') . '/' . $fileName;
        if (File::exists($from_path)) {
            // Storage::move(from_path, to_path);
            Storage::move('uploads/' . $fileName, $folderName . '/' . $fileName);
            $this->deleteFile($fileName, 'uploads');
            return true;
        }
        return false;
    }

    public function removeFile($file_path): bool
    {
        if (File::exists($file_path)) {
            File::delete($file_path);
            return true;
        } else
            return false;
    }
}

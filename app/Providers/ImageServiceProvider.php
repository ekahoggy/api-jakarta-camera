<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ImageServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
    }

    public static function uploadImage($image, $location = '')
    {
        if (!empty($image)) {
            $base64Image = $image;

            if (!$tmpFileObject = static::validateBase64($base64Image, ['png', 'jpg', 'jpeg', 'gif'])) {
                return response()->json([
                    'error' => 'Invalid image format.'
                ], 415);
            }

            $storedFilePath = static::storeFile($tmpFileObject, $location);

            if(!$storedFilePath) {
                return response()->json([
                    'error' => 'Something went wrong, the file was not stored.'
                ], 500);
            }

            return url(Storage::url($storedFilePath));
        }

        return response()->json([
            'error' => 'Invalid request.'
        ], 400);
    }

    private static function validateBase64(string $base64data, array $allowedMimeTypes)
    {
        // strip out data URI scheme information (see RFC 2397)
        if (str_contains($base64data, ';base64')) {
            list(, $base64data) = explode(';', $base64data);
            list(, $base64data) = explode(',', $base64data);
        }

        // strict mode filters for non-base64 alphabet characters
        if (base64_decode($base64data, true) === false) {
            return false;
        }

        // decoding and then re-encoding should not change the data
        if (base64_encode(base64_decode($base64data)) !== $base64data) {
            return false;
        }

        $fileBinaryData = base64_decode($base64data);

        // temporarily store the decoded data on the filesystem to be able to use it later on
        $tmpFileName = tempnam(sys_get_temp_dir(), 'medialibrary');
        file_put_contents($tmpFileName, $fileBinaryData);

        $tmpFileObject = new File($tmpFileName);

        // guard against invalid mime types
        $allowedMimeTypes = Arr::flatten($allowedMimeTypes);

        // if there are no allowed mime types, then any type should be ok
        if (empty($allowedMimeTypes)) {
            return $tmpFileObject;
        }

        // Check the mime types
        $validation = Validator::make(
            ['file' => $tmpFileObject],
            ['file' => 'mimes:' . implode(',', $allowedMimeTypes)]
        );

        if($validation->fails()) {
            return false;
        }

        return $tmpFileObject;
    }

    private static function storeFile(File $tmpFileObject, $location = 'default')
    {
        $tmpFileObjectPathName = $tmpFileObject->getPathname();

        $file = new UploadedFile(
            $tmpFileObjectPathName,
            $tmpFileObject->getFilename(),
            $tmpFileObject->getMimeType(),
            0,
            true
        );

        $storedFile = $file->store($location, ['disk' => 'public']);

        unlink($tmpFileObjectPathName); // delete temp file

        return $storedFile;
    }
}

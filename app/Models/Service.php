<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use WebPConvert\WebPConvert;
use Spatie\Image\Manipulations;

class Service extends Model
{
    public function saveImage($path = '', $image) {
        if (isset($image['base64']) && !empty($image['base64'])) {
            if (strpos($image['base64'], 'data:image/jpeg;base64,') === 0) {
                $prefixToRemove = 'data:image/jpeg;base64,';
                $extension = '.jpg';
            } elseif (strpos($image['base64'], 'data:image/png;base64,') === 0) {
                $prefixToRemove = 'data:image/png;base64,';
                $extension = '.png';
            } else {
                // Tipe gambar tidak didukung
                return null;
            }

            $imageData = substr($image['base64'], strlen($prefixToRemove));
            $imageData = base64_decode($imageData);
            $fileName = Str::random(10) . $extension;


            if (Storage::put("public/images/$path" . $fileName, $imageData)) {
                $options = [];
                $dir = storage_path().'/app/public/images/'.$path;
                $source = $dir.'/'.$fileName;
                $destination = $source . '.webp';
                WebPConvert::convert($source, $destination, $options);

                return $fileName.'.webp';
            } else {
                return null;
            }
        }

        $arrImage = explode("/", $image);
        return end($arrImage);
    }

    public function saveVideo($path = '', $video) {
        if (str_contains($video, 'data:video')) {
            $video_parts = explode(';base64,', $video);
            $video_type_aux = explode('video/', $video_parts[0]);
            $video_type = $video_type_aux[1];
            $video_base64 = base64_decode($video_parts[1]);
            $namaVideo = uniqid() . '.' . $video_type;
            $fileVideo = $path . $namaVideo;
            if (Storage::put("public/videos/" . $fileVideo, $video_base64)) {
                return $namaVideo;
            } else {
                return null;
            }
        }

        $arrVideo = explode("/", $video);
        return end($arrVideo);
    }

    function convertBase64ToWebp($base64Image, $width = null, $height = null, $quality = 80)
    {
        // Decode base64 string
        $imageData = base64_decode($base64Image);

        // Create an image instance from the decoded data
        $image = Image::make($imageData);

        // Resize the image if width and height are provided
        if ($width || $height) {
            $image->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }

        // Convert the image to WebP format and encode it
        $webpImage = $image->encode('webp', $quality);

        // Return the base64-encoded WebP image
        return base64_encode($webpImage);
    }
}

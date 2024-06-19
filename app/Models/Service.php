<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
                return $fileName;
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
                return $fileVideo;
            } else {
                return null;
            }
        }

        $arrVideo = explode("/", $video);
        return end($arrVideo);
    }
}

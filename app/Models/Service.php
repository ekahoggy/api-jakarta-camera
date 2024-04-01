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
}

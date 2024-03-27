<?php

namespace App\Http\Controllers;

use App\Models\Slider;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function slider() {
        $slider = Slider::where('is_status', 1)->orderBy('index_position', 'ASC')->get();

        if($slider){
            return response()->json(['status_code' => 200, 'data' => $slider], 200);
        }
        else{
            return response()->json(['status_code' => 422, 'pesan' => 'Data Tidak ada'], 422);
        }
    }
}

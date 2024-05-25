<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    protected $brand;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['brand']]);
        $this->brand = new Brand();
    }

    public function getData(Request $request){
        try {
            $params = (array) $request->all();
            $data = $this->brand->getAll($params);

            return response()->json([
                'data' => $data,
                'status_code' => 200
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th,
                'status_code' => 500
            ], 500);
        }
    }

    public function kategori() {
        $brand = $this->brand->getBrand();

        if($brand){
            return response()->json(['status_code' => 200, 'data' => $brand], 200);
        }
        else{
            return response()->json(['status_code' => 422, 'pesan' => 'Data Tidak ada'], 422);
        }
    }
}

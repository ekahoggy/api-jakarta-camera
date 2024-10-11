<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\WoocommerceModel;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class KategoriController extends Controller
{
    protected $kategori;
    protected $wooModel;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['kategori']]);
        $this->kategori = new Kategori();
        $this->wooModel = new WoocommerceModel();
    }

    public function getData(Request $request){
        try {
            $params = (array) $request->all();
            $data = $this->kategori->getAll($params);

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

    public function getDataById($id){
        try {
            // $params = (array) $request->all();
            $data = $this->kategori->getById($id);

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

    public function simpan(Request $request){
        $params = (array) $request->all();
        $validator = Validator::make($params, [
            "kategori"  => "required",
            "icon"  => "required"
        ]);
        if ($validator->valid()) {
            $this->kategori->simpan($params);

            return response()->json([
                'data' => [],
                'status_code' => 200
            ], 200);
        }
        else{
            return response()->json([
                'message' => $validator->errors(),
                'status_code' => 500
            ], 500);
        }
    }

    public function getDetail($id){
        try {
            $data = $this->kategori->getDetail($id);

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
        $categories = $this->kategori->getKategori();

        if($categories){
            return response()->json(['status_code' => 200, 'data' => $categories], 200);
        }
        else{
            return response()->json(['status_code' => 422, 'pesan' => 'Data Tidak ada'], 422);
        }
    }
}

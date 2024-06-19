<?php

namespace App\Http\Controllers;

use App\Models\EdukasiKategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EdukasiKategoriController extends Controller
{
    protected $kategori;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['kategori']]);
        $this->kategori = new EdukasiKategori();
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
            "kategori"  => "required"
        ]);
        if ($validator->valid()) {
            $data = $this->kategori->simpan($params);

            return response()->json([
                'data' => $data,
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

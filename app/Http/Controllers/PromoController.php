<?php

namespace App\Http\Controllers;

use App\Models\Promo;
use Illuminate\Http\Request;

class PromoController extends Controller
{
    protected $promo;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['promo']]);
        $this->promo = new Promo();
    }

    public function getData(Request $request){
        try {
            $params = (array) $request->all();
            $data = $this->promo->getAll($params);

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
            $data = $this->promo->getById($id);

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
        try {
            $params = (array) $request->only('id', 'kode', 'promo', 'tanggal_mulai', 'tanggal_selesai', 'jam_mulai', 'jam_selesai', 'is_status');
            $data = $this->promo->simpan($params);

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

    public function promo() {
        $promo = $this->promo->getPromo();

        if($promo){
            return response()->json(['status_code' => 200, 'data' => $promo], 200);
        }
        else{
            return response()->json(['status_code' => 422, 'pesan' => 'Data Tidak ada'], 422);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Promo;
use App\Models\PromoDet;
use Illuminate\Http\Request;

class PromoController extends Controller
{
    protected $promo;
    protected $promoDet;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['promo']]);
        $this->promo = new Promo();
        $this->promoDet = new PromoDet();
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
            $data->detail = $this->promoDet->getDetailByPromo($id);

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
            $params = (array) $request->all();
            $data = $this->promo->simpan($params['main']);
            $detail = [];
            foreach ($params['detail'] as $key => $value) {
                $detail[$key]['m_promo_id'] = $data['id'];
                $detail[$key]['m_produk_id'] = $value['m_produk_id'];
                $detail[$key]['persen'] = $value['persen'];
                $detail[$key]['nominal'] = ((double)$value['persen'] / 100) * (int)$value['harga'];
                $detail[$key]['promo_used'] = 0;
                $detail[$key]['qty'] = $value['qty'];
            }

            // simpan detail promo
            foreach ($detail as $key => $value) {
                $this->promoDet->simpan($value);
            }

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

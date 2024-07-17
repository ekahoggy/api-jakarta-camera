<?php

namespace App\Http\Controllers;

use App\Models\Promo;
use App\Models\PromoDet;
use App\Models\PromoKategori;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PromoController extends Controller
{
    protected $promo;
    protected $promoDet;
    protected $promoKat;
    protected $subscribe;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['promo']]);
        $this->promo = new Promo();
        $this->promoDet = new PromoDet();
        $this->promoKat = new PromoKategori();
        $this->subscribe = new Subscription();
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
            $data->promo_kategori = $this->promoDet->getKategoriByPromo($id);

            foreach ($data->detail as $key => $value) {
                $value->promo_used = $value->qty - $value->promo_used;
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

    public function simpan(Request $request){
        DB::beginTransaction();
        try {
            $params = (array) $request->all();
            $kategoriList = $params['main']['kategori_id'];
            $brandList = $params['main']['brand_id'];
            unset($params['main']['kategori_id']);
            unset($params['main']['brand_id']);
            $data = $this->promo->simpan($params['main']);

            if(isset($kategoriList)){
                foreach ($kategoriList as $key => $value) {
                    $pKategori = [
                        'promo_id' => $data['id'],
                        'kategori_id' => $value,
                    ];

                    $this->promoKat->post($pKategori);
                }
            }

            if(isset($brandList)){
                foreach ($brandList as $key => $value) {
                    $pBrand = [
                        'promo_id' => $data['id'],
                        'brand_id' => $value,
                    ];

                    $this->promoKat->post($pBrand);
                }
            }

            $detail = [];
            foreach ($params['detail'] as $key => $value) {
                $detail[$key]['m_promo_id'] = $data['id'];
                $detail[$key]['m_produk_id'] = isset($value['m_produk_id']) ? $value['m_produk_id'] : '';
                $detail[$key]['m_edukasi_id'] = isset($value['m_edukasi_id']) ? $value['m_edukasi_id'] : '';
                $detail[$key]['persen'] = $value['persen'];
                $detail[$key]['nominal'] = $value['nominal'];
                $detail[$key]['promo_used'] = 0;
                $detail[$key]['qty'] = $value['qty'];
                $detail[$key]['status'] = $value['status'] == true ? 1 : 0;
            }

            // simpan detail promo
            foreach ($detail as $key => $value) {
                $this->promoDet->simpan($value);
            }

            //send emails promo
            // $paramsEmail = [
            //     'promo' => $data,
            //     'detail' => $detail
            // ];

            // $this->subscribe->sendPromo($paramsEmail);

            return response()->json([
                'data' => $data,
                'status_code' => 200
            ], 200);
        } catch (\Throwable $th) {
            DB::rollback();
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

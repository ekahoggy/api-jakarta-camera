<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use App\Models\VoucherDetail;
use App\Models\VoucherKategori;
use App\Models\VoucherUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class VoucherController extends Controller
{
    protected $voucher;
    protected $voucherDet;
    protected $voucherKat;
    protected $voucherUser;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['voucher']]);
        $this->voucher = new Voucher();
        $this->voucherDet = new VoucherDetail();
        $this->voucherKat = new VoucherKategori();
        $this->voucherUser = new VoucherUser();
    }

    public function getData(Request $request){
        try {
            $params = (array) $request->all();
            $data = $this->voucher->getAll($params);

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
            $data = $this->voucher->getById($id);
            $data->detail = $this->voucherDet->getDetailByVoucher($id);
            $data->voucher_kategori = $this->voucherDet->getKategoriByVoucher($id);
            $data->user_id = $this->voucherUser->getUserByVoucher($id);

            foreach ($data->detail as $key => $value) {
                $value->used = $value->qty - $value->used;
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
            $userList = $params['main']['user_id'];
            unset($params['main']['kategori_id']);
            unset($params['main']['brand_id']);
            unset($params['main']['user_id']);
            $data = $this->voucher->simpan($params['main']);

            if(isset($kategoriList)){
                foreach ($kategoriList as $key => $value) {
                    $pKategori = [
                        'voucher_id' => $data['id'],
                        'kategori_id' => $value,
                    ];

                    $this->voucherKat->post($pKategori);
                }
            }

            if(isset($brandList)){
                foreach ($brandList as $key => $value) {
                    $pBrand = [
                        'voucher_id' => $data['id'],
                        'brand_id' => $value,
                    ];

                    $this->voucherKat->post($pBrand);
                }
            }

            $detail = [];
            foreach ($params['detail'] as $key => $value) {
                $detail[$key]['voucher_id'] = $data['id'];
                $detail[$key]['produk_id'] = isset($value['m_produk_id']) ? $value['m_produk_id'] : '';
                $detail[$key]['edukasi_id'] = isset($value['m_edukasi_id']) ? $value['m_edukasi_id'] : '';
                $detail[$key]['persen'] = $value['persen'];
                $detail[$key]['nominal'] = $value['nominal'];
                $detail[$key]['used'] = 0;
                $detail[$key]['qty'] = $value['qty'];
                $detail[$key]['status'] = $value['status'] == true ? 1 : 0;
            }

            // simpan detail voucher
            foreach ($detail as $key => $value) {
                $this->voucherDet->simpan($value);
            }

            // simpan detail voucher
            foreach ($userList as $key => $value) {
                $pUser = [
                    'voucher_id' => $data['id'],
                    'user_id' => $value
                ];
                $this->voucherUser->post($pUser);
            }

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

    public function voucher(Request $request) {
        $params = (array) $request->all();
        $voucher = $this->voucher->getVoucher($params);

        if($voucher){
            return response()->json(['status_code' => 200, 'data' => $voucher], 200);
        }
        else{
            return response()->json(['status_code' => 422, 'pesan' => 'Data Tidak ada'], 422);
        }
    }
}

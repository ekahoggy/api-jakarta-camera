<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    protected $voucher;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['voucher']]);
        $this->voucher = new Voucher();
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
            $params = (array) $request->only('id', 'redeem_code', 'voucher', 'tanggal_mulai', 'tanggal_selesai', 'jam_mulai', 'jam_selesai', 'gambar', 'deskripsi', 'kategori', 'qty', 'voucher_used', 'type', 'voucher_value', 'voucher_max', 'voucher_min_beli', 'is_status');
            $data = $this->voucher->simpan($params);

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

    public function voucher() {
        $voucherModel = new voucher();
        $voucher = $voucherModel->getVoucher();

        if($voucher){
            return response()->json(['status_code' => 200, 'data' => $voucher], 200);
        }
        else{
            return response()->json(['status_code' => 422, 'pesan' => 'Data Tidak ada'], 422);
        }
    }
}

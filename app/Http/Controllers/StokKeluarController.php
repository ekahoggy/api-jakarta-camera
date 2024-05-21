<?php

namespace App\Http\Controllers;

use App\Models\Stok;
use App\Models\StokDet;
use Illuminate\Http\Request;

class StokKeluarController extends Controller
{
    protected $stok;
    protected $stokDetail;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => []]);
        $this->stok = new Stok();
        $this->stokDetail = new StokDet();
    }

    public function getData(Request $request){
        try {
            $params = (array) $request->all();
            $params['type'] = 'o';
            $data = $this->stok->getAll($params);

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

    public function getAvailable(Request $request){
        try {
            $params = (array) $request->all();
            $data = $this->stokDetail->getAvailable($params);

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
            $data = $this->stok->getById($id);
            $data->detail = $this->stokDetail->getById($id);

            foreach ($data->detail as $key => $value) {
                $value->sisa = $this->stokDetail->getAvailable(
                    [
                        'type' => 'o',
                        'm_produk_id' => $value->m_produk_id
                    ]
                );
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
        try {
            $params = (array) $request->all();
            $data = $this->stok->simpan($params['main']);
            foreach ($params['detail'] as $key => $value) {
                $detail = [];
                $detail['id'] = isset($value['id']) ? $value['id'] : '';
                $detail['t_stok_id'] = $data['id'];
                $detail['m_produk_id'] = $value['m_produk_id'];
                $detail['qty'] = $value['qty'];

                $this->stokDetail->simpan($detail);
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

    public function changeStatus(Request $request){
        try {
            $params = (array) $request->all();
            $data = $this->stok->changeStatus($params);

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

    public function getDetail($id){
        try {
            $data = $this->stok->getDetail($id);

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
        $categories = $this->stok->getKategori();

        if($categories){
            return response()->json(['status_code' => 200, 'data' => $categories], 200);
        }
        else{
            return response()->json(['status_code' => 422, 'pesan' => 'Data Tidak ada'], 422);
        }
    }
}

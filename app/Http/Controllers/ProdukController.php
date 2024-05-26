<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProdukController extends Controller
{
    protected $produk;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['produk']]);
        $this->produk = new Produk();
    }

    public function getData(Request $request){
        $produkModel = new Produk();
        $params = (array) $request->all();

        $produk = $produkModel->getAll($params);
        foreach ($produk['list'] as $key => $value) {
            $value->variant = $produkModel->getVariant($value->id);
            $value->photo_product = $produkModel->getPhoto($value->id);
            $value->foto = Storage::url('images/produk/' . $value->media_link);
            $value->rowspan = count($value->variant);
        }
        return response()->json(['success' => true, "data" => $produk]);
    }

    public function getProdukById(Request $request, $id){
        $data = Produk::where('is_active', 1)->where('id', $id)->first();

        return response()->json(['success' => true, "data" => $data]);
    }

    public function katalog(Request $request) {
        $produkModel = new Produk();
        $params = (array) $request->all();

        $produk = $produkModel->getAll($params);

        if ($produk){
            return response()->json(['status_code' => 200, 'data' => $produk], 200);
        } else{
            return response()->json(['status_code' => 422, 'pesan' => 'Data Tidak ada'], 422);
        }
    }

    public function produk() {
        $produkModel = new Produk();
        $produk = $produkModel->getAll();
        if($produk){
            return response()->json(['status_code' => 200, 'data' => $produk], 200);
        }
        else{
            return response()->json(['status_code' => 422, 'pesan' => 'Data Tidak ada'], 422);
        }
    }

    public function getProdukSlug(Request $request) {
        $produkModel = new Produk();
        $produk = $produkModel->getBySlug($request->slug);
        if($produk){
            return response()->json(['status_code' => 200, 'data' => $produk], 200);
        }
        else{
            return response()->json(['status_code' => 422, 'pesan' => 'Data Tidak ada'], 422);
        }
    }

    public function simpan(Request $request){
        try {
            $params = (array) $request->all();
            $data = $this->produk->simpan($params);

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

    public function getPhoto($id) {
        $produkModel = new Produk();
        $photo = $produkModel->getPhoto($id);

        if($photo){
            return response()->json(['status_code' => 200, 'data' => $photo], 200);
        }
        else{
            return response()->json(['status_code' => 422, 'pesan' => 'Data Tidak ada'], 422);
        }
    }

    public function getVariant($id) {
        $produkModel = new Produk();
        $variant = $produkModel->getVariant($id);

        if($variant){
            return response()->json(['status_code' => 200, 'data' => $variant], 200);
        }
        else{
            return response()->json(['status_code' => 422, 'pesan' => 'Data Tidak ada'], 422);
        }
    }

    public function varian($type){
        $produkModel = new Produk();
        $variant = $produkModel->varian($type);

        if($variant){
            return response()->json(['status_code' => 200, 'data' => $variant], 200);
        }
        else{
            return response()->json(['status_code' => 422, 'pesan' => 'Data Tidak ada'], 422);
        }
    }

    public function prosesVariant(Request $request){
        $params = (array) $request->all();
        $data_utama = (array) $params['data_utama'];
        $data_second = (array) $params['data_second'];
        $splitDataUtama = [];
        foreach ($data_utama as $key => $value) {
            $splitDataUtama[$value['varian1']][] = $value;
        }

        foreach ($splitDataUtama as $key => $value) {
            foreach ($data_second as $i => $s) {
                foreach ($value as $k => $v) {
                    if($i === $k){
                        $splitDataUtama[$key][$k]['varian2'] = $s['nama'];
                    }
                }
            }
        }
        $data = [];
        foreach ($splitDataUtama as $key => $value) {
            foreach ($value as $k => $v) {
                $data[] = $v;
            }
        }
        return response()->json(['status_code' => 200, 'data' => array_values($data)], 200);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    protected $produk;

    public function __construct()
    {
        // $this->middleware('auth:api', ['except' => ['produk']]);
        $this->produk = new Produk();
    }

    public function getData(Request $request){
        $produkModel = new Produk();
        $params = (array) $request->all();

        $produk = $produkModel->getAll($params);

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
            $params = (array) $request->only('id', 'm_kategori_id', 'sku', 'nama', 'type', 'harga', 'link_tokped', 'link_shopee', 'link_bukalapak', 'link_lazada', 'link_blibli', 'detail_produk', 'deskripsi', 'in_box', 'photo', 'variant');
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
}

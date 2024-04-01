<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    protected $produk;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['produk']]);
        $this->produk = new Produk();
    }

    //public function
    public function getProduk(Request $request){
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
}

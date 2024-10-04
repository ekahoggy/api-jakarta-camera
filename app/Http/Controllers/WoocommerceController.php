<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Produk;
use App\Models\WoocommerceModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WoocommerceController extends Controller
{
    protected $woocommerce;
    protected $produk;
    protected $kategori;

     function __construct() {
        $this->woocommerce = new WoocommerceModel();
        $this->kategori = new Kategori();
        $this->produk = new Produk();
    }

    public function authorWoo(Request $request){

        try {
            $auth = $this->woocommerce->authWooCommerce($request);
            return response()->json([
                'data' => $auth,
                'status_code' => 200
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th,
                'status_code' => 500
            ], 500);
        }

    }

    public function sinkronKategori(Request $request) {
        try {
            Kategori::truncate();
            $params = [
                "per_page"=>100
            ];
            $kategori = $this->woocommerce->getCategories($params);

            $arrNewData = [];
            foreach ($kategori as $key => $value) {
                $arrNewData['woo_kategori_id'] = $value->id;
                $arrNewData['kategori'] = $value->name;
                $arrNewData['induk_id'] = $value->parent;
                $arrNewData['keterangan'] = $value->description;
                $arrNewData['icon'] = $value->image ? $value->image->src : null;
                $arrNewData['sinkron'] = true;

                $this->kategori->insertCategory($arrNewData);
            }

            return response()->json([
                'data' => $kategori,
                'status_code' => 200
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th,
                'status_code' => 500
            ], 500);
        }
    }

    public function getKategori(Request $request) {
        try {
            $kategori = $this->woocommerce->getCategories($request->all());

            return response()->json([
                'data' => $kategori,
                'status_code' => 200
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th,
                'status_code' => 500
            ], 500);
        }
    }

    public function sinkronProduk(Request $request) {
        try {
            Produk::truncate();
            DB::table('m_produk_media')->truncate();
            $max = 2000;
            for ($i = 0; $i < $max; $i += 100) {
                $params = [
                    "offset"=>$i,
                    "per_page"=>100
                ];
                $arrNewData = [];
                $produk = $this->woocommerce->getProduk($params);
                if($produk){
                    foreach ($produk as $key => $value) {
                        $arrNewData['woo_produk_id'] = $value->id;
                        $arrNewData['sku'] = $value->sku ?? '';
                        $arrNewData['nama'] = $value->name;
                        $arrNewData['deskripsi'] = $value->description ?? '';
                        $arrNewData['detail_produk'] = $value->short_description ?? '';
                        $arrNewData['in_box'] = $value->uagb_excerpt ?? '';
                        $arrNewData['stok'] = $value->stock_quantity ?? 0;
                        $arrNewData['stok_status'] = $value->stock_status ?? 0;
                        $arrNewData['harga'] = (double)$value->price ?? 0;
                        $arrNewData['berat'] = (double)$value->weight * 1000 ?? 0;
                        $arrNewData['lebar'] = $value->dimensions ? (int)$value->dimensions->length : 0;
                        $arrNewData['panjang'] = $value->dimensions ? (int)$value->dimensions->width : 0;
                        $arrNewData['tinggi'] = $value->dimensions ? (int)$value->dimensions->height : 0;

                        $arrNewData['photo'] = $value->images ?? [];
                        $arrNewData['sinkron'] = true;
                        $this->produk->insertProduct($arrNewData);
                    }
                }
            }

            return response()->json([
                'data' => $produk,
                'status_code' => 200
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th,
                'status_code' => 500
            ], 500);
        }
    }

    public function getProduk(Request $request) {
        try {
            $produk = $this->woocommerce->getProduk($request->all());

            return response()->json([
                'data' => $produk,
                'status_code' => 200
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th,
                'status_code' => 500
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


use App\Models\Slider;
use App\Models\Produk;
use App\Models\Brand;
use App\Models\Order;
use App\Models\Promo;
use App\Models\PromoDet;

class SiteController extends Controller
{
    protected $slider;
    protected $product;
    protected $brand;
    protected $order;
    protected $promo;
    protected $promoDet;

    public function __construct()
    {
        $this->slider = new Slider();
        $this->product = new Produk();
        $this->brand = new Brand();
        $this->order = new Order();
        $this->promo = new Promo();
        $this->promoDet = new PromoDet();
    }

    public function slider() {
        $slider = $this->slider->getAll([]);

        if($slider){
            return response()->json(['status_code' => 200, 'data' => $slider], 200);
        }
        else{
            return response()->json(['status_code' => 422, 'pesan' => 'Data Tidak ada'], 422);
        }
    }

    public function getProduct(Request $request){
        $params = (array) $request->all();

        $promo = $this->promoDet->getDetailPromoAktif();
        $produk = $this->product->getAll($params);

        foreach ($produk['list'] as $key => $value) {
            $value->is_promo = false;
            $value->is_flashsale = false;
            $value->promo = [];
            $value->harga_promo = $value->harga;
            $value->variant = $this->product->getVariant($value->id);
            $value->photo_product = $this->product->getPhoto($value->id);
            $value->foto = $this->product->getMainPhotoProduk($value->id);

            foreach ($promo as $k => $p) {
                if($p->m_produk_id === $value->id){
                    $value->is_promo = true;
                    $hitungPromo = ($p->persen / 100) * $value->harga;
                    $value->harga_promo = $value->harga - $hitungPromo;
                    $value->promo = [
                        'm_promo_id' => $p->m_promo_id,
                        'kode' => $p->kode,
                        'promo' => $p->promo,
                        'tanggal_mulai' => $p->tanggal_mulai,
                        'jam_mulai' => $p->jam_mulai,
                        'tanggal_selesai' => $p->tanggal_selesai,
                        'jam_selesai' => $p->jam_selesai,
                        'persen' => $p->persen,
                        'nominal' => $p->nominal,
                        'qty' => $p->qty,
                        'promo_min_beli' => $p->promo_min_beli
                    ];
                }
            }
        }
        return response()->json(['success' => true, "data" => $produk]);
    }

    public function getProdukSlug(Request $request) {
        $produk = $this->product->getBySlug($request->slug);
        $produk->variant = $this->product->getVariant($produk->id);
        $produk->foto = $this->product->getMainPhotoProduk($produk->id);

        foreach ($produk->detail_foto as $value) {
            $value->foto = $value->foto = Storage::url('images/produk/' . $value->media_link);
        }

        if($produk){
            return response()->json(['status_code' => 200, 'data' => $produk], 200);
        }
        else{
            return response()->json(['status_code' => 422, 'pesan' => 'Data Tidak ada'], 422);
        }
    }

    public function katalog(Request $request) {
        $params = (array) $request->all();
        $produk = $this->product->getAll($params);

        foreach ($produk['list'] as $key => $value) {
            $value->variant = $this->product->getVariant($value->id);
            $value->photo_product = $this->product->getPhoto($value->id);
            $value->foto = $this->product->getMainPhotoProduk($value->id);
            $value->rowspan = count($value->variant);
        }

        if ($produk){
            return response()->json(['status_code' => 200, 'data' => $produk], 200);
        } else{
            return response()->json(['status_code' => 422, 'pesan' => 'Data Tidak ada'], 422);
        }
    }

    public function getBrand(Request $request){
        try {
            $params = (array) $request->all();
            $data = $this->brand->getAll($params);

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

    public function getStok(Request $request){
        try {
            $params = (array) $request->all();
            $data = $this->product->stok($params);

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
}

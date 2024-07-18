<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProdukUlasan;
use App\Models\Wishlist;
use App\Models\PromoDet;
use App\Models\Service;
use App\Models\Produk;
use App\Models\Order;

class WishlistController extends Controller
{
    protected $wishlist;
    protected $order;
    protected $product;
    protected $promoDet;
    protected $produkUlasan;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['simpan']]);
        $this->wishlist = new Wishlist();
        $this->promoDet = new PromoDet();
        $this->product = new Produk();
        $this->order = new Order();
        $this->produkUlasan = new ProdukUlasan();
    }

    public function getData(Request $request){
        try {
            $params = (array) $request->all();
            $promo = $this->promoDet->getDetailPromoAktif();
            $produk = $this->wishlist->getAll($params);
    
            foreach ($produk['list'] as $value) {
                $value->variant = $this->product->getVariant($value->id);
                $value->photo_product = $this->product->getPhoto($value->id);
                $value->foto = $this->product->getMainPhotoProduk($value->id);
                $value->rating = $this->produkUlasan->getUlasanByProdukId($value->id)['rataRating'];
                $value->total_terjual = $this->order->getTotalTerjual($value->id)['total_terjual'];
    
                $value->rowspan = count($value->variant);
                $value->is_promo = false;
                $value->is_flashsale = false;
                $value->promo = [];
                $value->harga_promo = $value->harga;
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

    public function getDataById($id){
        try {
            $data = $this->wishlist->getById($id);

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
            $params = (array) $request->all('user_id', 'product_id');
            $data = Wishlist::create($params);

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

    public function update(Request $request){
        try {
            $service = new Service();
            $params = (array) $request->all('name', 'email', 'phone_code', 'phone_number', 'keterangan', 'file');
            
            $params['file'] = $service->saveImage("promo-slider/", $params['file']);
            $data = Wishlist::create($params);

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

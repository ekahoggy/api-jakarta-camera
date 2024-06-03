<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\PromoDet;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected $cart;
    protected $promoDet;

    public function __construct()
    {
        $this->cart = new Cart();
        $this->promoDet = new PromoDet();
    }

    public function getCart(Request $request) {
        $params = $request->all();
        $promo = $this->promoDet->getDetailPromoAktif();
        $data = $this->cart->getCart($params);

        foreach ($data as $key => $value) {
            $value->harga = $value->harga_varian !== null ? $value->harga_varian : $value->harga;
            $value->is_promo = false;
            $value->is_flashsale = false;
            $value->promo = [];
            $value->harga_asli = $value->harga;
            $value->harga_promo = $value->harga;
            foreach ($promo as $k => $p) {
                if($p->m_produk_id === $value->product_id){
                    $value->is_promo = true;
                    $hitungPromo = ($p->persen / 100) * $value->harga;
                    $value->harga_promo = $value->harga - $hitungPromo;
                    $value->harga = $value->harga - $hitungPromo;
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
            'data' => $data,
            'status_code' => 200,
            'message' => 'Successfully added to cart'
        ], 200);
    }

    public function addCart(Request $request) {
        $params = $request->all();

        $found = $this->cart->checkCart($params);

        if (isset($found) && !empty($found)) {
            $params['quantity'] += $found->quantity;
            $model = $this->cart->updateCart($params);
        } else {
            $model = $this->cart->insertCart($params);
        }

        if ($model === true || $model === 1) {
            return response()->json(['status_code' => 200, 'message' => 'Successfully added to cart'], 200);
        }

        return response()->json(['status_code' => 422, 'message' => 'An error occurred on the server'], 422);
    }

    public function updateCart(Request $request) {
        $params = $request->all();
        $model = $this->cart->changeCart($params);

        if ($model === true || $model === 1) {
            return response()->json(['status_code' => 200, 'message' => 'Successfully change quantity'], 200);
        }

        return response()->json(['status_code' => 422, 'message' => 'An error occurred on the server'], 422);
    }

    public function deleteCart(Request $request) {
        $params = $request->all();
        $model = $this->cart->deleteCart($params);

        if ($model === true || $model === 1) {
            return response()->json(['status_code' => 200, 'message' => 'Successfully delete cart'], 200);
        }

        return response()->json(['status_code' => 422, 'message' => 'An error occurred on the server'], 422);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\WoocommerceModel;
use Illuminate\Http\Request;

class WoocommerceController extends Controller
{
    protected $woocommerce;

     function __construct() {
        $this->woocommerce = new WoocommerceModel();
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

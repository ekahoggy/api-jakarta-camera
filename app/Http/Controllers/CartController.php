<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected $cart;

    public function __construct()
    {
        $this->cart = new Cart();
    }

    public function getCart(Request $request) {
        $params = $request->all();
        $data = $this->cart->getCart($params);

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

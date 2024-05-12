<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $order;

    public function __construct()
    {
        $this->order = new Order();
    }

    public function createOrder(Request $request) {
        $params = $request->only('data', "detail");
        $model = $this->order->createOrder($params["data"]);

        foreach($params["detail"] as $item) {
            $item["order_id"] = $model["id"];
            $this->order->createOrderDetail($item);
        }

        if (!empty($model)) {
            return response()->json(['status_code' => 200, 'message' => 'Successfully create order'], 200);
        }

        return response()->json(['status_code' => 422, 'message' => 'An error occurred on the server'], 422);
    }

    public function getOrder(Request $request) {
        $params = $request->all();
        $data = $this->order->getOrder($params);

        return response()->json([
            'data' => $data,
            'status_code' => 200,
            'message' => 'Successfully create order'
        ], 200);
    }
}

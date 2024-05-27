<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Xendit;
use Illuminate\Http\Request;
class OrderController extends Controller
{
    protected $order;
    protected $xendit;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['kategori', 'checkout', 'xenditCallback']]);
        $this->order = new Order();
        $this->xendit = new Xendit();
    }

    public function getData(Request $request){
        try {
            $params = (array) $request->all();
            $data = $this->order->getAll($params);

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

    public function getDataById($id){
        try {
            $data = $this->order->getById($id);

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
            $params = (array) $request->only('id', 'type', 'username', 'name', 'email', 'password', 'phone_code', 'phone_number', 'remember_token', 'address', 'photo', 'roles_id', 'kode', 'email_expired', 'is_active',);
            $data = $this->order->simpan($params);

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

    public function order() {
        $order = $this->order->getCustomer();

        if($order){
            return response()->json(['status_code' => 200, 'data' => $order], 200);
        }
        else{
            return response()->json(['status_code' => 422, 'pesan' => 'Data Tidak ada'], 422);
        }
    }

    public function createOrder(Request $request) {
        $params = $request->only('data', "detail");
        $model = $this->order->createOrder($params["data"]);

        foreach($params["detail"] as $item) {
            $item["order_id"] = $model["id"];
            $this->order->createOrderDetail($item);
        }

        dd($model);

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

    public function xenditCallback(Request $request) {
        $params = $request->all();
        dd($params);
        $data = $this->order->getOrder($params);

        return response()->json([
            'data' => $data,
            'status_code' => 200,
            'message' => 'Successfully create order'
        ], 200);
    }
}

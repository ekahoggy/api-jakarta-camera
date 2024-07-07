<?php

namespace App\Http\Controllers;

use App\Models\BiteShip;
use App\Models\Order;
use App\Models\Subscription;
use App\Models\Xendit;
use Illuminate\Http\Request;
class OrderController extends Controller
{
    protected $order;
    protected $xendit;
    protected $biteship;
    protected $subscribe;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['createOrder', 'kategori', 'checkout', 'xenditCallback']]);
        $this->order = new Order();
        $this->xendit = new Xendit();
        $this->biteship = new BiteShip();
        $this->subscribe = new Subscription();
    }

    function statusOrder($status) {
        if($status === 'ordered'){
            return 'Belum Bayar';
        }
        else if($status === 'processed'){
            return 'Konfirmasi';
        }
        else if($status === 'sent'){
            return 'Kirim';
        }
        else if($status === 'received'){
            return 'Selesai';
        }
        else{
            return 'Batal';
        }
    }

    public function getData(Request $request){
        try {
            $params = (array) $request->all();
            $data = $this->order->getAll($params);

            foreach ($data['list'] as $key => $value) {
                $value->status_order_convert = $this->statusOrder($value->status_order);
            }
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
            $data['data']->status_order_convert = $this->statusOrder($data['data']->status_order);

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
        $params = $request->only('data', "detail", "kurir", "voucher");
        if(isset($params['data']['subscribe'])){
            $dataSub = [
                'email' => $params['data']['email'],
                'created_by' => $params['data']['user_id'],
                'name' => $params['data']['recipient']
            ];
            $sub = $this->subscribe->getByEmail($params['data']['email']);
            if($sub){
                if($sub->is_subscribed === "0"){
                    $dataSub['is_subscribed'] = 1;
                    $dataSub['id'] = $sub->id;
                    $this->subscribe->edit($dataSub);
                }
            }
            else{
                $this->subscribe->post($dataSub);
                $this->subscribe->sendEmail($dataSub);
            }
        }
        $model = $this->order->createOrder($params);

        foreach($params["detail"] as $item) {
            $item["order_id"] = $model["id"];
            $this->order->createOrderDetail($item);
        }

        if (!empty($model)) {
            $model['email'] = $params['data']['email'];
            $generateInvoice = $this->xendit->createInvoice($model);
            $dataPayment['payment_type'] = 'x';
            $dataPayment['payment_total'] = $model['grand_total'];
            $dataPayment['payment_status'] = 'n';
            $dataPayment['payment_code']    = $generateInvoice['id'];
            $dataPayment['payment_expired'] = $generateInvoice['expiry_date'];
            $dataPayment['payment_link']    = $generateInvoice['invoice_url'];
            $payment = $this->order->payment($dataPayment);

            $updateOrder = [
                'id' => $model['id'],
                'payment_id' => $payment['payment_id']
            ];
            $this->order->updateOrderPaymentId($updateOrder);

            return response()->json(['status_code' => 200, 'message' => 'Successfully create order', 'link' => $generateInvoice['invoice_url']], 200);
        }

        return response()->json(['status_code' => 422, 'message' => 'An error occurred on the server'], 422);
    }

    public function kirim(Request $request) {
        $params = $request->all();
        $data = $this->biteship->order($params);

        return response()->json([
            'data' => $data,
            'status_code' => 200,
        ], 200);
    }

    public function getOrder(Request $request) {
        $params = $request->all();
        $data = $this->order->getOrder($params);

        return response()->json([
            'data' => $data,
            'status_code' => 200,
        ], 200);
    }

    public function changeStatus(Request $request) {
        $params = $request->all();
        $data = $this->order->updateOrder($params);

        return response()->json([
            'data' => $data,
            'status_code' => 200
        ], 200);
    }

    public function updatePengiriman(Request $request) {
        $params = $request->all();
        $data = $this->order->updatePengiriman($params);

        return response()->json([
            'data' => $data,
            'status_code' => 200
        ], 200);
    }

    public function xenditCallback(Request $request) {
        $params = $request->all();
        $status = $params['status'];

        if($status === 'PAID'){
            $paymentData = [
                'method' => $params['payment_method'],
                'payment_status' => 'p',
                'channel' => $params['bank_code'],
            ];

            $this->order->updateStatusOrder($params['id'], $params['external_id'], $paymentData);
        }

        return response()->json([
            'data' => [],
            'status_code' => 200
        ], 200);
    }
}

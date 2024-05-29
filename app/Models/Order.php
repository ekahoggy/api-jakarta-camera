<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid as Generator;

class Order extends Model
{
    use HasFactory;
    public $timestamps = true;
    protected $table = 't_order';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected $casts = [
        'id' => 'string'
    ];

    public function getOrder($params) {
        $orders = DB::table($this->table)
            ->select(
                't_order.id',
                't_order.product_id',
                't_order.user_id',
                't_order.quantity',
                'm_produk.nama',
                'm_produk.harga'
            )
            ->where($params)
            ->get();

        foreach($orders as $order) {
            $order->detail = DB::table('t_order_detail')
                ->select(
                    't_order_detail.id',
                    't_order_detail.product_id',
                    't_order_detail.user_id',
                    't_order_detail.quantity',
                    'm_produk.nama',
                    'm_produk.harga'
                )
                ->leftJoin('m_produk', 'm_produk.id', '=', 't_order_detail.product_id')
                ->get();
        }

        return $orders;
    }

    public function createOrder($params) {
        $payload = [];
        $payload['id'] = Generator::uuid4()->toString();
        $payload['invoice_number'] = $this->generateCodeInvoice();
        $payload["user_id"] = $params["user_id"];
        $payload["payment_id"] = "pay001";
        $payload["total"] = $params["total"];
        $payload["grand_total"] = $params["grand_total"];
        $payload["recipient"] = $params["recipient"];
        $payload["phone_code"] = $params["phone_code"];
        $payload["phone_number"] = $params["phone_number"];
        $payload["province_name"] = $params["province_name"];
        $payload["city_name"] = $params["city_name"];
        $payload["subdistrict_name"] = $params["subdistrict_name"];
        $payload["village_name"] = $params["village_name"];
        $payload["address"] = $params["address"];
        $payload["postal_code"] = $params["postal_code"];
        $payload["note"] = isset($params["note"]) ? $params["note"] : "";
        $payload["date"] = date("Y-m-d H:i:s");
        $payload["status_order"] = "ordered";

        // $payload['created_by'] = Auth::user()->id;
        $payload['created_at'] = date('Y-m-d H:i:s');

        DB::table($this->table)->insert($payload);

        return $payload;
    }

    public function createOrderDetail($params) {
        $payload = [];
        $payload["order_id"] = $params["order_id"];
        $payload["product_id"] = $params["product_id"];
        $payload["price"] = $params["price"];
        $payload["subtotal_price"] = $params["subtotal_price"];
        $payload["grandtotal_price"] = $params["grandtotal_price"];

        return DB::table("t_order_detail")->insert($payload);
    }

    private function generateCodeInvoice() {
        $totalOrder = DB::table('t_order')->where('date', "=", date("Y-m-d"))->count();
        $count = $totalOrder + 1;
        $date = date("ymd");

        //sequence tidak jalan
        if (strlen($count) == 1) {
            $sequence = "00$count";
        } else if (strlen($count) == 2) {
            $sequence = "0$count";
        } else if (strlen($count) == 3) {
            $sequence = "$count";
        }

        return "INV-JC/$date/".date('is');
    }

    public function getAll($params){
        $query = DB::table($this->table)
            ->select('t_order.*', 'users.name', 'users.username', 'users.email')
            ->leftJoin('users', 'users.id', '=', 't_order.user_id');

        $totalItems = $query->count();

        if (isset($params['notEqual']) && !empty($params['notEqual'])) {
            $query->where("id", "!=", $params['notEqual']);
        }

        if (isset($params['offset']) && !empty($params['offset'])) {
            $query->offset($params['offset']);
        }

        if (isset($params['limit']) && !empty($params['limit'])) {
            $query->limit($params['limit']);
        }

        $data = $query->orderBy('created_at', 'DESC')->get();

        return [
            'list' => $data,
            'totalItems' => $totalItems
        ];
    }

    public function getById($id){
        $data = DB::table('t_order')
            ->select('t_order.*', 'users.name', 'users.username', 'users.email')
            ->leftJoin('users', 'users.id', '=', 't_order.user_id')
            ->where('t_order.id', $id)
            ->first();

        $detail = DB::table('t_order_detail')
            ->select('t_order_detail.*', 'm_produk.nama', 'm_produk.sku')
            ->leftJoin('m_produk', 'm_produk.id', '=', 't_order_detail.product_id')
            ->where('order_id', $id)
            ->get();

        return [
            'data' => $data,
            'detail' => $detail
        ];
    }

    public function simpan($params) {
        if (isset($params['id']) && !empty($params['id'])) {
            return $this->updateOrder($params);
        } else {
            return $this->insertOrder($params);
        }
    }

    public function updateOrder($params) {
        $id = $params['id']; unset($params['id']);
        $data['status_order'] = $params['status_order'];
        $data['updated_by'] = Auth::user()->id;
        $data['updated_at'] = date('Y-m-d H:i:s');

        return DB::table('t_order')->where('id', $id)->update($data);
    }

    public function insertOrder($params) {
        $params['id'] = Generator::uuid4()->toString();
        $params['created_at'] = date('Y-m-d H:i:s');

        return DB::table('users')->insert($params);
    }

    public function payment($data) {
        if (isset($data['payment_id'])) {
            $model = DB::table('t_payment')->where('payment_id', $data['payment_id'])->update($data);
        } else {
            $data['payment_id'] = Generator::uuid4()->toString();
            DB::table('t_payment')->insert($data);
            $model = $data;
        }

        return $model;
    }

    public function updateOrderPaymentId($params) {
        $id = $params['id']; unset($params['id']);

        return DB::table('t_order')->where('id', $id)->update($params);
    }

    function updateStatusOrder($id, $inv, $params) {
        DB::table('t_payment')->where('payment_code', $id)->update($params);
        $status_order = $params['payment_status'] == 'p' ? 'processed' : 'ordered';
        $order = DB::table('t_order')->where('invoice_number', $inv)->update(['status_order' => $status_order]);

        return $order;
    }
}

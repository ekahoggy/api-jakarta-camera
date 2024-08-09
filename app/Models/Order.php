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
        $payload["user_id"] = $params['data']["user_id"];
        $payload["voucher_id"] = isset($params['voucher']) ? '' : $params['voucher']["id"];
        $payload["payment_id"] = "pay001";
        $payload["recipient"] = $params['data']["recipient"];
        $payload["phone_code"] = $params['data']["phone_code"];
        $payload["phone_number"] = $params['data']["phone_number"];
        $payload["province_name"] = $params['data']["province_name"];
        $payload["city_name"] = $params['data']["city_name"];
        $payload["subdistrict_name"] = $params['data']["subdistrict_name"];
        $payload["village_name"] = $params['data']["village_name"];
        $payload["address"] = $params['data']["address"];
        $payload["postal_code"] = $params['data']["postal_code"];
        $payload["latitude"] = $params['data']["latitude"];
        $payload["longitude"] = $params['data']["longitude"];
        $payload["note"] = isset($params['data']["note"]) ? $params['data']["note"] : "";
        $payload["date"] = date("Y-m-d H:i:s");

        $payload["shipping_service"] = $params['kurir']["courier_service_code"];
        $payload["shipping_sender"] = $params['kurir']["courier_name"];
        $payload["shipping_sender_code"] = $params['kurir']["courier_code"];
        $payload["shipping_etd"] = $params['kurir']["etd"];
        $payload["shipping_type"] = $params['kurir']["shipping_type"];
        $payload["shipping_group"] = $params['kurir']["service_type"];

        $payload["total_voucher"] = $params['data']["total_voucher"];
        $payload["total_pengiriman"] = $params['data']["total_pengiriman"];
        $payload["total_price"] = $params['data']["total_price"];
        $payload["grand_total"] = $params['data']["grand_total"];

        $payload["status_order"] = "ordered";

        $payload['created_by'] = $params['data']["user_id"];
        $payload['created_at'] = date('Y-m-d H:i:s');

        DB::table($this->table)->insert($payload);

        return $payload;
    }

    public function createOrderDetail($params) {
        $payload = [];
        $payload["order_id"] = $params["order_id"];
        $payload["product_id"] = $params["product_id"];
        $payload["price"] = $params["price"];
        $payload["subtotal"] = $params["subtotal_price"];

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
            ->select(
                't_order.*',
                'users.name',
                'users.username',
                'users.email'
            )
            ->leftJoin('users', 'users.id', '=', 't_order.user_id');

        if (isset($params['filter']) && !empty($params['filter'])) {
            $filter = json_decode($params['filter']);
            foreach ($filter as $key => $value) {
                if($key === 'invoice_number' && !empty($value)){
                    $query->where('t_order.invoice_number', 'like', '%' . $value . '%');
                }
                if($key === 'status_order' && !empty($value)){
                    $query->where('t_order.status_order', '=', $value);
                }
                if($key === 'date' && !empty($value)){
                    $query->where('t_order.date', '=', $value);
                }
            }
        }
        if (isset($params['notEqual']) && !empty($params['notEqual'])) {
            $query->where("id", "!=", $params['notEqual']);
        }

        if (isset($params['user_id']) && !empty($params['user_id'])) {
            $query->where("user_id", "=", $params['user_id']);
        }

        if (isset($params['bulan']) && !empty($params['bulan'])) {
            $query->whereMonth('date', '=', $params['bulan']);
        }

        if (isset($params['hari']) && !empty($params['hari'])) {
            $query->whereDate('date', '=', $params['hari']);
        }

        if (isset($params['tahun']) && !empty($params['tahun'])) {
            $query->whereYear('date', '=', $params['tahun']);
        }

        if (isset($params['offset']) && !empty($params['offset'])) {
            $query->offset($params['offset']);
        }

        if (isset($params['limit']) && !empty($params['limit'])) {
            $query->limit($params['limit']);
        }

        $query->orderBy('t_order.date', 'DESC');
        $data = $query->orderBy('t_order.created_at', 'DESC')->get();

        $totalItems = $query->count();
        return [
            'list' => $data,
            'totalItems' => $totalItems
        ];
    }

    public function getCounted(){
        $query = DB::table($this->table)
            ->select(
                array(
                    DB::raw("SUM(CASE WHEN t_order.status_order = 'ordered' THEN 1 ELSE 0 END) AS total_pending"),
                    DB::raw("SUM(CASE WHEN t_order.status_order = 'processed' THEN 1 ELSE 0 END) AS total_konfirmasi"),
                    DB::raw("SUM(CASE WHEN t_order.status_order = 'sent' THEN 1 ELSE 0 END) AS total_kirim"),
                    DB::raw("SUM(CASE WHEN t_order.status_order = 'received' THEN 1 ELSE 0 END) AS total_selesai"),
                    DB::raw("SUM(CASE WHEN t_order.status_order = 'canceled' THEN 1 ELSE 0 END) AS total_batal"),
                )
            );

        $data = $query->get();
        return $data;
    }

    public function getById($id){
        $data = DB::table('t_order')
            ->select('t_order.*', 'users.name', 'users.username', 'users.email')
            ->leftJoin('users', 'users.id', '=', 't_order.user_id')
            ->where('t_order.id', $id)
            ->first();

        $detail = DB::table('t_order_detail')
            ->select('t_order_detail.*', 'm_produk.*')
            ->leftJoin('m_produk', 'm_produk.id', '=', 't_order_detail.product_id')
            ->where('order_id', $id)
            ->get();

        return [
            'data' => $data,
            'detail' => $detail
        ];
    }

    public function getMultipleById($id){
        $data = DB::table('t_order')
            ->select('t_order.*', 'users.name', 'users.username', 'users.email')
            ->leftJoin('users', 'users.id', '=', 't_order.user_id')
            ->whereIn('t_order.id', $id)
            ->get();

        foreach ($data as $key => $value) {
            $data[$key]->detail = DB::table('t_order_detail')
            ->select('t_order_detail.*', 'm_produk.*')
            ->leftJoin('m_produk', 'm_produk.id', '=', 't_order_detail.product_id')
            ->where('order_id', $value->id)
            ->get();
        }
        return $data;
    }

    public function simpan($params) {
        if (isset($params['id']) && !empty($params['id'])) {
            return $this->updateOrder($params);
        } else {
            return $this->insertOrder($params);
        }
    }

    public function updatePengiriman($params){
        $id = $params['id']; unset($params['id']);
        $data = $params;
        $data['shipping_date'] = date('Y-m-d H:i:s');

        return DB::table('t_order')->where('id', $id)->update($data);
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

        DB::table('t_order_edukasi')->where('invoice_number', $inv)->update(['status_order' => $status_order]);
        $order = DB::table('t_order')->where('invoice_number', $inv)->update(['status_order' => $status_order]);

        return $order;
    }

    function getOrderUer($params) {
        $query = DB::table('t_order AS order')
            ->select(
                'order.id', 'order.invoice_number', 'order.total_voucher', 'order.total_pengiriman', 'order.total_price', 'order.grand_total',
                'order.status_order', 'order.date', 'order.shipping_sender', 'order.shipping_group', 'order.awb_shipping', 'order.recipient', 'order.phone_code', 'order.phone_number',
                'order.province_name', 'order.city_name', 'order.subdistrict_name', 'order.village_name', 'order.address', 'order.postal_code',
                'payment.channel', 'payment.method',
                'detail.promo_id', 'detail.product_id', 'detail.varian_id', 'detail.qty', 'detail.price', 'detail.promo_amount', 'detail.promo_percent', 'detail.subtotal',
                'produk.nama', 'ulasan.id as ulasan_id', 'ulasan.rating', 'ulasan.ulasan'
            )
            ->leftJoin('t_order_detail AS detail', 'detail.order_id', '=', 'order.id')
            ->leftJoin('t_payment AS payment', 'order.payment_id', '=', 'payment.payment_id')
            ->leftJoin('m_produk AS produk', 'produk.id', '=', 'detail.product_id')
            ->leftJoin('m_produk_ulasan AS ulasan', 'ulasan.m_produk_id', '=', 'produk.id');

        $totalItems = $query->count();

        if (isset($params['user_id']) && !empty($params['user_id'])) {
            $query->where("user_id", "=", $params['user_id']);
        }

        if (isset($params['nama']) && !empty($params['nama'])) {
            $query->where("nama", "like", "%".$params['nama']."%");
        }

        if (isset($params['status']) && !empty($params['status'])) {
            $query->where("status_order", "=", $params['status']);
        }

        if (isset($params['offset']) && !empty($params['offset'])) {
            $query->offset($params['offset']);
        }

        if (isset($params['limit']) && !empty($params['limit'])) {
            $query->limit($params['limit']);
        }

        $orders = $query->orderBy('date', 'ASC')->get();

        $data = [];
        foreach($orders as $i => $order) {
            $data[$i]['id'] = $order->id;
            $data[$i]['invoice_number'] = $order->invoice_number;
            $data[$i]['total_voucher'] = $order->total_voucher;
            $data[$i]['total_pengiriman'] = $order->total_pengiriman;
            $data[$i]['grand_total'] = $order->grand_total;
            $data[$i]['total_price'] = $order->total_price;
            $data[$i]['pay_channel'] = str_replace('_', ' ', $order->channel);
            $data[$i]['pay_method'] = str_replace('_', ' ', $order->method);
            $data[$i]['status_order'] = $order->status_order;
            $data[$i]['date'] = date('d M Y', strtotime($order->date));
            $data[$i]['shipping_sender'] = $order->shipping_sender;
            $data[$i]['shipping_group'] = $order->shipping_group;
            $data[$i]['awb_shipping'] = $order->awb_shipping;
            $data[$i]['recipient'] = $order->recipient;
            $data[$i]['phone_code'] = $order->phone_code;
            $data[$i]['phone_number'] = $order->phone_number;
            $data[$i]['province_name'] = $order->province_name;
            $data[$i]['city_name'] = $order->city_name;
            $data[$i]['subdistrict_name'] = $order->subdistrict_name;
            $data[$i]['village_name'] = $order->village_name;
            $data[$i]['address'] = $order->address;
            $data[$i]['postal_code'] = $order->postal_code;

            $data[$i]['detail'][$order->id]['nama'] = $order->nama;
            $data[$i]['detail'][$order->id]['promo_id'] = $order->promo_id;
            $data[$i]['detail'][$order->id]['product_id'] = $order->product_id;
            $data[$i]['detail'][$order->id]['varian_id'] = $order->varian_id;
            $data[$i]['detail'][$order->id]['qty'] = $order->qty;
            $data[$i]['detail'][$order->id]['price'] = $order->price;
            $data[$i]['detail'][$order->id]['promo_amount'] = $order->promo_amount;
            $data[$i]['detail'][$order->id]['promo_percent'] = $order->promo_percent;
            $data[$i]['detail'][$order->id]['subtotal'] = $order->subtotal;
            $data[$i]['detail'][$order->id]['ulasan_id'] = $order->ulasan_id;
            $data[$i]['detail'][$order->id]['ulasan'] = $order->ulasan;
            $data[$i]['detail'][$order->id]['rating'] = $order->rating;
        }

        foreach ($data as $i => $order) {
            $data[$i]['detail'] = array_values($data[$i]['detail']);
        }

        return [
            'list' => $data,
            'totalItems' => $totalItems
        ];
    }

    function getOrderEdukasi($params) {
        $query = DB::table('t_order_edukasi AS order')
            ->select(
                'order.id', 'order.invoice_number', 'order.total_promo', 'order.total_voucher', 'order.total_pembayaran', 'order.grand_total', 'order.status_order', 'order.date',
                'payment.channel', 'payment.method',
                'edukasi.judul', 'edukasi.gambar', 'edukasi.harga'
            )
            ->leftJoin('m_edukasi AS edukasi', 'order.edukasi_id', '=', 'edukasi.id')
            ->leftJoin('t_payment AS payment', 'order.payment_id', '=', 'payment.payment_id');

        $totalItems = $query->count();

        if (isset($params['user_id']) && !empty($params['user_id'])) {
            $query->where("user_id", "=", $params['user_id']);
        }

        if (isset($params['judul']) && !empty($params['judul'])) {
            $query->where("judul", "like", "%".$params['judul']."%");
        }

        if (isset($params['status']) && !empty($params['status'])) {
            $query->where("status_order", "=", $params['status']);
        }

        if (isset($params['offset']) && !empty($params['offset'])) {
            $query->offset($params['offset']);
        }

        if (isset($params['limit']) && !empty($params['limit'])) {
            $query->limit($params['limit']);
        }

        $orders = $query->orderBy('date', 'ASC')->get();

        return [
            'list' => $orders,
            'totalItems' => $totalItems
        ];
    }

    public function generateLaporan($params) {
        $orders = DB::table('t_order_detail')
            ->select(
                't_order.*',
                't_order_detail.*',
                'm_produk.nama',
                'm_produk.harga',
                'm_produk.sku'
            )
            ->leftJoin('t_order', 't_order.id', '=', 't_order_detail.order_id')
            ->leftJoin('m_produk', 'm_produk.id', '=', 't_order_detail.product_id')
            ->get();

        return $orders;
    }

    public function getTotalTerjual($id){
        $query = DB::table($this->table)
            ->select('t_order.status_order')
            ->leftJoin('t_order_detail', 't_order.id', '=', 't_order_detail.order_id')
            ->where('t_order.status_order', '=', 'received')
            ->where('t_order_detail.product_id', '=',$id);

        $totalTerjual = $query->count();

        return [
            'total_terjual' => $this->formatNominal($totalTerjual),
        ];
    }

    private function formatNominal($number) {

        // Jika nominal kurang dari 1000, tidak perlu mengubahnya
        if ($number < 1000) {
            return $number;
        }

        // Jika nominal lebih dari atau sama dengan 1000 dan kurang dari 1 juta
        if ($number < 1000000) {
            $formatted = $number / 1000;

            // Pilih format yang diinginkan, bisa 'rb' atau 'k'
            return round($formatted, 2) . 'rb'; // atau 'k' untuk format internasional
        }

        // Jika nominal lebih dari atau sama dengan 1 juta
        if ($number >= 1000000) {
            $formatted = $number / 1000000;
            return round($formatted, 2) . 'jt';
        }
    }
}

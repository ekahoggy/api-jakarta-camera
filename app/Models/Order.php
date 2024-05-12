<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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

    public function getOrder($params) {
        $data = DB::table($this->table)
            ->select(
                't_order.id',
                't_order.product_id',
                't_order.user_id',
                't_order.quantity',
                'm_produk.nama',
                'm_produk.harga'
            )
            ->leftJoin('m_produk', 'm_produk.id', '=', 't_order.product_id')
            ->where($params)
            ->get();

        return $data;
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
        $count = $totalOrder == 0 ? 1 : $totalOrder;
        $date = date("ymd");

        if (strlen($count) == 1) {
            $sequence = "00$count"; 
        } else if (strlen($count) == 2) {
            $sequence = "0$count"; 
        } else if (strlen($count) == 3) {
            $sequence = "$count"; 
        }

        return "INV/$date/$sequence";
    }
}

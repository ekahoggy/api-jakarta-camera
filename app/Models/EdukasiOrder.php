<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid as Generator;
use Illuminate\Support\Facades\DB;
use App\Models\Xendit;

class EdukasiOrder extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 't_order_edukasi';

    protected $fillable = [
        'invoice_number',
        'user_id',
        'payment_id',
        'voucher_id',
        'promo_id',
        'edukasi_id',
        'total_voucher',
        'total_promo',
        'total_pembayaran',
        'grand_total',
        'note',
        'date',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];

    protected $casts = [
        'id' => 'string'
    ];

    public function getAll($params){
        $query = DB::table($this->table)
            ->select(
                't_order_edukasi.*',
                'm_edukasi.kategori_id',
                'm_edukasi.judul',
                'm_edukasi.slug',
                'm_edukasi.gambar',
                'm_edukasi.harga',
                'm_edukasi.tingkatan',
                'm_edukasi.is_publish',
                'm_edukasi_kategori.kategori',
                'users.name',
                'users.username',
                'users.email'
            )
            ->leftJoin('users', 'users.id', '=', 't_order_edukasi.user_id')
            ->leftJoin('m_edukasi', 'm_edukasi.id', '=', 't_order_edukasi.edukasi_id')
            ->leftJoin('m_edukasi_kategori', 'm_edukasi_kategori.id', '=', 'm_edukasi.kategori_id');

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

        if (isset($params['offset']) && !empty($params['offset'])) {
            $query->offset($params['offset']);
        }

        if (isset($params['limit']) && !empty($params['limit'])) {
            $query->limit($params['limit']);
        }

        $data = $query->orderBy('created_at', 'DESC')->get();
        $totalItems = $query->count();
        return [
            'list' => $data,
            'totalItems' => $totalItems
        ];
    }

    public function getById($id){
        $data = DB::table($this->table)
            ->select(
                't_order_edukasi.*',
                'm_edukasi.kategori_id',
                'm_edukasi.judul',
                'm_edukasi.slug',
                'm_edukasi.gambar',
                'm_edukasi.harga',
                'm_edukasi.tingkatan',
                'm_edukasi.is_publish',
                'm_edukasi_kategori.kategori',
                't_payment.channel',
                't_payment.method',
                'users.name',
                'users.username',
                'users.email'
            )
            ->leftJoin('users', 'users.id', '=', 't_order_edukasi.user_id')
            ->leftJoin('m_edukasi', 'm_edukasi.id', '=', 't_order_edukasi.edukasi_id')
            ->leftJoin('m_edukasi_kategori', 'm_edukasi_kategori.id', '=', 'm_edukasi.kategori_id')
            ->leftJoin('t_payment', 't_payment.payment_id', '=', 't_order_edukasi.payment_id')
            ->where('t_order_edukasi.id', $id)
            ->first();

        $detail = DB::table('m_edukasi_video')
            ->select('m_edukasi_video.*', 'm_edukasi.*')
            ->leftJoin('m_edukasi', 'm_edukasi.id', '=', 'm_edukasi_video.edukasi_id')
            ->where('m_edukasi.id', $data->edukasi_id)
            ->get();

        return [
            'data' => $data,
            'detail' => $detail
        ];
    }

    public function createOrder($params){
        $id = Generator::uuid4()->toString();
        $xendit = new Xendit();

        $t_inv['invoice_number'] = $this->generateCodeInvoice();
        $t_inv['grand_total'] = $params['checkout']['total'];

        $generateInvoice = $xendit->createInvoice($t_inv);
        $dataPayment['payment_type'] = 'x';
        $dataPayment['payment_total'] = $t_inv['grand_total'];
        $dataPayment['payment_status'] = 'n';
        $dataPayment['payment_code']    = $generateInvoice['id'];
        $dataPayment['payment_expired'] = $generateInvoice['expiry_date'];
        $dataPayment['payment_link']    = $generateInvoice['invoice_url'];
        $payment = $this->payment($dataPayment);

        $data = [
            'id' => $id,
            'invoice_number' => $t_inv['invoice_number'],
            'payment_id' => $payment['payment_id'],
            'user_id' => $params['data']['user_id'],
            'voucher_id' => isset($params['data']['voucher_id']) ? $params['data']['voucher_id'] : '',
            'promo_id' => isset($params['data']['promo_id']) ? $params['data']['promo_id'] : '',
            'edukasi_id' => $params['data']['id'],
            'total_voucher' => $params['checkout']['voucher'],
            'total_promo' => $params['checkout']['promo'],
            'total_pembayaran' => $params['checkout']['harga'],
            'grand_total' => $params['checkout']['total'],
            'date' => date('Y-m-d H:i:s')
        ];

        DB::table($this->table)->insert($data);

        return $payment;
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

    private function generateCodeInvoice() {
        $totalOrder = DB::table($this->table)->where('date', "=", date("Y-m-d"))->count();
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

        return "EDU-JC/$date/".date('is');
    }
}

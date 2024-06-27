<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function createOrder($params){

    }
}

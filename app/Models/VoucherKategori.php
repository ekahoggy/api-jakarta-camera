<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid as Generator;
use Illuminate\Support\Facades\DB;

class VoucherKategori extends Model
{
    use HasFactory;

    protected $table = 'm_voucher_kategori';

    protected $fillable = [
        'id',
        'voucher_id',
        'kategori_id',
        'brand_id'
    ];

    protected $casts = [
        'id' => 'string'
    ];

    public function post($params) {
        $service = new Service();
        $params['id'] = Generator::uuid4()->toString();

        DB::table($this->table)->insert($params);
        return $params;
    }
}

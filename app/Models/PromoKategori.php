<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid as Generator;
use Illuminate\Support\Facades\DB;


class PromoKategori extends Model
{
    use HasFactory;

    protected $table = 'm_promo_kategori';

    protected $fillable = [
        'id',
        'promo_id',
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

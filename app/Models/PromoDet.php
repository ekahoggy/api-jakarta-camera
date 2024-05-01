<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PromoDet extends Model
{
    use HasFactory;

    protected $table = 'm_promo_det';

    protected $fillable = [
        'id',
        'm_promo_id',
        'm_produk_id',
        'persen',
        'nominal',
        'promo_used',
        'qty'
    ];

    protected $casts = [
        'id' => 'string'
    ];

    public function getDetailByPromo($id_promo = null){
        $query = DB::table($this->table);
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
}

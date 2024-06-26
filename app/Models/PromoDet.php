<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid as Generator;
use Illuminate\Support\Facades\Auth;

class PromoDet extends Model
{
    use HasFactory;

    protected $table = 'm_promo_det';

    protected $fillable = [
        'id',
        'm_promo_id',
        'm_produk_id',
        'm_edukasi_id',
        'persen',
        'nominal',
        'promo_used',
        'qty',
        'status',
    ];

    protected $casts = [
        'id' => 'string'
    ];

    public function getDetailByPromo($id_promo = null){
        $query = DB::table($this->table)
        ->select(
            'm_promo_det.*',
            'm_produk.id as m_produk_id',
            'm_produk.nama',
            'm_produk.sku',
            'm_produk.harga'
        )
        ->leftJoin('m_produk', 'm_produk.id', '=', 'm_promo_det.m_produk_id');

        if ($id_promo !== null) {
            $query->where("m_promo_id", "=", $id_promo);
        }
        if (isset($params['notEqual']) && !empty($params['notEqual'])) {
            $query->where("id", "!=", $params['notEqual']);
        }

        if (isset($params['offset']) && !empty($params['offset'])) {
            $query->offset($params['offset']);
        }

        if (isset($params['limit']) && !empty($params['limit'])) {
            $query->limit($params['limit']);
        }

        $data = $query->get();

        return $data;
    }

    public function simpan($params) {
        if (isset($params['id']) && !empty($params['id'])) {
            return $this->updatePromoDet($params);
        } else {
            return $this->insertPromoDet($params);
        }
    }

    public function updatePromoDet($params) {
        $id = $params['id']; unset($params['id']);
        return DB::table($this->table)->where('id', $id)->update($params);
    }

    public function insertPromoDet($params) {
        $params['id'] = Generator::uuid4()->toString();
        return DB::table($this->table)->insert($params);
    }

    public function getDetailPromoAktif(){
        $query = DB::table($this->table)
        ->select(
            'm_promo.*',
            'm_promo_det.*',
            'm_produk.id as m_produk_id',
            'm_produk.nama',
            'm_produk.sku',
            'm_produk.harga'
        )
        ->leftJoin('m_promo', 'm_promo.id', '=', 'm_promo_det.m_promo_id')
        ->leftJoin('m_produk', 'm_produk.id', '=', 'm_promo_det.m_produk_id')
        ->where('m_promo.type', 'produk')
        ->where('m_promo.is_status', 1)
        ->where('m_promo_det.status', 1);
        return $query->get();
    }
}

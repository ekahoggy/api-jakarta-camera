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
            'm_produk.m_kategori_id',
            'm_produk.m_brand_id',
            'm_produk.nama',
            'm_produk.stok',
            'm_produk.sku',
            'm_produk.harga',
            'm_kategori.slug as slug_kategori',
            'm_kategori.kategori as kategori',
            'm_brand.brand as brand',
            'm_brand.slug as slug_brand'
        )
        ->leftJoin('m_produk', 'm_produk.id', '=', 'm_promo_det.m_produk_id')
        ->leftJoin('m_kategori', 'm_kategori.id', '=', 'm_produk.m_kategori_id')
        ->leftJoin('m_brand', 'm_brand.id', '=', 'm_produk.m_brand_id');

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

    public function getKategoriByPromo($id_promo = null){
        $query = DB::table('m_promo_kategori')
        ->select(
            'm_promo_kategori.*',
            'prd.slug as slug_kategori',
            'prd.kategori as kategori',
            'edu.slug as slug_kategori',
            'edu.kategori as kategori',
            'm_brand.brand as brand',
            'm_brand.slug as slug_brand'
        )
        ->leftJoin('m_kategori as prd', 'prd.id', '=', 'm_promo_kategori.kategori_id')
        ->leftJoin('m_edukasi_kategori as edu', 'edu.id', '=', 'm_promo_kategori.kategori_id')
        ->leftJoin('m_brand', 'm_brand.id', '=', 'm_promo_kategori.brand_id');

        $query->where("m_promo_kategori.promo_id", "=", $id_promo);

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

    public function getDetailPromoAktif($type = 'produk'){
        $date = date('Y-m-d');
        $time = date('H:i:s');
        $query = DB::table($this->table)
        ->select(
            'm_promo.id',
            'm_promo.kode',
            'm_promo.type',
            'm_promo.promo',
            'm_promo.tanggal_mulai',
            'm_promo.jam_mulai',
            'm_promo.tanggal_selesai',
            'm_promo.jam_selesai',
            'm_promo.promo_min_beli',
            'm_promo.is_status',
            'm_promo_det.*',
            'm_produk.id as m_produk_id',
            'm_produk.nama',
            'm_produk.sku',
            'm_produk.m_kategori_id',
            'm_produk.m_brand_id',
            'm_produk.harga',
            'm_kategori.kategori as kategori_produk',
        )
        ->leftJoin('m_promo', 'm_promo.id', '=', 'm_promo_det.m_promo_id')
        ->leftJoin('m_produk', 'm_produk.id', '=', 'm_promo_det.m_produk_id')
        ->leftJoin('m_kategori', 'm_kategori.id', '=', 'm_produk.m_kategori_id')
        ->where('m_promo.type', $type)
        ->where('m_promo.is_status', 1)
        ->where('m_promo.is_flashsale', 0);

        $query->where(function ($query) use ($date, $time) {
            $query->where(function ($query) use ($date) {
                $query->whereDate('m_promo.tanggal_mulai', '<=', $date)
                    ->whereDate('m_promo.tanggal_selesai', '>=', $date);
            })
            ->where(function ($query) use ($date, $time) {
                $query->whereDate('m_promo.tanggal_mulai', '=', $date)
                    ->whereTime('m_promo.jam_mulai', '<=', $time);

                $query->orWhere(function ($query) use ($date, $time) {
                    $query->whereDate('m_promo.tanggal_selesai', '>', $date);
                });
            });
        });

        return $query->get();
    }

    public function getFlashsale(){
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
        ->where('m_promo.is_flashsale', 1)
        ->where('m_promo_det.status', 1);
        return $query->get();
    }
}

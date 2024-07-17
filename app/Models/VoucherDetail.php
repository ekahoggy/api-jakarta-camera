<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid as Generator;

class VoucherDetail extends Model
{
    use HasFactory;

    protected $table = 'm_voucher_det';

    protected $fillable = [
        'id',
        'voucher_id',
        'produk_id',
        'edukasi_id',
        'persen',
        'nominal',
        'used',
        'qty',
        'status',
    ];

    protected $casts = [
        'id' => 'string'
    ];

    public function getDetailByVoucher($id_voucher = null){
        $query = DB::table($this->table)
        ->select(
            'm_voucher_det.*',
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
        ->leftJoin('m_produk', 'm_produk.id', '=', 'm_voucher_det.produk_id')
        ->leftJoin('m_kategori', 'm_kategori.id', '=', 'm_produk.m_kategori_id')
        ->leftJoin('m_brand', 'm_brand.id', '=', 'm_produk.m_brand_id');

        if ($id_voucher !== null) {
            $query->where("voucher_id", "=", $id_voucher);
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

    public function getKategoriByVoucher($id_voucher = null){
        $query = DB::table('m_voucher_kategori')
        ->select(
            'm_voucher_kategori.*',
            'prd.slug as slug_kategori',
            'prd.kategori as kategori',
            'edu.slug as slug_kategori',
            'edu.kategori as kategori',
            'm_brand.brand as brand',
            'm_brand.slug as slug_brand'
        )
        ->leftJoin('m_kategori as prd', 'prd.id', '=', 'm_voucher_kategori.kategori_id')
        ->leftJoin('m_edukasi_kategori as edu', 'edu.id', '=', 'm_voucher_kategori.kategori_id')
        ->leftJoin('m_brand', 'm_brand.id', '=', 'm_voucher_kategori.brand_id');

        $query->where("m_voucher_kategori.voucher_id", "=", $id_voucher);

        $data = $query->get();

        return $data;
    }

    public function simpan($params) {
        if (isset($params['id']) && !empty($params['id'])) {
            return $this->updateVoucherDet($params);
        } else {
            return $this->insertVoucherDet($params);
        }
    }

    public function updateVoucherDet($params) {
        $id = $params['id']; unset($params['id']);
        return DB::table($this->table)->where('id', $id)->update($params);
    }

    public function insertVoucherDet($params) {
        $params['id'] = Generator::uuid4()->toString();
        return DB::table($this->table)->insert($params);
    }

    public function getDetailVoucherAktif($type = 'produk'){
        $date = date('Y-m-d');
        $time = date('H:i:s');
        $query = DB::table($this->table)
        ->select(
            'm_voucher.id',
            'm_voucher.redeem_code',
            'm_voucher.jenis',
            'm_voucher.gambar',
            'm_voucher.voucher',
            'm_voucher.tanggal_mulai',
            'm_voucher.jam_mulai',
            'm_voucher.tanggal_selesai',
            'm_voucher.jam_selesai',
            'm_voucher.kategori',
            'm_voucher.voucher_used',
            'm_voucher.qty',
            'm_voucher.type',
            'm_voucher.voucher_value',
            'm_voucher.voucher_max',
            'm_voucher.voucher_min_beli',
            'm_voucher.is_hidden',
            'm_voucher.is_status',
            'm_voucher.untuk',
            'm_voucher.used_to',
            'm_voucher.for_co',
            'm_voucher_det.*',
            'm_produk.id as m_produk_id',
            'm_produk.nama',
            'm_produk.sku',
            'm_produk.m_kategori_id',
            'm_produk.m_brand_id',
            'm_produk.harga',
            'm_kategori.kategori as kategori_produk',
        )
        ->leftJoin('m_voucher', 'm_voucher.id', '=', 'm_voucher_det.voucher_id')
        ->leftJoin('m_produk', 'm_produk.id', '=', 'm_voucher_det.produk_id')
        ->leftJoin('m_kategori', 'm_kategori.id', '=', 'm_produk.m_kategori_id')
        ->where('m_voucher.type', $type)
        ->where('m_voucher.is_status', 1);

        $query->where(function ($query) use ($date, $time) {
            $query->where(function ($query) use ($date) {
                $query->whereDate('m_voucher.tanggal_mulai', '<=', $date)
                    ->whereDate('m_voucher.tanggal_selesai', '>=', $date);
            })
            ->where(function ($query) use ($date, $time) {
                $query->whereDate('m_voucher.tanggal_mulai', '=', $date)
                    ->whereTime('m_voucher.jam_mulai', '<=', $time);

                $query->orWhere(function ($query) use ($date, $time) {
                    $query->whereDate('m_voucher.tanggal_selesai', '>', $date);
                });
            });
        });

        return $query->get();
    }
}

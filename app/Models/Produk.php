<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Uuid as Generator;
use Illuminate\Support\Facades\DB;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'm_produk';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'sku',
        'm_kategori_id',
        'nama',
        'slug',
        'type',
        'harga',
        'link_tokped',
        'link_shopee',
        'link_bukalapak',
        'link_lazada',
        'link_blibli',
        'detail_produk',
        'deskripsi',
        'in_box',
        'tags',
        'min_beli',
        'link_video',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'id' => 'string'
    ];

    public function getAll($params = []){
        $query = DB::table('m_produk')
            ->selectRaw('m_produk.*, m_kategori.slug as slug_kategori, m_kategori.kategori, m_produk_media.media_link')
            ->leftJoin('m_kategori', 'm_kategori.id', '=', 'm_produk.m_kategori_id')
            ->leftJoin('m_produk_media', 'm_produk_media.m_produk_id', '=', 'm_produk.id')
            ->where('m_produk_media.is_main', 1);

        $totalItems = $query->count();

        if (isset($params['kategori']) && !empty($params['kategori'])) {
            $query->where("m_kategori.slug", "!=", $params['kategori']);
        }

        // if (isset($params['price_start']) && !empty($params['price_start'])) {
        //     $query->where("m_kategori.slug", "!=", $params['kategori']);
        // }

        if (isset($params['offset']) && !empty($params['offset'])) {
            $query->offset($params['offset']);
        }

        if (isset($params['limit']) && !empty($params['limit'])) {
            $query->limit($params['limit']);
        }

        $data = $query->orderBy('m_produk.created_at', 'DESC')->get();

        return [
            'list' => $data,
            'totalItems' => $totalItems
        ];
    }

    public function getBySlug($slug){
        $query = DB::table('m_produk')
                ->selectRaw('m_produk.*, m_kategori.slug as slug_kategori, m_kategori.kategori, m_produk_media.media_link')
                ->leftJoin('m_kategori', 'm_kategori.id', '=', 'm_produk.m_kategori_id')
                ->leftJoin('m_produk_media', 'm_produk_media.m_produk_id', '=', 'm_produk.id')
                ->where('m_produk_media.is_main', 1)
                ->where('m_produk.slug', $slug)
                ->first();

        $query->detail_foto = DB::table('m_produk_media')->where('m_produk_id', $query->id)->get();

        return $query;
    }
}

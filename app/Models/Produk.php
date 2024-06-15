<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid as Generator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
            ->selectRaw('
                m_produk.*,
                m_kategori.kategori,
                m_kategori.slug as slug_kategori,
                m_brand.brand,
                m_brand.slug as slug_brand
            ')
            ->leftJoin('m_kategori', 'm_kategori.id', '=', 'm_produk.m_kategori_id')
            ->leftJoin('m_brand', 'm_brand.id', '=', 'm_produk.m_brand_id');

        if (isset($params['filter']) && !empty($params['filter'])) {
            $filter = json_decode($params['filter']);
            foreach ($filter as $key => $value) {
                if($key === 'nama'){
                    if(!empty($value)){
                        $query->where('nama', 'like', '%' . $value . '%');
                    }
                    // $query->orWhere('sku', 'like', '%' . $value . '%');
                }
                if($key === 'm_kategori_id'){
                    if($value !== null){
                        $query->where('m_kategori_id', $value);
                    }
                }
                if($key === 'm_brand_id'){
                    if($value !== null){
                        $query->where('m_brand_id', $value);
                    }
                }
                if($key === 'is_active'){
                    if($value !== null){
                        $query->where('is_active', $value);
                    }
                }
                if($key == 'category'){
                    if(!empty($value) && isset($value)){
                        $query->whereIn('m_kategori.slug', $value);
                    }
                }
                if($key == 'brand'){
                    if(!empty($value) && isset($value)){
                        $query->whereIn('m_brand.slug', $value);
                    }
                }
            }
        }

        if (isset($params['kategori']) && !empty($params['kategori'])) {
            $query->where('m_kategori.slug', $params['kategori']);
        }

        if (isset($params['brand']) && !empty($params['brand'])) {
            $query->where('m_brand.slug', $params['brand']);
        }

        if (isset($params['offset']) && !empty($params['offset'])) {
            $query->offset($params['offset']);
        }

        if (isset($params['limit']) && !empty($params['limit'])) {
            $query->limit($params['limit']);
        }

        $data = $query->orderBy('m_produk.created_at', 'DESC')->get();
        $totalItems = $query->count();
        return [
            'list' => $data,
            'totalItems' => $totalItems
        ];
    }

    public function getBySlug($slug){
        $query = DB::table('m_produk')
                ->selectRaw('m_produk.*, m_kategori.slug as slug_kategori, m_kategori.kategori, m_brand.brand, m_brand.slug as slug_brand,m_produk_media.media_link')
                ->leftJoin('m_kategori', 'm_kategori.id', '=', 'm_produk.m_kategori_id')
                ->leftJoin('m_brand', 'm_brand.id', '=', 'm_produk.m_brand_id')
                ->leftJoin('m_produk_media', 'm_produk_media.m_produk_id', '=', 'm_produk.id')
                ->where('m_produk_media.is_main', 1)
                ->where('m_produk.slug', $slug)
                ->first();

        $query->detail_foto = DB::table('m_produk_media')->where('m_produk_id', $query->id)->get();

        return $query;
    }

    public function simpan($params) {
        if (isset($params['id']) && !empty($params['id'])) {
            return $this->updateProduct($params);
        } else {
            return $this->insertProduct($params);
        }
    }

    public function updateProduct($params) {

        $id = $params['id']; unset($params['id']);
        $params['slug'] = Str::slug($params['nama'], '-');
        $params['updated_at'] = date('Y-m-d H:i:s');

        if (isset($params['photo']) && !empty(isset($params['photo']))) {
            $this->savePhoto($id, $params['photo']);
            unset($params['photo']);
        }

        if (isset($params['variant']) && !empty(isset($params['variant']))) {
            $this->saveVariant($id, $params['variant']);
            unset($params['variant']);
        }

        return DB::table('m_produk')->where('id', $id)->update($params);
    }

    public function insertProduct($params) {
        $params['id'] = Generator::uuid4()->toString();
        $params['sku'] = isset($params['sku']) ? $params['sku'] : 'JC-'.date('ymdhms');
        $params['slug'] = Str::slug($params['nama'], '-');
        $params['updated_at'] = date('Y-m-d H:i:s');

        if (isset($params['photo']) && !empty(isset($params['photo']))) {
            $this->savePhoto($params['id'], $params['photo']);
            unset($params['photo']);
        }

        if (isset($params['variant']) && !empty(isset($params['variant']))) {
            $this->saveVariant($params['id'], $params['variant']);
            unset($params['variant']);
        }

        return DB::table('m_produk')->insert($params);
    }

    public function savePhoto($produkId, $photo) {
        $service = new Service();

        DB::table('m_produk_media')->where('m_produk_id', '=', $produkId)->delete();
        foreach($photo as $i => $image) {
            $data = [];
            if($image['isFoto']){
                $data['id'] = Generator::uuid4()->toString();
                $data['m_produk_id'] = $produkId;
                $data['is_main'] = $i == 0 ? 1 : 0;
                $data['urutan'] = $i + 1;
                $data['media_link'] = $service->saveImage("produk/", $image['foto']);

                DB::table('m_produk_media')->insert($data);
            }
        }
    }

    public function saveVariant($produkId, $listVariant) {
        $service = new Service();

        foreach($listVariant as $variant) {
            $variant['m_produk_id'] = $produkId;
            if (isset($variant['image']) && !empty(isset($variant['image']))) {
                $variant['image'] = $service->saveImage("produk-variant/", $variant['image']);
            }

            if (isset($variant['id']) && !empty(isset($variant['id']))) {
                $id = $variant['id']; unset($variant['id']);
                DB::table('m_produk_varian')->where('id', $id)->update($variant);
            } else {
                $variant['id'] = Generator::uuid4()->toString();
                DB::table('m_produk_varian')->insert($variant);
            }
        }
    }

    public function getPhoto($produkId) {
        $photo = DB::table('m_produk_media')->where('m_produk_id', $produkId)
        ->orderBy('urutan', 'ASC')
        ->get();

        if (!empty($photo)) {
            foreach($photo as $i => $image) {
                if($image->media_link !== ''){
                    $photo[$i]->foto = Storage::url('images/produk/' . $image->media_link);
                    $photo[$i]->isFoto = true;
                }
            }
        }

        return $photo;
    }

    public function getVariant($produkId) {
        $listVariant = DB::table('m_produk_varian')->where('m_produk_id', $produkId)->get();

        $varian1 = '';
        $varian2 = '';
        $group_varian = [];
        $arr_varian1 = [];
        if(isset($listVariant)){
            foreach ($listVariant as $product) {
                $product->image = Storage::url('images/produk-variant/' . $product->image);
                if($product->varian1 !== null || $product->varian1 !== ''){
                    $varian1 = $product->varian1_type;
                    //set detail varian 1
                    $v1 = $product->varian1;
                    if (!isset($arr_varian1[$v1])) {
                        $arr_varian1[$v1] = [];
                    }
                    $arr_varian1[$v1][] = $product;
                }

                if($product->varian2 !== null || $product->varian2 !== ''){
                    $varian2 = $product->varian2_type;
                    //set detail varian 2
                    $v2 = $product->varian2;
                    if (!isset($arr_varian2[$v2])) {
                        $arr_varian2[$v2] = [];
                    }
                    $arr_varian2[$v2][] = $product;
                }
            }

            $detail_varian1 = [];
            if(isset($arr_varian1)){
                foreach ($arr_varian1 as $k => $value) {
                    array_push($detail_varian1, $k);
                }
            }
            $detail_varian2 = [];
            if(isset($arr_varian2)){
                foreach ($arr_varian2 as $k => $value) {
                    array_push($detail_varian2, $k);
                }
            }

            $group_varian = [
                'all_varian'        => $listVariant,
                'varian1'           => $varian1,
                'varian1_detail'    => $detail_varian1,
                'varian2'           => $varian2,
                'varian2_detail'    => $detail_varian2,
            ];

            return $group_varian;
        }

        return [];
    }

    public function varian($type) {
        if($type === 'ukuran'){
            $data = DB::table('m_varian_ukuran')->get();
        }else if($type === 'warna'){
            $data = DB::table('m_varian_warna')->get();
        }

        return $data;
    }

    public function updateStok($params) {
        DB::table('m_produk_varian')->where('id', $params['id'])->update(['stok' => $params['stok']]);

        return true;
    }

    public function updateStokProduk($params) {
        DB::table('m_produk')->where('id', $params['id'])->update(['stok' => $params['stok']]);

        return true;
    }

    public function ubahStatus($params) {
        DB::table('m_produk')->where('id', $params['id'])->update(['is_active' => $params['is_active']]);

        return true;
    }

    public function getMainPhotoProduk($id) {
        $data = DB::table('m_produk_media')->where('m_produk_id', $id)->where('is_main', 1)->first();
        $url = Storage::url('images/produk/' . $data->media_link);

        return $url;
    }

    public function stok($param) {
        $query = DB::table('m_produk_varian')
        ->where('m_produk_id', $param['m_produk_id'])
        ->where('varian1', $param['varian1']);

        if(isset($param['varian2'])){
            $query->where('varian2', $param['varian2']);
        }
        $data = $query->first();
        //set image
        $data->foto = Storage::url('images/produk-variant/' . $data->image);

        return $data;
    }

}

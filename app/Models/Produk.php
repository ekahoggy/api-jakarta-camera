<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid as Generator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Produk extends Model
{
    use HasFactory;
    private $WOOCOMMERCE_STORE_URL;
    private $WOOCOMMERCE_CONSUMER_KEY;
    private $WOOCOMMERCE_CONSUMER_SECRET;

    public function __construct() {
        $this->WOOCOMMERCE_STORE_URL = 'https://jakartacamera.com';
        $this->WOOCOMMERCE_CONSUMER_KEY = 'ck_c46c613c61a7afda407d20f59593c4cc1523d22e';
        $this->WOOCOMMERCE_CONSUMER_SECRET = 'cs_6aa1f62f6dd3539d47ac1cb528d954f276b60352';
    }

    protected $table = 'm_produk';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'woo_produk_id',
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
        'min_beli',
        'berat',
        'tinggi',
        'panjang',
        'lebar',
        'stok',
        'stok_status',
        'link_video',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'id' => 'string'
    ];

    public function getAll($params = []){
        $query = $this->query()
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
                if ($key === 'nama'){
                    if(!empty($value)){
                        $query->where('nama', 'like', '%' . $value . '%');
                    }
                }
                if ($key === 'm_kategori_id'){
                    if($value !== null){
                        $query->where('m_kategori_id', $value);
                    }
                }
                if ($key === 'm_brand_id'){
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

        if (isset($params['lastseen']) && !empty($params['lastseen'])) {
            $query->whereIn('m_produk.id', $params['lastseen']);
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

    public function getAllPaginate($params = []){
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
                if ($key === 'nama'){
                    if(!empty($value)){
                        $query->where('nama', 'like', '%' . $value . '%');
                    }
                    // $query->orWhere('sku', 'like', '%' . $value . '%');
                }
                if ($key === 'm_kategori_id'){
                    if($value !== null){
                        $query->where('m_kategori_id', $value);
                    }
                }
                if ($key === 'm_brand_id'){
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

        if (isset($params['lastseen']) && !empty($params['lastseen'])) {
            $query->whereIn('m_produk.id', $params['lastseen']);
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

        $perPage = isset($params['per_page']) ? $params['per_page'] : 12;

        $data = $query->orderBy('m_produk.created_at', 'DESC')
            ->paginate($perPage);

        $totalItems = $query->count();

        return [
            'list' => $data,
            'totalItems' => $totalItems
        ];
    }

    public function getBySlug($slug){
        $query = DB::table('m_produk')
                ->selectRaw('m_produk.*, m_kategori.slug as slug_kategori, m_kategori.kategori, m_brand.brand, m_brand.slug as slug_brand')
                ->leftJoin('m_kategori', 'm_kategori.id', '=', 'm_produk.m_kategori_id')
                ->leftJoin('m_brand', 'm_brand.id', '=', 'm_produk.m_brand_id')
                ->where('m_produk.slug', $slug)
                ->first();

        $query->detail_foto = DB::table('m_produk_media')
            ->where('m_produk_id', $query->id)
            ->orderBy('is_video', 'ASC')
            ->orderBy('is_main', 'DESC')
            ->orderBy('urutan', 'ASC')
            ->get();

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

        $image = [];
        foreach ($params['photo'] as $key => $value) {
            $image[$key]['id'] = $value['woo_media_id'];
        }

        $dataWoo = [
            'name' => $params['nama'],
            'sku' => $params['sku'],
            'slug' => $params['slug'],
            // 'description' => $params['deskripsi'],
            // 'short_description' => $params['detail_produk'],
            // 'uagb_excerpt' => $params['in_box'],
            'stock_quantity' => $params['stok'],
            'price' => $params['harga'],
            'weight' => $params['berat'] / 1000,
            'dimensions' => [
                'length' => $params['lebar'],
                'width' => $params['panjang'],
                'height' => $params['tinggi'],
            ],
            // 'image' => $image
        ];

        $woo = $this->updateWooProduk($params['woo_produk_id'], $dataWoo);

        if (isset($params['photo']) && !empty(isset($params['photo']))) {
            $this->savePhoto($id, $params['photo']);
            unset($params['photo']);
        }

        if (isset($params['video']) && !empty(isset($params['video']))) {
            $this->saveVideo($id, $params['video']);
            unset($params['video']);
        }

        if (isset($params['variant']) && !empty(isset($params['variant']))) {
            $this->saveVariant($id, $params['variant']);
            unset($params['variant']);
        }

        $dataUpdate = [
            'nama' => $woo->name,
            'sku' => $woo->sku,
            'slug' => $woo->slug,
            'deskripsi' => $params['deskripsi'],
            'detail_produk' => $params['detail_produk'],
            'in_box' => $params['in_box'],
            'stok' => $woo->stock_quantity,
            'stok_status' => $woo->stock_status,
            'harga' => $woo->price,
            'berat' => $woo->weight * 1000,
            'harga' => (double)$woo->price ?? 0,
            'berat' => (double)$woo->weight * 1000 ?? 0,
            'lebar' => $woo->dimensions ? (int)$woo->dimensions->length : 0,
            'panjang' => $woo->dimensions ? (int)$woo->dimensions->width : 0,
            'tinggi' => $woo->dimensions ? (int)$woo->dimensions->height : 0,
        ];
        return DB::table('m_produk')->where('id', $id)->update($dataUpdate);
    }

    public function insertProduct($params) {
        $params['id'] = Generator::uuid4()->toString();
        $params['sku'] = isset($params['sku']) ? $params['sku'] : '';

        if($params['sku'] == ''){
            $params['sku'] = 'JC-'.date('ymdhms').$params['woo_produk_id'];
        }
        $params['slug'] = Str::slug($params['nama'], '-');
        $params['updated_at'] = date('Y-m-d H:i:s');

        if (isset($params['photo']) && !empty(isset($params['photo']))) {
            if($params['sinkron']){
                $this->savePhotoSinkron($params['id'], $params['photo'], $params['sinkron']);
            }
            else{
                $this->savePhoto($params['id'], $params['photo'], $params['sinkron']);
            }
            unset($params['photo']);
        }

        if (isset($params['video']) && !empty(isset($params['video']))) {
            $this->saveVideo($params['id'], $params['video']);
            unset($params['video']);
        }

        if (isset($params['variant']) && !empty(isset($params['variant']))) {
            $this->saveVariant($params['id'], $params['variant']);
            unset($params['variant']);
        }
        if($params['sinkron']){
            if(isset($params['tags'])){
                foreach ($params['tags'] as $key => $value) {
                    $tags = [
                        'produk_id' => $params['id'],
                        'woo_tags_id' => $value->id
                    ];

                    DB::table('m_produk_tags')->insert($tags);
                }
            }
            if(isset($params['categories'])){
                foreach ($params['categories'] as $key => $value) {
                    $categories = [
                        'produk_id' => $params['id'],
                        'woo_kategori_id' => $value->id
                    ];

                    DB::table('m_produk_kategori')->insert($categories);
                }
            }
        }

        unset($params['sinkron']);
        unset($params['tags']);
        unset($params['categories']);

        return DB::table('m_produk')->insert($params);
    }

    public function savePhotoSinkron($produkId, $photo, $sinkron = false) {
        $service = new Service();

        foreach($photo as $i => $image) {
            $data = [];
            $data['id'] = Generator::uuid4()->toString();
            $data['m_produk_id'] = $produkId;
            $data['woo_media_id'] = $image->id;
            $data['name'] = $image->name ?? '';
            $data['alt'] = $image->alt ?? '';
            $data['is_main'] = $i == 0 ? 1 : 0;
            $data['urutan'] = $i + 1;
            $data['media_link'] = $sinkron ? $image->src : $service->saveImage("produk/", $image['foto']);

            DB::table('m_produk_media')->insert($data);
        }
    }

    public function savePhoto($produkId, $photo, $sinkron = false) {
        $service = new Service();

        DB::table('m_produk_media')->where('m_produk_id', '=', $produkId)->delete();
        foreach($photo as $i => $image) {
            $data = [];
            if($image['isFoto']){
                $abbreviation = explode('.', trim($image['foto']))[0];
                $data['id'] = Generator::uuid4()->toString();
                $data['m_produk_id'] = $produkId;
                $data['name'] = $image['name'] ?? '';
                $data['alt'] = $image['alt'] ?? '';
                $data['is_main'] = $i == 0 ? 1 : 0;
                $data['urutan'] = $i + 1;
                $data['media_link'] = $abbreviation == 'https://jakartacamera' ? $image['foto'] : $service->saveImage("produk/", $image['foto']);

                DB::table('m_produk_media')->insert($data);
            }
        }
    }

    public function saveVideo($produkId, $video) {
        $service = new Service();

        DB::table('m_produk_media')->where('m_produk_id', '=', $produkId)->where('is_video', 'ya')->delete();
        $data['id'] = Generator::uuid4()->toString();
        $data['m_produk_id'] = $produkId;
        $data['is_video'] = 'ya';
        $data['is_main'] = 0;
        $data['urutan'] = 0;
        $data['media_link'] = $service->saveVideo("produk/", $video);

        DB::table('m_produk_media')->insert($data);
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

    public function getVideo($produkId) {
        $data = DB::table('m_produk_media')
        ->where('m_produk_id', $produkId)
        ->where('is_video', 'ya')
        ->first();

        if($data){
            $url = Storage::url('videos/produk/' . $data->media_link);
            return $url;
        }
        return null;
    }

    public function getCategories($produkId) {
        $data = DB::table('m_produk_kategori')
        ->selectRaw('
            m_produk_kategori.*,
            m_kategori.induk_id,
            m_kategori.kategori,
            m_kategori.slug,
            m_kategori.icon
        ')
        ->leftJoin('m_kategori', 'm_kategori.woo_kategori_id', '=', 'm_produk_kategori.woo_kategori_id')
        ->where('produk_id', $produkId)
        ->get();

        return $data;
    }

    public function getTags($produkId) {
        $data = DB::table('m_produk_tags')
        ->selectRaw('
            m_produk_tags.*,
            m_tags.name,
            m_tags.slug,
            m_tags.count
        ')
        ->leftJoin('m_tags', 'm_tags.woo_tags_id', '=', 'm_produk_tags.woo_tags_id')
        ->where('produk_id', $produkId)
        ->get();

        return $data;
    }

    public function media(): HasMany
    {
        $media = $this->hasMany(Media::class, 'm_produk_id', 'id');

        return $media;
    }

    public function getPhoto($produkId) {
        $photo = DB::table('m_produk_media')
        ->where('m_produk_id', $produkId)
        ->where('is_video', 'tidak')
        ->orderBy('urutan', 'ASC')
        ->get();

        if (!empty($photo)) {
            foreach($photo as $i => $image) {
                if($image->media_link !== ''){
                    $photo[$i]->foto = $image->media_link;
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

    public function decreaseStok($params, $is_varian = false) {
        if(isset($params['product_varian_id'])){
            $stokSekarang = $params['sisa_stok'] - $params['quantity'];
            $pVarian = [
                'id' => $params['product_varian_id'],
                'stok' => $stokSekarang
            ];
            $this->updateStok($pVarian);
        }
        else{
            $stokSekarang = $params['sisa_stok'] - $params['quantity'];
            $pProduct = [
                'id' => $params['product_id'],
                'stok' => $stokSekarang
            ];
            $this->updateStokProduk($pProduct);
        }
    }

    public function updateStok($params) {
        DB::table('m_produk_varian')->where('id', $params['id'])->update(['stok' => $params['stok']]);

        return true;
    }

    public function updateStokProduk($params) {
        $dataWoo = [
            'stock_quantity' => $params['stok']
        ];
        $woo = $this->updateWooProduk($params['woo_produk_id'], $dataWoo);

        $dataUpdate = [
            'stok' => $woo->stock_quantity,
            'stok_status' => $woo->stock_status
        ];
        DB::table('m_produk')->where('id', $params['id'])->update($dataUpdate);
        return true;
    }

    public function ubahStatus($params) {
        DB::table('m_produk')->where('id', $params['id'])->update(['is_active' => $params['is_active']]);

        return true;
    }

    public function getMainPhotoProduk($id) {
        $data = DB::table('m_produk_media')->where('m_produk_id', $id)->where('is_main', 1)->first();
        $url = $data->media_link ?? '';

        return $url;
    }

    public function updateWooProduk($id, $payload = []){
        $params = http_build_query($payload);
        $url = $this->WOOCOMMERCE_STORE_URL.'/wp-json/wc/v3/products/'.$id.'?'.$params;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Basic '. base64_encode($this->WOOCOMMERCE_CONSUMER_KEY . ":" . $this->WOOCOMMERCE_CONSUMER_SECRET)
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response);
        // echo $response;
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

    public function getProdukKategori($params){
        $query = DB::table('m_produk_kategori')
                ->selectRaw('
                    m_produk.id,
                    m_produk.sku,
                    m_produk.nama,
                    m_produk.type,
                    m_produk.stok,
                    m_produk.min_beli,
                    m_produk.harga,
                    m_produk.m_kategori_id,
                    m_produk.m_brand_id,
                    m_kategori.slug as slug_kategori,
                    m_kategori.kategori as kategori_produk,
                    m_kategori.woo_kategori_id as kategori_produk
                ')
                ->leftJoin('m_produk', 'm_produk.id', '=', 'm_produk_kategori.produk_id')
                ->leftJoin('m_kategori', 'm_kategori.woo_kategori_id', '=', 'm_produk_kategori.woo_kategori_id')
                // ->leftJoin('m_brand', 'm_brand.id', '=', 'm_produk.m_brand_id')
                ->where('is_active', 1)
                ->whereIn('m_kategori.id', $params)
                ->get();

        return $query;
    }

    public function getProdukBrand($params){
        $query = DB::table($this->table)
                ->selectRaw('
                    m_produk.id,
                    m_produk.sku,
                    m_produk.nama,
                    m_produk.type,
                    m_produk.stok,
                    m_produk.min_beli,
                    m_produk.harga,
                    m_produk.m_kategori_id,
                    m_produk.m_brand_id,
                    m_kategori.slug as slug_kategori,
                    m_kategori.kategori as kategori_produk,
                    m_brand.brand as brand_produk,
                    m_brand.slug as slug_brand
                ')
                ->leftJoin('m_kategori', 'm_kategori.id', '=', 'm_produk.m_kategori_id')
                ->leftJoin('m_brand', 'm_brand.id', '=', 'm_produk.m_brand_id')
                ->where('is_active', 1)
                ->whereIn('m_brand_id', $params)
                ->get();

        return $query;
    }

    function reminderStok() {
        $products = DB::table('m_produk')
        ->selectRaw('
            m_produk.id,
            m_produk.nama,
            m_produk.stok,
            m_produk.min_beli,
            m_produk.harga,
            m_kategori.kategori,
            NULL as varian_id,
            m_produk.sku,
            NULL as varian1,
            NULL as varian1_type,
            NULL as varian2,
            NULL as varian2_type,
            NULL as stok_varian,
            NULL as harga_varian,
            CASE WHEN EXISTS (
                SELECT 1 FROM m_produk_varian
                WHERE m_produk_varian.m_produk_id = m_produk.id
            ) THEN true ELSE false END as is_variant
        ')
        ->leftJoin('m_kategori', 'm_kategori.id', '=', 'm_produk.m_kategori_id')
        ->where('m_produk.is_active', 1)
        ->where('m_produk.stok', '<=', DB::raw('m_produk.min_beli'))
        ->unionAll(
            DB::table('m_produk_varian')
            ->selectRaw('
                m_produk.id,
                m_produk.nama,
                m_produk.stok,
                m_produk.min_beli,
                m_produk.harga,
                m_kategori.kategori,
                m_produk_varian.id as varian_id,
                m_produk_varian.sku,
                m_produk_varian.varian1,
                m_produk_varian.varian1_type,
                m_produk_varian.varian2,
                m_produk_varian.varian2_type,
                m_produk_varian.stok as stok_varian,
                m_produk_varian.harga as harga_varian,
                true as is_variant
            ')
            ->leftJoin('m_produk', 'm_produk.id', '=', 'm_produk_varian.m_produk_id')
            ->leftJoin('m_kategori', 'm_kategori.id', '=', 'm_produk.m_kategori_id')
            ->where('m_produk.is_active', 1)
            ->where('m_produk_varian.stok', '<=', DB::raw('m_produk.min_beli'))
        )
        ->orderByRaw('COALESCE(stok_varian, stok) ASC')
        ->get();

        return $products;
    }
}

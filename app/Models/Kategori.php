<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid as Generator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Service;

class Kategori extends Model
{
    use HasFactory;

    private $WOOCOMMERCE_STORE_URL;
    private $WOOCOMMERCE_CONSUMER_KEY;
    private $WOOCOMMERCE_CONSUMER_SECRET;
    protected $table = 'm_kategori';

    public function __construct() {
        $this->WOOCOMMERCE_STORE_URL = 'https://jakartacamera.com';
        $this->WOOCOMMERCE_CONSUMER_KEY = 'ck_c46c613c61a7afda407d20f59593c4cc1523d22e';
        $this->WOOCOMMERCE_CONSUMER_SECRET = 'cs_6aa1f62f6dd3539d47ac1cb528d954f276b60352';
    }

    protected $fillable = [
        'id',
        'woo_kategori_id',
        'induk_id',
        'kategori',
        'slug',
        'icon',
        'keterangan'
    ];

    protected $casts = [
        'id' => 'string'
    ];

    public function childs() {
        return $this->hasMany('App\Models\Kategori','induk_id','woo_kategori_id') ;
    }

    public function getAll($params){
        $query = DB::table($this->table);
        $totalItems = $query->count();

        if (isset($params['filter']) && !empty($params['filter'])) {
            $filter = json_decode($params['filter']);
            foreach ($filter as $key => $value) {
                if($key === 'kategori'){
                    $query->where('kategori', 'like', '%' . $value . '%');
                }
            }
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

        $data = $query->orderBy('created_at', 'DESC')->get();

        foreach ($data as $key => $value) {
            $iconPath = 'images/kategori/' . $value->icon;
            $data[$key]->icon = $value->icon->endsWith('.webp') ? Storage::url($iconPath) : null;
            $data[$key]->children = DB::table($this->table)->where('induk_id', $value->woo_kategori_id)->get();
        }

        return [
            'list' => $data,
            'totalItems' => $totalItems
        ];
    }

    public function getById($id){
        $data = DB::table('m_kategori')
            ->select('id', 'induk_id', 'woo_kategori_id', 'kategori', 'slug', 'icon', 'keterangan')
            ->where('id', $id)
            ->first();

        return $data;
    }

    public function simpan($params) {
        if (isset($params['id']) && !empty($params['id'])) {
            return $this->updateCategory($params);
        } else {
            return $this->insertCategory($params);
        }
    }

    public function getDetail($id) {
        return DB::table('m_kategori')->where('induk_id', $id)->get();
    }

    public function updateCategory($params) {
        $service = new Service();

        $id = $params['id']; unset($params['id']);
        $params['slug'] = Str::slug($params['kategori'], '-');
        $params['updated_at'] = date('Y-m-d H:i:s');
        $params['icon'] = $service->saveImage("kategori/", $params['icon']);

        return DB::table('m_kategori')->where('id', $id)->update($params);
    }

    public function insertCategory($params) {
        $service = new Service();
        $logUser = new LogUser();

        $params['id'] = Generator::uuid4()->toString();
        $params['slug'] = Str::slug($params['kategori'], '-');
        $params['created_at'] = date('Y-m-d H:i:s');
        if(!isset($params['sinkron'])){
            $params['icon'] = $service->saveImage("kategori/", $params['icon']);

            $dataWoo = [
                'name' => $params['kategori'],
                'slug' => $params['slug'],
                'description' => $params['keterangan'],
                // 'image' => Storage::url('images/kategori/' . $params['icon'])
            ];
            $woo = $this->saveWooKategori($dataWoo);
            $dataUpdate = [
                'induk_id' => $woo->parent,
                'woo_kategori_id' => $woo->id
            ];

            DB::table('m_kategori')->insert($params);
            return DB::table('m_kategori')->where('id', $params['id'])->update($dataUpdate);
        }
        else{
            unset($params['sinkron']);
            return DB::table('m_kategori')->insert($params);
        }

    }

    public function getKategori(){
        $data = DB::table('m_kategori')->whereNull('induk_id')->orderBy('created_at', 'DESC')->get();

        foreach ($data as $key => $value) {
            $data[$key]->icon = Storage::url('images/kategori/' . $value->icon);
            $data[$key]->child = DB::table('m_kategori')->where('induk_id', $value->id)->get();
        }

        return $data;
    }

    public function saveWooKategori($payload = []){
        $url = $this->WOOCOMMERCE_STORE_URL.'/wp-json/wc/v3/products/categories';
        $curl = curl_init();
        // $image = file_get_contents('https://admin.jakartacamera.com/assets/img/logo.png');

        // $payload['image'] = $image;
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: multipart/form-data',
                'Authorization: Basic '. base64_encode($this->WOOCOMMERCE_CONSUMER_KEY . ":" . $this->WOOCOMMERCE_CONSUMER_SECRET)
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response);
    }
}

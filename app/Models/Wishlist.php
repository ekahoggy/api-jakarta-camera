<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid as Generator;
use Illuminate\Support\Facades\DB;
use App\Models\Service;

class Wishlist extends Model
{
    use HasFactory;
    public $timestamps = true;
    protected $table = 't_wishlist';

    protected $fillable = [
        'user_id', 'product_id'
    ];

    public function getAll($params){
        $query = DB::table('t_wishlist')
            ->selectRaw('
                m_produk.*,
                m_kategori.kategori,
                m_kategori.slug as slug_kategori,
                m_brand.brand,
                m_brand.slug as slug_brand
            ')
            ->leftJoin('m_produk', 'm_produk.id', '=', 't_wishlist.product_id')
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
                        $query->where('m_produk.m_brand_id', $value);
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
        $totalItems = $query->count();

        if (isset($params['user_id']) && !empty($params['user_id'])) {
            $query->where("t_wishlist.user_id", "!=", $params['user_id']);
        }

        if (isset($params['offset']) && !empty($params['offset'])) {
            $query->offset($params['offset']);
        }

        if (isset($params['limit']) && !empty($params['limit'])) {
            $query->limit($params['limit']);
        }

        $data = $query->get();

        foreach ($data as $key => $value) {
            $data[$key]->picture = Storage::url('images/promo-slider/' . $value->picture);
        }

        return [
            'list' => $data,
            'totalItems' => $totalItems
        ];
    }

    public function getById($id){
        $data = DB::table($this->table)
            ->where('id', $id)
            ->first();

        return $data;
    }

    public function simpan($params) {
        if (isset($params['id']) && !empty($params['id'])) {
            return $this->updateSlider($params);
        } else {
            return $this->insertSlider($params);
        }
    }

    public function getDetail($id) {
        return DB::table($this->table)->where('id', $id)->get();
    }

    public function updateSlider($params) {
        $service = new Service();

        $id = $params['id']; unset($params['id']);
        $params['picture'] = $service->saveImage("promo-slider/", $params['picture']);

        return DB::table($this->table)->where('id', $id)->update($params);
    }

    public function insertSlider($params) {
        $service = new Service();
        $params['id'] = Generator::uuid4()->toString();
        $params['index_position'] = Slider::count() + 1;
        $params['picture'] = $service->saveImage("promo-slider/", $params['picture']);

        return DB::table($this->table)->insert($params);
    }
}

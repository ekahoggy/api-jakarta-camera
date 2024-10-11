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

    protected $table = 'm_kategori';

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
            // $data[$key]->icon = Storage::url('images/kategori/' . $value->icon);
            $data[$key]->child = DB::table('m_kategori')->where('induk_id', $value->woo_kategori_id)->get();
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
        }
        else{
            unset($params['sinkron']);
        }

        return DB::table('m_kategori')->insert($params);
    }

    public function getKategori(){
        $data = DB::table('m_kategori')->where('induk_id', '=', 0)->orderBy('created_at', 'DESC')->get();

        foreach ($data as $key => $value) {
            $data[$key]->icon = Storage::url('images/kategori/' . $value->icon);
            $data[$key]->child = DB::table('m_kategori')->where('induk_id', $value->woo_kategori_id)->get();
        }

        return $data;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid as Generator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Service;

class Edukasi extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'm_edukasi';

    protected $fillable = [
        'kategori_id',
        'judul',
        'slug',
        'gambar',
        'tanggal',
        'deskripsi',
        'peralatan',
        'harga',
        'tingkatan',
        'is_publish',
        'is_deleted',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];

    protected $casts = [
        'id' => 'string'
    ];

    public function getAll($params){
        $query = DB::table($this->table)
                ->selectRaw('m_edukasi.*, m_edukasi_kategori.kategori')
                ->leftJoin('m_edukasi_kategori', 'm_edukasi.kategori_id', '=', 'm_edukasi_kategori.id');


        if (isset($params['filter']) && !empty($params['filter'])) {
            $filter = json_decode($params['filter']);
            foreach ($filter as $key => $value) {
                if($key === 'judul'){
                    if(!empty($value)){
                        $query->where('judul', 'like', '%' . $value . '%');
                    }
                }
                if($key === 'kategori_id'){
                    if($value !== null){
                        $query->where('kategori_id', $value);
                    }
                }
                if($key === 'is_deleted'){
                    if($value !== null){
                        $query->where('m_edukasi.is_deleted', $value);
                    }
                }
                if($key === 'is_publish'){
                    if($value !== null){
                        $query->where('m_edukasi.is_publish', $value);
                    }
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

        $data = $query->orderBy('is_publish', 'ASC')->get();

        foreach ($data as $key => $value) {
            $data[$key]->gambar = Storage::url('images/edukasi/' . $value->gambar);
        }

        $totalItems = $query->count();
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

    public function getBySlug($slug){
        $data = DB::table($this->table)
            ->where('slug', $slug)
            ->first();

        return $data;
    }

    public function simpan($params) {
        if (isset($params['data']['id']) && !empty($params['data']['id'])) {
            return $this->updateEdukasi($params);
        } else {
            return $this->insertEdukasi($params);
        }
    }

    public function getDetail($id) {
        return DB::table('m_edukasi_video')->where('edukasi_id', $id)->orderBy('urutan', 'ASC')->get();
    }

    public function updateEdukasi($params) {
        $service = new Service();
        $modelDetail = new EdukasiVideo();

        $id = $params['data']['id']; unset($params['data']['id']);
        $params['data']['slug'] = Str::slug($params['data']['judul'], '-');
        $params['data']['gambar'] = $service->saveImage("edukasi/", $params['data']['gambar']);
        $params['data']['updated_at'] = strtotime(date('Y-m-d H:i:s'));

        $data = DB::table($this->table)->where('id', $id)->update($params['data']);

        foreach ($params['detail'] as $key => $value) {
            $value['urutan'] = $key + 1;
            $modelDetail->simpan($value, $id);
        }
        return $data;
    }

    public function insertEdukasi($params) {
        $service = new Service();
        $modelDetail = new EdukasiVideo();

        $id = Generator::uuid4()->toString();
        $params['data']['id'] = $id;
        $params['data']['slug'] = Str::slug($params['data']['judul'], '-');
        $params['data']['gambar'] = $service->saveImage("edukasi/", $params['data']['gambar']);
        $params['data']['tanggal'] = date('Y-m-d H:i:s');
        $params['data']['created_at'] = strtotime(date('Y-m-d H:i:s'));

        $data = DB::table($this->table)->insert($params['data']);
        foreach ($params['detail'] as $key => $value) {
            $value['urutan'] = $key + 1;
            $modelDetail->simpan($value, $id);
        }
        return $data;
    }

    public function changeStatus($params) {
        $id = $params['id'];
        return DB::table($this->table)->where('id', $id)->update($params);
    }

    public function getEdukasi(){
        $data = DB::table($this->table)->orderBy('is_publish', 'ASC')->get();

        foreach ($data as $key => $value) {
            $data[$key]->gambar = Storage::url('images/edukasi/' . $value->gambar);
        }

        return $data;
    }
}

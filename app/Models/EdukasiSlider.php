<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid as Generator;
use Illuminate\Support\Facades\DB;
use App\Models\Service;

class EdukasiSlider extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'm_edukasi_slider';

    protected $fillable = [
        'gambar',
        'gambar_mobile',
        'judul',
        'link',
        'urutan',
        'is_status'
    ];

    protected $casts = [
        'id' => 'string'
    ];

    public function getAll($params){
        $query = DB::table($this->table);
        $totalItems = $query->count();

        if (isset($params['notEqual']) && !empty($params['notEqual'])) {
            $query->where("id", "!=", $params['notEqual']);
        }

        if (isset($params['offset']) && !empty($params['offset'])) {
            $query->offset($params['offset']);
        }

        if (isset($params['limit']) && !empty($params['limit'])) {
            $query->limit($params['limit']);
        }

        $query->where('is_status', 1);
        $data = $query->orderBy('urutan', 'ASC')->get();

        foreach ($data as $key => $value) {
            $data[$key]->gambar = Storage::url('images/edukasi_slider/' . $value->gambar);
            $data[$key]->gambar_mobile = Storage::url('images/edukasi_slider/' . $value->gambar_mobile);
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
        $params['gambar'] = $service->saveImage("edukasi_slider/", $params['gambar']);
        $params['gambar_mobile'] = $service->saveImage("edukasi_slider/", $params['gambar_mobile']);

        return DB::table($this->table)->where('id', $id)->update($params);
    }

    public function insertSlider($params) {
        $service = new Service();
        $params['id'] = Generator::uuid4()->toString();
        $params['urutan'] = Slider::count() + 1;
        $params['gambar'] = $service->saveImage("edukasi_slider/", $params['gambar']);
        $params['gambar_mobile'] = $service->saveImage("edukasi_slider/", $params['gambar_mobile']);

        return DB::table($this->table)->insert($params);
    }

    public function changeStatus($params) {
        $id = $params['id'];
        $params['is_status'] = 0;
        return DB::table($this->table)->where('id', $id)->update($params);
    }

    public function getSlider(){
        $data = DB::table($this->table)->orderBy('urutan', 'ASC')->get();

        foreach ($data as $key => $value) {
            $data[$key]->gambar = Storage::url('images/edukasi_slider/' . $value->gambar);
            $data[$key]->gambar_mobile = Storage::url('images/edukasi_slider/' . $value->gambar_mobile);
        }

        return $data;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid as Generator;
use Illuminate\Support\Facades\DB;
use App\Models\Service;

class JasaServis extends Model
{
    use HasFactory;
    public $timestamps = true;
    protected $table = 'm_jasa_servis';

    protected $fillable = [
        'name', 'email', 'phone_code', 'phone_number', 'keterangan', 'file'
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

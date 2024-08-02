<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid as Generator;
use Illuminate\Support\Facades\DB;
use App\Models\Service;

class JasaPasangCctv extends Model
{
    use HasFactory;
    public $timestamps = true;
    protected $table = 'm_jasa_cctv';

    protected $fillable = [
        'name', 'email', 'phone_code', 'phone_number', 'pesan',
        'province_id', 'subdistrict_id', 'city_id', 'village_id', 'postal_code', 'address', 'latitude', 'longitude'
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

        $data = $query->orderBy('index_position', 'ASC')->get();

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

    public function getDetail($id) {
        return DB::table($this->table)->where('id', $id)->get();
    }
}

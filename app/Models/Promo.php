<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid as Generator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Service;

class Promo extends Model
{
    use HasFactory;

    protected $table = 'm_promo';

    protected $fillable = [
        'id',
        'kode',
        'type',
        'jenis',
        'promo',
        'tanggal_mulai',
        'tanggal_selesai',
        'jam_mulai',
        'jam_selesai',
        'promo_min_beli',
        'is_flashsale',
        'is_status'
    ];

    protected $casts = [
        'id' => 'string'
    ];

    public function getAll($params){
        $query = DB::table($this->table);

        if (isset($params['filter']) && !empty($params['filter'])) {
            $filter = json_decode($params['filter']);
            foreach ($filter as $key => $value) {
                if($key === 'kode'){
                    $query->where('promo', 'like', '%' . $value . '%');
                    $query->orWhere('kode', 'like', '%' . $value . '%');
                }
                if($key === 'is_status'){
                    if($value !== null){
                        $query->where('is_status', $value);
                    }
                }
            }
        }

        if (isset($params['status']) && !empty($params['status'])) {
            $query->where("is_status", "=", $params['status']);
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

        return [
            'list' => $data,
            'totalItems' => $query->count()
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
            return $this->updatePromo($params);
        } else {
            return $this->insertPromo($params);
        }
    }

    public function updatePromo($params) {
        $service = new Service();

        $id = $params['id']; unset($params['id']);
        $params['updated_at'] = date('Y-m-d H:i:s');

        return DB::table($this->table)->where('id', $id)->update($params);
    }

    public function insertPromo($params) {
        $service = new Service();

        $params['id'] = Generator::uuid4()->toString();
        $params['created_at'] = date('Y-m-d H:i:s');

        DB::table($this->table)->insert($params);
        return $params;
    }

    public function getPromoChanged(){
        $date = date('Y-m-d');
        $time = date('H:i:s');

        $query = DB::table($this->table)
        ->select(
            'm_promo.id',
            'm_promo.kode',
            'm_promo.type',
            'm_promo.jenis',
            'm_promo.promo',
            'm_promo.tanggal_mulai',
            'm_promo.jam_mulai',
            'm_promo.tanggal_selesai',
            'm_promo.jam_selesai',
            'm_promo.promo_min_beli',
            'm_promo.is_status'
        )
        ->where('m_promo.is_status', 1)
        ->where(function ($query) use ($date, $time) {
            $query->where(function ($query) use ($date, $time) {
                $query->whereDate('m_promo.tanggal_selesai', '<', $date)
                    ->orWhere(function ($query) use ($date, $time) {
                        $query->whereDate('m_promo.tanggal_selesai', '=', $date)
                            ->whereTime('m_promo.jam_selesai', '<', $time);
                    });
            });
        });

        return $query->get();
    }
}

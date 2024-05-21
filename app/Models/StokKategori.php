<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid as Generator;
use Illuminate\Support\Facades\DB;

class StokKategori extends Model
{
    use HasFactory;

    protected $table = 't_stok_kategori';

    protected $fillable = [
        'id',
        'nama',
        'type',
        'deskripsi',
        'status'
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

        $query->where("status", "=", 1);
        $data = $query->orderBy('created_at', 'DESC')->get();

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

    public function getDataByType($type){
        $data = DB::table($this->table)
            ->where('type', $type)
            ->get();

        return $data;
    }

    public function simpan($params) {
        if (isset($params['id']) && !empty($params['id'])) {
            return $this->updateStokKategori($params);
        } else {
            return $this->insertStokKategori($params);
        }
    }

    public function updateStokKategori($params) {
        $service = new Service();

        $id = $params['id']; unset($params['id']);
        $params['updated_at'] = date('Y-m-d H:i:s');

        return DB::table($this->table)->where('id', $id)->update($params);
    }

    public function updateStatus($params) {
        $id = $params['id']; unset($params['id']);
        $params['updated_at'] = date('Y-m-d H:i:s');

        return DB::table($this->table)->where('id', $id)->update($params);
    }

    public function changeStatus($params) {
        $id = $params['id'];

        $params['updated_at'] = date('Y-m-d H:i:s');
        $params['status'] = 0;

        return DB::table($this->table)->where('id', $id)->update($params);
    }

    public function insertStokKategori($params) {
        $service = new Service();

        $params['id'] = Generator::uuid4()->toString();
        $params['created_at'] = date('Y-m-d H:i:s');

        return DB::table($this->table)->insert($params);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid as Generator;
use Illuminate\Support\Facades\DB;

class StokDet extends Model
{
    use HasFactory;

    protected $table = 't_stok_det';

    protected $fillable = [
        'id',
        't_stok_id',
        'm_produk_id',
        'qty'
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

        $data = $query->orderBy('created_at', 'DESC')->get();

        return [
            'list' => $data,
            'totalItems' => $totalItems
        ];
    }

    public function getById($id){
        $data = DB::table($this->table)
            ->where('t_stok_id', $id)
            ->get();

        return $data;
    }

    public function getAvailable($params){
        $data = DB::table($this->table)
            ->select('t_stok.*', 't_stok_det.m_produk_id', 't_stok_det.qty')
            ->leftJoin('t_stok', 't_stok.id', '=' , 't_stok_det.t_stok_id')
            // ->where('t_stok.type', $params['type'])
            ->where('t_stok.status', 'a')
            ->where('t_stok_det.m_produk_id', $params['m_produk_id'])
            ->get();

        $stok = 0;
        foreach ($data as $key => $value) {
            if($value->type === 'i'){
                $stok += $value->qty;
            }
            else{
                $stok -= $value->qty;
            }
        }

        return $stok;
    }

    public function simpan($params) {
        if (isset($params['id']) && !empty($params['id'])) {
            return $this->updateStokDet($params);
        } else {
            return $this->insertStokDet($params);
        }
    }

    public function updateStokDet($params) {
        $id = $params['id']; unset($params['id']);
        return DB::table($this->table)->where('id', $id)->update($params);
    }

    public function insertStokDet($params) {
        $params['id'] = Generator::uuid4()->toString();
        return DB::table($this->table)->insert($params);
    }
}

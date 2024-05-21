<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid as Generator;
use Illuminate\Support\Facades\DB;

class Stok extends Model
{
    use HasFactory;

    protected $table = 't_stok';

    protected $fillable = [
        'id',
        't_stok_kategori_id',
        'kode',
        'type',
        'tanggal',
        'catatan',
        'status'
    ];

    protected $casts = [
        'id' => 'string'
    ];

    public function getAll($params){
        $query = DB::table($this->table)
        ->select(
            't_stok.*',
            't_stok_kategori.nama',
            't_stok_kategori.type',
            'users.name as pembuat'
        )
        ->leftJoin('t_stok_kategori','t_stok_kategori.id', '=', 't_stok.t_stok_kategori_id')
        ->leftJoin('users','users.id', '=', 't_stok.created_by');

        if (isset($params['notEqual']) && !empty($params['notEqual'])) {
            $query->where("t_stok.id", "!=", $params['notEqual']);
        }

        if (isset($params['type']) && !empty($params['type'])) {
            $query->where("t_stok.type", "=", $params['type']);
        }

        if (isset($params['offset']) && !empty($params['offset'])) {
            $query->offset($params['offset']);
        }

        if (isset($params['limit']) && !empty($params['limit'])) {
            $query->limit($params['limit']);
        }

        $data = $query->orderBy('t_stok.created_at', 'DESC')->get();
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

    public function simpan($params) {
        if (isset($params['id']) && !empty($params['id'])) {
            return $this->updateStok($params);
        } else {
            return $this->insertStok($params);
        }
    }

    public function updateStok($params) {
        $id = $params['id'];
        unset($params['id']);
        $params['updated_at'] = date('Y-m-d H:i:s');
        $params['updated_by'] = Auth::user()->id;

        DB::table($this->table)->where('id', $id)->update($params);

        $data = $params;
        $data['id'] = $id;
        return $data;
    }

    public function insertStok($params) {
        $params['id'] = Generator::uuid4()->toString();
        $params['kode'] = $this->generateCode($params['type']);
        $params['created_at'] = date('Y-m-d H:i:s');
        $params['created_by'] = Auth::user()->id;
        DB::table($this->table)->insert($params);
        return $params;
    }

    public function generateCode($type) {
        $data = DB::table($this->table)
                ->select('kode')
                ->where('type', $type)
                ->orderBy('kode', 'DESC')
                ->orderBy('created_at', 'DESC')
                ->first();

        if (isset($data->kode)) {
            $lastCode = $data->kode;
        } else {
            $lastCode = 0;
        }

        if($type == 'i'){
            $types = 'SMJC-';
        }elseif($type == 'o'){
            $types = 'SKJC-';
        }else{
            $types = 'SOJC-';
        }

        $codeItem = (substr($lastCode, -6) + 1);
        $code = substr('000000' . $codeItem, -5);
        $date = date('y').date('m');

        return $types . $date . $code;
    }
}

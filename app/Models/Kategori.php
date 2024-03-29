<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'm_kategori';

    protected $fillable = [
        'id',
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
        return $this->hasMany('App\Models\Kategori','induk_id','id') ;
    }

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

        foreach ($data as $key => $value) {
            $data[$key]->child = DB::table($this->table)->where('induk_id', $value->id)->get();
        }

        return [
            'list' => $data,
            'totalItems' => $totalItems
        ];
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
        $id = $params['id']; unset($params['id']);
        $params['slug'] = Str::slug($params['kategori'], '-');
        $params['updated_at'] = date('Y-m-d H:i:s');

        return DB::table('m_kategori')->where('id', $id)->update($params);
    }

    public function insertCategory($params) {
        $params['slug'] = Str::slug($params['kategori'], '-');
        $params['updated_at'] = date('Y-m-d H:i:s');

        return DB::table('m_kategori')->insert($params);
    }
}

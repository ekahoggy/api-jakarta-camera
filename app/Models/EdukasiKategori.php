<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid as Generator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Service;

class EdukasiKategori extends Model
{
    use HasFactory;

    protected $table = 'm_edukasi_kategori';

    protected $fillable = [
        'id',
        'kategori',
        'slug',
        'is_deleted',
    ];

    protected $casts = [
        'id' => 'string'
    ];

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

        $data = $query->get();

        return [
            'list' => $data,
            'totalItems' => $totalItems
        ];
    }

    public function getById($id){
        $data = DB::table($this->table)
            ->select('id', 'kategori')
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
        return DB::table($this->table)->get();
    }

    public function updateCategory($params) {
        $id = $params['id']; unset($params['id']);
        $params['slug'] = Str::slug($params['kategori'], '-');

        return DB::table($this->table)->where('id', $id)->update($params);
    }

    public function insertCategory($params) {
        $params['id'] = Generator::uuid4()->toString();
        $params['slug'] = Str::slug($params['kategori'], '-');

        return DB::table($this->table)->insert($params);
    }

    public function getKategori(){
        $data = DB::table($this->table)->get();

        return $data;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class NewsKategori extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'm_news_kategori';

    protected $fillable = [
        'kategori',
        'is_active'
    ];

    protected $casts = [
        'id' => 'integer'
    ];

    public function getAll($params){
        $query = DB::table($this->table);
        $data = $query->orderBy('kategori', 'ASC')->get();

        $totalItems = $query->count();
        return [
            'list' => $data,
            'totalItems' => $totalItems
        ];
    }

    public function getNewsKategori(){
        $data = DB::table($this->table)->orderBy('kategori', 'ASC')->get();

        return $data;
    }

    public function getNewsKategoriByName($kategori){
        $data = DB::table($this->table)
        ->where('kategori', $kategori)->first();

        return $data;
    }


    public function getNewsKategoriByNameMulti($data){
        $data = DB::table($this->table)
        ->whereIn('kategori', $data)
        ->orderBy('kategori', 'ASC')->get();

        return $data;
    }

    public function simpan($data){
        $params = [
            'kategori' => $data
        ];
        DB::table($this->table)->insert($params);
        $kategori = $this->getNewsKategoriByName($data);
        return $kategori->id;
    }
}

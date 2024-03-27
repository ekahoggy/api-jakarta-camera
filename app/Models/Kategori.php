<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    public function getKategori($req){
        $data = DB::table($this->table)->whereNull('induk_id')->orderBy('created_at', 'DESC')->get();

        foreach ($data as $key => $value) {
            $data[$key]->child = DB::table($this->table)->where('induk_id', $value->id)->get();
        }

        return $data;
    }
}

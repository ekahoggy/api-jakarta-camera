<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Brand extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'm_brand';

    protected $fillable = [
        'brand',
        'slug',
        'keterangan'
    ];

    protected $casts = [
        'id' => 'string'
    ];

    public function getAll($params){
        $query = DB::table($this->table);
        $totalItems = $query->count();

        $data = $query->orderBy('brand', 'ASC')->get();

        return [
            'list' => $data,
            'totalItems' => $totalItems
        ];
    }

    public function getBrand(){
        $data = DB::table($this->table)->orderBy('brand', 'ASC')->get();

        return $data;
    }
}

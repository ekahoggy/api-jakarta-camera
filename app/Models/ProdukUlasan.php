<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProdukUlasan extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'm_produk_ulasan';

    protected $fillable = [
        't_order_id', 'm_produk_id', 'rating', 'ulasan', 'is_publish'
    ];

    protected $casts = [
        'id' => 'integer'
    ];

    public function getAll($params){
        $query = DB::table($this->table)
            ->orderBy('created_at', 'ASC');

        $data = $query->get();
        $totalItems = $query->count();

        return [
            'list' => $data,
            'totalItems' => $totalItems
        ];
    }

    public function getProdukUlasan(){
        $query = DB::table($this->table)
            ->select('m_produk_ulasan.*','m_news.judul')
            ->leftJoin('m_news','m_news.id', '=', 'm_produk_ulasan.news_id');

        $data = $query->orderBy('is_publish', 'ASC')
            ->orderBy('tanggal', 'DESC')
            ->get();

        $totalItems = $query->count();
        return [
            'list' => $data,
            'totalItems' => $totalItems
        ];
    }

    public function getProdukUlasanByName($kategori){
        return DB::table($this->table)->where('kategori', $kategori)->first();
    }

    public function getProdukUlasanByNameMulti($data){
        return  DB::table($this->table)
            ->whereIn('kategori', $data)
            ->orderBy('kategori', 'ASC')->get();
    }

    public function getByNewsId($id){
        $query = DB::table($this->table)
            ->where('news_id', '=', $id)
            ->where('is_publish', 1)
            ->orderBy('created_at', 'ASC');

        $data = $query->get();
        $totalItems = $query->count();

        return [
            'list' => $data,
            'totalItems' => $totalItems
        ];
    }

    public function postBalasan($data) {
        $payload = [
            'balasan' => $data['balasan'],
            'tanggal_balasan' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        return DB::table($this->table)->update($payload, ['id' => $data['id']]);
    }

    public function changeStatus($data) {
        $payload = [
            'is_publish' => $data['is_publish'],
            'updated_at' => date('Y-m-d H:i:s')
        ];

        return DB::table($this->table)->update($payload, ['id' => $data['id']]);
    }

    public function postUlasan($data) {

        $payload = [
            'm_produk_id' => $data['m_produk_id'],
            't_order_id' => $data['t_order_id'],
            'rating' => $data['rating'],
            'ulasan' => $data['ulasan'],
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => $data['user_id'],
        ];

        return DB::table($this->table)->insert($payload);
    }
}

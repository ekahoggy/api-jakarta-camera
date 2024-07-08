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

    public function getUlasanByProdukId($id){
        $query = DB::table($this->table)
            ->select(
                'm_produk_ulasan.rating', 'm_produk_ulasan.ulasan', 'm_produk_ulasan.created_at',
                'users.username', 'users.name' 
            )
            ->leftJoin('users', 'users.id', '=', 'm_produk_ulasan.created_by')
            ->where('m_produk_id', '=', $id)
            ->where('is_publish', 1)
            ->orderBy('created_at', 'ASC');

        $data = $query->get();
        $totalItems = $query->count();
        $rataRating = $query->avg('rating');

        return [
            'list' => $data,
            'totalItems' => $totalItems,
            'rataRating' => round($rataRating, 3)
        ];
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

    public function changeStatus($data) {
        $payload = [
            'is_publish' => $data['is_publish'],
            'updated_at' => date('Y-m-d H:i:s')
        ];

        return DB::table($this->table)->update($payload, ['id' => $data['id']]);
    }
}

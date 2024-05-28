<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid as Generator;
use App\Models\Produk;

class Cart extends Model
{
    use HasFactory;
    public $timestamps = true;
    protected $table = 't_cart';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'user_id',
        'product_id',
        'quantity',
    ];

    public function getCart($params) {
        $produk = new Produk();

        $data = DB::table($this->table)
            ->select(
                't_cart.id',
                't_cart.product_id',
                't_cart.user_id',
                't_cart.quantity',
                'm_produk.id as produk_id',
                'm_produk.nama',
                'm_produk.harga'
            )
            ->leftJoin('m_produk', 'm_produk.id', '=', 't_cart.product_id')
            ->where($params)
            ->get();

        foreach ($data as $value) {
            $value->foto = $produk->getMainPhotoProduk($value->produk_id);
        }

        return $data;
    }

    public function checkCart($params) {
        $payload = [];
        $payload['user_id'] = $params['user_id'];
        $payload['product_id'] = $params['product_id'];

        return DB::table($this->table)
            ->select('quantity')
            ->where($payload)
            ->first();
    }

    public function insertCart($params) {
        $params['id'] = Generator::uuid4()->toString();
        return DB::table($this->table)->insert($params);
    }

    public function updateCart($params) {
        $payload['user_id'] = $params['user_id'];
        $payload['product_id'] = $params['product_id'];

        return DB::table($this->table)->where($payload)->update($params);
    }

    public function changeCart($params) {
        $payload['user_id'] = $params['user_id'];
        $payload['product_id'] = $params['product_id'];

        return DB::table($this->table)->where($payload)->update(['quantity' => $params['quantity']]);
    }

    public function deleteCart($params) {
        return DB::table($this->table)->where(['id' => $params['id']])->delete();
    }
}

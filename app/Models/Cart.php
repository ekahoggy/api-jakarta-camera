<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid as Generator;
use App\Models\Produk;
use Illuminate\Support\Facades\Storage;

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
        'product_varian_id',
        'promo_id',
        'quantity',
    ];

    public function getCart($params) {
        $produk = new Produk();

        $data = DB::table($this->table)
            ->select(
                't_cart.id',
                't_cart.product_id',
                't_cart.product_varian_id',
                't_cart.promo_id',
                't_cart.user_id',
                't_cart.quantity',
                'm_produk.id as produk_id',
                'm_produk.nama',
                'm_produk.sku',
                'm_produk.harga',
                'm_produk.stok',
                'm_produk_varian.sku',
                'm_produk_varian.image',
                'm_produk_varian.varian1_type',
                'm_produk_varian.varian1',
                'm_produk_varian.varian2_type',
                'm_produk_varian.varian2',
                'm_produk_varian.harga as harga_varian',
                'm_produk_varian.stok as stok_varian',
                'm_produk_varian.berat as berat_varian'
            )
            ->leftJoin('m_produk', 'm_produk.id', '=', 't_cart.product_id')
            ->leftJoin('m_produk_varian', 'm_produk_varian.id', '=', 't_cart.product_varian_id')
            ->where($params)
            ->get();

        foreach ($data as $value) {
            $value->foto = $produk->getMainPhotoProduk($value->produk_id);
            $value->foto_varian = Storage::url('images/produk-variant/' . $value->image);
        }

        return $data;
    }

    public function checkCart($params) {
        $payload = [];
        $payload['user_id'] = $params['user_id'];
        $payload['product_id'] = $params['product_id'];
        $payload['product_varian_id'] = $params['product_varian_id'];

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
        $payload['product_id'] = $params['product_id'];

        return DB::table($this->table)->where($payload)->update($params);
    }

    public function changeCart($params) {
        $payload['user_id'] = $params['user_id'];
        $payload['product_id'] = $params['product_id'];
        $payload['product_varian_id'] = $params['product_varian_id'];

        return DB::table($this->table)->where($payload)->update(['quantity' => $params['quantity']]);
    }

    public function deleteCart($params) {
        return DB::table($this->table)->where(['id' => $params['id']])->delete();
    }
}

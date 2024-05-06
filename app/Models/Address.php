<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid as Generator;

class Address extends Model
{
    use HasFactory;
    public $timestamps = true;
    protected $table = 'users_address';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'user_id',
        'recipient',
        'phone_code',
        'phone_number',
        'village_id',
        'subdistrict_id',
        'city_id',
        'province_id',
        'postal_code',
        'address',
        'is_active',
        'created_at',
        'updated_at',
    ];

    public function getAll($params){
        $query = DB::table($this->table)
            ->select(
                'users_address.*', 
                'village.desa as village_name', 
                'subdistrict.kecamatan as subdistrict_name',
                'city.kota as city_name',
                'province.provinsi as province_name'
            )
            ->leftJoin('village', 'village.id', '=', 'users_address.village_id')
            ->leftJoin('subdistrict', 'subdistrict.id', '=', 'users_address.subdistrict_id')
            ->leftJoin('city', 'city.id', '=', 'users_address.city_id')
            ->leftJoin('province', 'province.id', '=', 'users_address.province_id');

        $totalItems = $query->count();
        
        $filter = json_decode($params['filter']);
        
        foreach($filter as $key => $val) {
            if ($key == "user_id" && !empty($val)) {
                $query->where("users_address.user_id", "=", $val);
            }
        }

        if (isset($params['offset']) && !empty($params['offset'])) {
            $query->offset($params['offset']);
        }

        if (isset($params['limit']) && !empty($params['limit'])) {
            $query->limit($params['limit']);
        }

        $data = $query->orderBy('active', 'DESC')->get();

        return [
            'list' => $data,
            'totalItems' => $totalItems
        ];
    }

    public function getAddress($params) {
        $query = DB::table('users_address')
            ->select(
                'users_address.*', 
                'village.desa as village_name', 
                'subdistrict.kecamatan as subdistrict_name',
                'city.kota as city_name',
                'province.provinsi as province_name'
            )
            ->leftJoin('village', 'village.id', '=', 'users_address.village_id')
            ->leftJoin('subdistrict', 'subdistrict.id', '=', 'users_address.subdistrict_id')
            ->leftJoin('city', 'city.id', '=', 'users_address.city_id')
            ->leftJoin('province', 'province.id', '=', 'users_address.province_id');

            if (isset($params['user_id']) && !empty($params['user_id'])) {
                $query->where('users_address.user_id', '=', $params['user_id']);
            }

            if (isset($params['active']) && !empty($params['active'])) {
                $query->where('users_address.active', '=', $params['active']);
            }

        return $query->get();
    }

    public function getAddressById($id) {
        return DB::table('users_address')->where('id', $id)->first();
    }

    public function checkAddress($params) {
        $payload = [];
        $payload['user_id'] = $params['user_id'];
        $payload['product_id'] = $params['product_id'];

        return DB::table('users_address')
            ->select('quantity')
            ->where($payload)
            ->first();
    }

    public function insertAddress($params) {
        return DB::table('users_address')->insert($params);
    }

    public function updateAddress($params) {
        $id = $params['id'];
        $payload = $params;
        unset($payload['id']);

        return DB::table('users_address')->where(['id' => $id])->update($payload);
    }

    public function changeAddress($params) {
        $deactivated = DB::table('users_address')->where('active', 1)->update(['active' => 0]);
        $activated = DB::table('users_address')->where('id', $params['id'])->update(['active' => 1]);
        return $activated;
    }

    public function deleteAddress($params) {
        return DB::table('users_address')->where(['id' => $params['id']])->delete();
    }
}

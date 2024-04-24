<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid as Generator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Service;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'users';

    protected $fillable = [
        'id',
        'type',
        'username',
        'name',
        'email',
        'password',
        'phone_code',
        'phone_number',
        'remember_token',
        'address',
        'photo',
        'roles_id',
        'kode',
        'email_expired',
        'is_active',
    ];

    protected $casts = [
        'id' => 'string'
    ];

    public function getAll($params){
        $query = DB::table($this->table)
            ->where('type', 'customer');
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
            $data[$key]->photo = Storage::url('images/customer/' . $value->photo);
        }

        return [
            'list' => $data,
            'totalItems' => $totalItems
        ];
    }

    public function getById($id){
        $data = DB::table('users')
            ->select('id', 'type', 'username', 'name', 'email', 'phone_code', 'phone_number', 'remember_token', 'address', 'photo', 'roles_id', 'kode', 'email_expired', 'is_active',)
            ->where('id', $id)
            ->first();

        return $data;
    }

    public function simpan($params) { 
        if (isset($params['id']) && !empty($params['id'])) {
            return $this->updateCustomer($params);
        } else {
            return $this->insertCustomer($params);
        }
    }

    public function updateCustomer($params) {
        $service = new Service();

        $id = $params['id']; unset($params['id']);
        $params['updated_at'] = date('Y-m-d H:i:s');
        $params['photo'] = $service->saveImage("customer/", $params['photo']);

        if (isset($params['password']) && !empty($params['password'])) {
            $params['password'] = bcrypt($params['password']);
        }

        return DB::table('users')->where('id', $id)->update($params);
    }

    public function insertCustomer($params) {
        $service = new Service();

        $params['id'] = Generator::uuid4()->toString();
        $params['created_at'] = date('Y-m-d H:i:s');
        $params['photo'] = $service->saveImage("customer/", $params['photo']);

        if (isset($params['password']) && !empty($params['password'])) {
            $params['password'] = bcrypt($params['password']);
        }

        return DB::table('users')->insert($params);
    }
}

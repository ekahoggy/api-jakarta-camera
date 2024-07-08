<?php

namespace App\Models;

use App\Mail\Subscribe;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid as Generator;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;

class Subscription extends Model
{
    use HasFactory;

    protected $table = 'm_subscription';

    protected $fillable = [
        'id',
        'name',
        'email',
        'is_subscribed',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    protected $casts = [
        'id' => 'string'
    ];

    public function getAll($params){
        $query = DB::table($this->table)
        ->select(
            'm_subscription.*',
            'users.photo',
            'users.name'
        )
        ->leftJoin('users','users.email', '=', 'm_subscription.email');

        if (isset($params['offset']) && !empty($params['offset'])) {
            $query->offset($params['offset']);
        }

        if (isset($params['limit']) && !empty($params['limit'])) {
            $query->limit($params['limit']);
        }

        $data = $query->orderBy('m_subscription.created_at', 'DESC')->get();
        $totalItems = $query->count();
        return [
            'list' => $data,
            'totalItems' => $totalItems
        ];
    }

    public function getByEmail($email){
        $data = DB::table($this->table)
            ->where('email', $email)
            ->first();

        return $data;
    }

    public function simpan($params) {
        if (isset($params['id']) && !empty($params['id'])) {
            return $this->edit($params);
        } else {
            return $this->post($params);
        }
    }

    public function edit($params) {
        $id = $params['id'];
        unset($params['id']);
        $params['updated_at'] = date('Y-m-d H:i:s');

        DB::table($this->table)->where('id', $id)->update($params);

        $data = $params;
        $data['id'] = $id;
        return $data;
    }

    public function changeStatus($params) {
        $params['updated_at'] = date('Y-m-d H:i:s');

        return DB::table($this->table)->where('id', $params['id'])->update($params);
    }

    public function post($params) {
        $params['id'] = Generator::uuid4()->toString();
        $params['created_at'] = date('Y-m-d H:i:s');

        DB::table($this->table)->insert($params);
        $this->sendEmail($params);
        
        return $params;
    }

    public function sendEmail($data){
        $appUrl = env("APP_CLIENT_URL", "http://localhost:3200");
        $data = [
            'name' => $data['name'],
            'email' => $data['email'],
            'link_unsub' => "$appUrl/unsubscribe?email=" .$data['email']
        ];

        Mail::to($data['email'])->send(new Subscribe($data));
    }
}

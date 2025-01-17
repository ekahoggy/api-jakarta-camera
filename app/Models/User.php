<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'type',
        'username',
        'name',
        'email',
        'phone_code',
        'password',
        'phone_number',
        'address',
        'photo',
        'roles_id',
        'kode',
        'email_expired',
        'is_active',
        'gauth_id',
        'gauth_type',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'id' => 'string'
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getUser($params){
        $query = DB::table($this->table);

        if($params->type !== null){
            $query->where("type", $params->type);
        }
        if($params->name !== null){
            $query->where('name', 'like', '%' . $params->name . '%');
        }
        if($params->is_active !== null){
            $query->where("is_active", $params->is_active);
        }

        $data = $query->limit(20)
            ->orderBy('name', 'ASC')
            ->get();

        return $data;
    }

    public function getDetailUser($id){
        $data = DB::table($this->table)
            ->select('id', 'type', 'username', 'name', 'email', 'phone_code', 'phone_number', 'address', 'photo', 'roles_id', 'kode', 'email_expired', 'is_active', 'created_at', 'updated_at')
            ->where("id", $id)
            ->first();

        return $data;
    }

    public function changeStatus($id, $status = 'aktif'){
        $query = DB::table($this->table)
            ->where('id', $id)
            ->update(['is_active' => $status]);

        return $query;
    }

    public function updateData($id, $data = []){
        $query = DB::table($this->table)
            ->where('id', $id)
            ->update($data);

        return $query;
    }

    public function roles(){
        return $this->belongsTo(Role::class, 'roles_id')->select('name', 'id');
    }

    public function checkEmail($email){
        $query = DB::table($this->table)
            ->where('email', $email)
            ->orWhere('name', 'like', '%' . $email . '%')->get();

        return $query;
    }

    public function getByEmail($email){
        return DB::table($this->table)->where('email', '=', $email);
    }

    public function updatePasswordByEmail($params){
        return DB::table($this->table)
            ->where('email', $params['email'])
            ->update(['password' => $params['password']]);
    }
}


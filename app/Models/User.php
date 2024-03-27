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

    public function getUser($req){
        $query = DB::table($this->table);

        if($req->type !== null){
            $query->where("type", $req->type);
        }
        if($req->name !== null){
            $query->where('name', 'like', '%' . $req->name . '%');
        }
        if($req->is_active !== null){
            $query->where("is_active", $req->is_active);
        }

        $data = $query->limit(20)
                    ->orderBy('name', 'ASC')
                    ->get();

        return $data;
    }

    public function getDetailUser($id){
        $data = DB::table($this->table)
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
}

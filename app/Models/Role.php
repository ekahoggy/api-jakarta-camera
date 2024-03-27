<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Role extends Model
{
    use HasFactory;
    // public $timestamps = false;
    protected $table = 'm_roles';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'access',
        'is_deleted',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'id' => 'string'
    ];

    public function getRole($req){
        $query = DB::table($this->table);

        if($req->is_deleted !== null){
            $query->where("is_deleted", $req->is_deleted);
        }

        $data = $query->limit(20)
                    ->orderBy('name', 'ASC')
                    ->get();

        return $data;
    }

    function getRoleActive(){
        return Role::where('is_deleted', 0)->get();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    function getRoleActive(){
        return Role::where('is_deleted', 0)->get();
    }
}

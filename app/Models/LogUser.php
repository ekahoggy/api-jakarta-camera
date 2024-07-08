<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid as Generator;

class LogUser extends Model
{
    use HasFactory;

    protected $table = 'l_users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'ref_name',
        'ref_id',
        'notes',
        'created_at',
        'created_by',
        'updated_at'
    ];

    protected $casts = [
        'id' => 'string'
    ];

    public function post($ref_name, $ref_id, $notes, $user_id)
    {
        $params['id'] = Generator::uuid4()->toString();
        $params['ref_name'] = $ref_name;
        $params['ref_id'] = $ref_id;
        $params['notes'] = $notes;
        $params['created_at'] = date('Y-m-d H:i:s');
        $params['created_by'] = $user_id;

        return DB::table($this->table)->insert($params);
    }
}

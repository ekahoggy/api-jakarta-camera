<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid as Generator;
use Illuminate\Support\Facades\DB;

class VoucherUser extends Model
{
    use HasFactory;

    protected $table = 'm_voucher_user';

    protected $fillable = [
        'id',
        'voucher_id',
        'user_id'
    ];

    protected $casts = [
        'id' => 'string'
    ];

    public function post($params) {
        $service = new Service();
        $params['id'] = Generator::uuid4()->toString();

        DB::table($this->table)->insert($params);
        return $params;
    }
}

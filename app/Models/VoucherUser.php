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

    public function getUserByVoucher($id) {
        $data = DB::table($this->table)->select(
            'm_voucher_user.*',
            'users.name',
            'users.email',
        )
        ->leftJoin('users', 'users.id', '=', 'm_voucher_user.user_id')
        ->leftJoin('m_voucher', 'm_voucher.id', '=', 'm_voucher_user.voucher_id')
        ->where('m_voucher_user.voucher_id', $id)->get();

        return $data;
    }
}

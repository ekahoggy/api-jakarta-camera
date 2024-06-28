<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid as Generator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Service;

class Voucher extends Model
{
    use HasFactory;

    protected $table = 'm_voucher';

    protected $fillable = [
        'id',
        'user_id',
        'redeem_code',
        'voucher',
        'tanggal_mulai',
        'tanggal_selesai',
        'jam_mulai',
        'jam_selesai',
        'gambar',
        'deskripsi',
        'kategori',
        'qty',
        'voucher_used',
        'type',
        'jenis',
        'voucher_value',
        'voucher_max',
        'voucher_min_beli',
        'untuk',
        'is_status',
    ];

    protected $casts = [
        'id' => 'string'
    ];

    public function getAll($params){
        $query = DB::table($this->table);
        $totalItems = $query->count();

        if (isset($params['filter']) && !empty($params['filter'])) {
            $filter = json_decode($params['filter']);
            foreach ($filter as $key => $value) {
                if($key === 'kode'){
                    $query->where('voucher', 'like', '%' . $value . '%');
                    $query->orWhere('redeem_code', 'like', '%' . $value . '%');
                }
                if($key === 'is_status'){
                    if($value !== null){
                        $query->where('is_status', $value);
                    }
                }
            }
        }

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
            $data[$key]->gambar = Storage::url('images/voucher/' . $value->gambar);
        }

        return [
            'list' => $data,
            'totalItems' => $totalItems
        ];
    }

    public function getById($id){
        $data = DB::table('m_voucher')
            ->where('id', $id)
            ->first();

        return $data;
    }

    public function getVoucher($params){
        $data = DB::table('m_voucher')
            ->where('is_status', 1);

        if (isset($params['jenis']) && !empty($params['jenis'])) {
            $data->where('jenis', $params['jenis']);
        }
        if (isset($params['user_id']) && !empty($params['user_id'])) {
            $data->where('untuk', 'user');
            $data->where('user_id', $params['user_id']);
        }

        return $data->get();
    }

    public function simpan($params) {
        if (isset($params['id']) && !empty($params['id'])) {
            return $this->updateVoucher($params);
        } else {
            return $this->insertVoucher($params);
        }
    }

    public function updateVoucher($params) {
        $service = new Service();

        $id = $params['id']; unset($params['id']);
        $params['updated_at'] = date('Y-m-d H:i:s');
        $params['gambar'] = $service->saveImage("voucher/", $params['gambar']);

        return DB::table('m_voucher')->where('id', $id)->update($params);
    }

    public function insertVoucher($params) {
        $service = new Service();

        $params['id'] = Generator::uuid4()->toString();
        $params['created_at'] = date('Y-m-d H:i:s');
        $params['gambar'] = $service->saveImage("voucher/", $params['gambar']);

        return DB::table('m_voucher')->insert($params);
    }
}

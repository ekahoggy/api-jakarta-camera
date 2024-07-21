<?php

namespace App\Models;

use App\Mail\Voucher as MailVoucher;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid as Generator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Service;
use Illuminate\Support\Facades\Mail;

class Voucher extends Model
{
    use HasFactory;

    protected $table = 'm_voucher';

    protected $fillable = [
        'id',
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
        'for_co',
        'used_to',
        'is_hidden',
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
        $today = date('Y-m-d');
        $data = DB::table('m_voucher as v')
            ->selectRaw('v.*')
            ->whereDate('v.tanggal_mulai','<=', $today)
            ->whereDate('v.tanggal_selesai','>=', $today)
            ->where('v.is_hidden', 0)
            ->where('v.is_status', 1);

        if (isset($params['jenis']) && !empty($params['jenis'])) {
            $data->where('jenis', $params['jenis']);
        }
        if (isset($params['redeem_code']) && !empty($params['redeem_code'])) {
            $data->where('v.redeem_code', 'like', '%' . $params['redeem_code'] . '%');
            // $data->where('v.is_hidden', 1);
            // if (isset($params['user_id']) && !empty($params['user_id'])) {
            //     $data->orWhere('v.untuk', 'user');
            //     $data->orWhere('vu.user_id', $params['user_id']);
            // }
        }

        $model = $data->get();

        foreach ($model as $i => $value) {
            $model[$i]->gambar = Storage::url('images/voucher/' . $value->gambar);
        }

        return $model;
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

    public function changeStatusVoucher($params) {
        $service = new Service();

        $id = $params['id']; unset($params['id']);
        $params['updated_at'] = date('Y-m-d H:i:s');

        return DB::table('m_voucher')->where('id', $id)->update($params);
    }

    public function insertVoucher($params) {
        $service = new Service();

        $params['id'] = Generator::uuid4()->toString();
        $params['created_at'] = date('Y-m-d H:i:s');
        $params['gambar'] = $service->saveImage("voucher/", $params['gambar']);

        // $user = User::find($params['user_id']);
        // $data = [
        //     'subject' => '🔥 Selamat Kamu Mendapatkan Kode Voucher Dari Jakarta Camera !! 🔥',
        //     'name' => $user->name,
        //     'email' => $user->email,
        //     'redeem_code' => $params['redeem_code'],
        //     'tanggal_mulai' => $params['tanggal_mulai'],
        //     'jam_mulai' => $params['jam_mulai'],
        //     'tanggal_selesai' => $params['tanggal_selesai'],
        //     'jam_selesai' => $params['jam_selesai'],
        //     'voucher' => $params['type'] === 'P' ? $params['voucher_value'].'%' : 'Rp '.number_format($params['voucher_value']),
        // ];

        // Mail::to($data['email'])->send(new MailVoucher($data));
        // $user_id = $params['data']['user_id'];
        // $note = $params['data']['recipient'].' membuat order dengan invoice #'.$model['invoice_number'];

        // $this->logUser->post('t_order', $model['id'], $note, $user_id);
        DB::table('m_voucher')->insert($params);
        return $params;
    }

    public function getVoucherChanged(){
        $date = date('Y-m-d');
        $time = date('H:i:s');

        $query = DB::table($this->table)
        ->select(
            'm_voucher.id',
            'm_voucher.redeem_code',
            'm_voucher.type',
            'm_voucher.jenis',
            'm_voucher.voucher',
            'm_voucher.tanggal_mulai',
            'm_voucher.jam_mulai',
            'm_voucher.tanggal_selesai',
            'm_voucher.jam_selesai',
            'm_voucher.is_status'
        )
        ->where('m_voucher.is_status', 1)
        ->where(function ($query) use ($date, $time) {
            $query->where(function ($query) use ($date, $time) {
                $query->whereDate('m_voucher.tanggal_selesai', '<', $date)
                    ->orWhere(function ($query) use ($date, $time) {
                        $query->whereDate('m_voucher.tanggal_selesai', '=', $date)
                            ->whereTime('m_voucher.jam_selesai', '<', $time);
                    });
            });
        });

        return $query->get();
    }
}

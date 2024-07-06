<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid as Generator;

class NewsKomentar extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'm_news_komentar';

    protected $fillable = [
        'kategori',
        'is_active'
    ];

    protected $casts = [
        'id' => 'integer'
    ];

    public function getAll($params){
        $query = DB::table($this->table)
            ->orderBy('created_at', 'ASC');

        $data = $query->get();
        $totalItems = $query->count();

        return [
            'list' => $data,
            'totalItems' => $totalItems
        ];
    }

    public function getNewsKomentar(){
        $query = DB::table($this->table)
                ->select('m_news_komentar.*','m_news.judul')
                ->leftJoin('m_news','m_news.id', '=', 'm_news_komentar.news_id');
        $data = $query->orderBy('is_publish', 'ASC')
                    ->orderBy('tanggal', 'DESC')
                    ->get();
        $totalItems = $query->count();
        return [
            'list' => $data,
            'totalItems' => $totalItems
        ];
    }

    public function getNewsKomentarByName($kategori){
        return DB::table($this->table)->where('kategori', $kategori)->first();
    }


    public function getNewsKomentarByNameMulti($data){
        return  DB::table($this->table)
            ->whereIn('kategori', $data)
            ->orderBy('kategori', 'ASC')->get();
    }

    public function getByNewsId($id){
        $query = DB::table($this->table)
            ->where('news_id', '=', $id)
            ->where('is_publish', 1)
            ->orderBy('created_at', 'ASC');

        $data = $query->get();
        $totalItems = $query->count();

        return [
            'list' => $data,
            'totalItems' => $totalItems
        ];
    }

    public function postBalasan($data) {
        $payload = [
            'balasan' => $data['balasan'],
            'tanggal_balasan' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        return DB::table($this->table)->update($payload, ['id' => $data['id']]);
    }

    public function changeStatus($data) {
        $payload = [
            'is_publish' => $data['is_publish'],
            'updated_at' => date('Y-m-d H:i:s')
        ];

        return DB::table($this->table)->update($payload, ['id' => $data['id']]);
    }

    public function postKomentar($data) {
        $payload = [
            'id' => Generator::uuid4()->toString(),
            'news_id' => $data['news_id'],
            'nama' => $data['nama'],
            'email' => $data['email'],
            'komentar' => $data['komentar'],
            'tanggal' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => $data['user_id'],
        ];

        return DB::table($this->table)->insert($payload);
    }
}

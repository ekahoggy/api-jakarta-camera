<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid as Generator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Service;

class EdukasiVideo extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'm_edukasi_video';

    protected $fillable = [
        'edukasi_id',
        'video_url',
        'title',
        'is_lock',
        'urutan'
    ];

    protected $casts = [
        'id' => 'string'
    ];

    public function simpan($params, $id) {
        if (isset($params['id']) && !empty($params['id'])) {
            return $this->updateEdukasi($params, $id);
        } else {
            return $this->insertEdukasi($params, $id);
        }
    }

    public function updateEdukasi($params, $id) {
        $service = new Service();

        $id = $params['id']; unset($params['id']);
        $params['video_url'] = $service->saveVideo("edukasi/", $params['video_url']);

        return DB::table($this->table)->where('id', $id)->update($params);
    }

    public function insertEdukasi($params, $id) {
        $service = new Service();

        $params['id'] = Generator::uuid4()->toString();
        $params['edukasi_id'] = $id;
        $params['video_url'] = $service->saveVideo("edukasi/", $params['video_url']);

        return DB::table($this->table)->insert($params);
    }
}

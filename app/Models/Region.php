<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Region extends Model
{
    public function village($params) {
        $query = DB::table('village')->select('*');

        if (isset($params['kecamatan_id']) && !empty($params['kecamatan_id'])) {
            $query->where('kecamatan_id', '=', $params['kecamatan_id']);
        }

        $data = $query->orderBy('desa', 'asc')->get();

        return $data;
    }
    public function subdistrict($params) {
        $query = DB::table('subdistrict')->select('*');

        if (isset($params['kota_id']) && !empty($params['kota_id'])) {
            $query->where('kota_id', '=', $params['kota_id']);
        }

        $data = $query->orderBy('kecamatan', 'asc')->get();

        return $data;
    }
    public function city($params) {
        $query = DB::table('city')->select('*');

        if (isset($params['provinsi_id']) && !empty($params['provinsi_id'])) {
            $query->where('provinsi_id', '=', $params['provinsi_id']);
        }

        $data = $query->orderBy('kota', 'asc')->get();

        return $data;
    }
    public function province() {
        $data = DB::table('province')
            ->select('*')
            ->orderBy('provinsi', 'asc')
            ->get();

        return $data;
    }
}

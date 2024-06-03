<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'm_setting';
    protected $primaryKey = 'id';

    protected $fillable = [
        'setting_name',
        'setting_value',
        'setting_kategori',
        'setting_type',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    public function getAll($params){
        $query = DB::table($this->table);

        if(isset($params['kategori']) && !empty($params['kategori'])){
            $query->where('setting_kategori', $params['kategori']);
        }

        $data = $query->get();

        $arr = [];
        foreach ($data as $key => $value) {
            if($value->setting_type === 'F'){
                $value->setting_value = Storage::url('images/setting/' . $value->setting_value);
            }
            if($value->setting_name === 'keyword'){
                $value->setting_value = str_replace(' ', '', $value->setting_value);
                $value->setting_value = explode(',', $value->setting_value);
                // array_pop($value->setting_value);
            }
            $arr[$value->setting_name]['name'] = $value->setting_name;
            $arr[$value->setting_name]['value'] = $value->setting_value;
            $arr[$value->setting_name]['kategori'] = $value->setting_kategori;
            $arr[$value->setting_name]['type'] = $value->setting_type;
        }

        return array_values($arr);
    }

    public function getById($id){
        $data = DB::table($this->table)
            ->where('id', $id)
            ->first();

        return $data;
    }

    public function simpan($params) {
        $service = new Service();

        $params['favicon'] = $service->saveImage("setting/", $params['favicon']);
        $params['icon'] = $service->saveImage("setting/", $params['icon']);
        $params['keyword'] = implode(',', $params['keyword']);

        foreach ($params as $key => $value) {
            $up = [
                'setting_value' => $value
            ];
            DB::table($this->table)->where('setting_name', $key)->update($up);
        }
        return $params;
    }
}

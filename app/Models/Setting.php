<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'm_setting';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'setting_name',
        'setting_value',
        'setting_kategori',
        'setting_type',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    protected $casts = [
        'id' => 'string'
    ];
}

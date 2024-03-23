<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'm_sliders';

    protected $fillable = [
        'picture',
        'title',
        'title_en',
        'content',
        'content_en',
        'url',
        'is_status',
        'index_position'
    ];

    protected $casts = [
        'id' => 'string'
    ];
}

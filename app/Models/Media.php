<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Uuid as Generator;

class Media extends Model
{
    use HasFactory;

    protected $table = 'm_produk_media';

    protected $fillable = [
        'woo_media_id',
        'm_produk_id',
        'media_link',
        'name',
        'alt',
        'is_video',
        'is_main',
        'urutan',
        'is_active'
    ];

    protected $casts = [
        'id' => 'string'
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;

    protected $table = 'media';

    protected $fillable = [
        'path',
        'for',
        'primary',
        'created_by',
        'updated_by',
    ];
    protected $hidden = [
        'id',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];
}

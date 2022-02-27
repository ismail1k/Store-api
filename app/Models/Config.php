<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    use HasFactory;

    protected $table = 'configs';
    protected $fillable = [
        'key',
        'value',
        'created_by',
        'updated_by',
    ];
    protected $hidden = [
        'created_by',
        'updated_by',
    ];
}

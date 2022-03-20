<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class transaction extends Model
{
    use HasFactory;
    protected $table = 'transactions';
    protected $fillable = [
        'number',
        'method',
        'price',
        'state',
        'created_at',
        'updated_at',
    ];
    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
    ];
}

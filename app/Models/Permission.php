<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class Permission extends Model
{
    use HasFactory;
    protected $table = 'permissions';
    
    protected $fillable = [
        'id',
        'user_id',
        'name',
        'access',
    ];

}

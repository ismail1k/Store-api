<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $table = 'orders';
    
    protected $fillable = [
        'id',
        'cart_id',
        'user_id',
        'fullname',
        'address',
        'phone',
        'payment_method',
        'note',
        'state',
    ];
}

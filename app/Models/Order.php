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
        'product_id',
        'user_id',
        'fullname',
        'quantity',
        'address',
        'phone',
        'payment_method',
        'note',
        'state',
    ];
}

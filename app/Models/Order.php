<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $table = 'orders';
    
    protected $fillable = [
        'user_id',
        'fullname',
        'address',
        'email',
        'phone',
        'note',
        'payment_id',
        'state',
    ];
    
    public function items(){
        return $this->hasMany(OrderItems::class);
    }
    
    public function payment(){
        return $this->hasOne(Payment::class);
    }
}

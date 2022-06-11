<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $table = 'payments';
    
    protected $fillable = [
        'order_id',
        'reference',
        'amount',
        'provider',
        'status',
    ];
    
    public function order(){
        return $this->hasOne(Order::class);
    }
}

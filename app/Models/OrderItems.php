<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItems extends Model
{
    use HasFactory;
    protected $table = 'order_items';
    
    protected $fillable = [
        'order_id',
        'product_id',
        'item_id',
        'quantity',
        'value',
        'refund',
        'payed',
    ];
    
    public function order(){
        return $this->belongsTo(Orders::class);
    }
    
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sku extends Model
{
    use HasFactory;
    
    protected $table = 'skus';
    protected $fillable = [
        'inventory_id',
        'value',
        'valid',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function inventory(){
        return $this->belongsTo(Inventory::class);
    }
}

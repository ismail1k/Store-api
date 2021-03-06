<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventories';
    protected $fillable = [
        'name',
        'digital',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    
    public function items(){
        return $this->hasMany(Sku::class);
    }
}

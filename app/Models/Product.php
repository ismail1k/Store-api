<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $fillable = [
        'name',
        'short_description',
        'description',
        'tags',
        'category_id',
        'inventory_id',
        'price',
        'discount',
        'virtual',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];
    protected $hidden = [
        'category_id',
        'inventory_id',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    public function media(){
        return $this->hasMany(Media::class);
    }

    public function inventory(){
        return $this->belongsTo(Inventory::class);
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }
}

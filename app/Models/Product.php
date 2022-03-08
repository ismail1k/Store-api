<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Yab\ShoppingCart\Traits\Purchaseable;
use Yab\ShoppingCart\Contracts\Purchaseable as PurchaseableInterface;

class Product extends Model implements PurchaseableInterface
{
    use HasFactory, Purchaseable;

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

    public function orders(){
        return $this->hasMany(Order::class);
    }
}

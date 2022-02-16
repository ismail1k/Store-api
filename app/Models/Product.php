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
        'id',
        'name',
        'description',
        'tags',
        'media_id',
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
}

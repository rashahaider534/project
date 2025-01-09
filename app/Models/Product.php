<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable=[
        'name',
        'quantity',
        'describtion',
        'image',
        'store_id',
    ];

    public function order_items()
    {
        return $this->hasMany(Order::class);
    }
    public function cart_items()
    {
        return $this->belongsToMany(Cart_items::class, 'cart_item_product', 'product_id', 'cart_item_id')
        ->withPivot('quantity'); 
    }
}

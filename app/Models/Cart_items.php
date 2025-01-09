<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart_items extends Model
{
    use HasFactory;
    protected $fillable=['product_id','quantity','user_id'];
    // public function product()
    // {
    //     return $this->belongsTo(Product::class,'product_id');
    // }
    public function product()
    {
        return $this->belongsToMany(Product::class, 'cart_item_product', 'cart_item_id', 'product_id')
        ->withPivot('quantity');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

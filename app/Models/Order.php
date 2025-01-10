<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable=['total_price','user_id','driver_id','status'];
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
 
    public function order_items()
    {
        return $this->hasMany(Order_items::class);
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
}

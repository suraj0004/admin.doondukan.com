<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\OrderItem;
use App\Models\User;

class Orders extends Model
{
    protected $table = "orders";

    public function orderitem()
    {
    	return $this->hasMany(OrderItem::class, 'order_id', 'id')->with('product:id,name,weight,weight_type');
    }

    public function seller()
    {
    	return $this->hasOne(User::class, 'id', 'seller_id');
    }
}

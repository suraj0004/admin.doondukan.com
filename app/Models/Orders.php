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
    	return $this->hasMany(OrderItem::class, 'order_id', 'id')->with('product:id,name,weight,weight_type,image');
    }

    public function seller()
    {
    	return $this->hasOne(User::class, 'id', 'seller_id');
    }

    public function store()
    {
    	return $this->hasOne(Store::class, 'user_id', 'seller_id');
    }
    public function buyer()
    {
        return $this->hasOne(User::class, 'id', 'buyer_id');
    }

    /**
     * Get the shippingAddress associated with the Orders
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function shippingAddress()
    {
        return $this->hasOne(ShippingAddress::class, 'order_id', 'id');
    }


}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

class OrderItem extends Model
{
    protected $table = "order_items";

    public function product()
    {
    	return $this->hasOne(Product::class, 'id', 'product_id');
    }
}

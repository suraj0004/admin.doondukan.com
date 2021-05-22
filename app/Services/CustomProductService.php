<?php
namespace App\Services;

use App\Models\TempProduct;

class CustomProductService{

     // Return Collection of data or return NULL
    public function addUserCustomProduct(int $user_id, string $product_name, int $product_weight, string $weight_type)
    {
       $product = new TempProduct();
       $product->name = $product_name;
       $product->weight = $product_weight;
       $product->weight_type = $weight_type;
       $product->user_id = $user_id;
       $product->save();
       return $product;

    }

    public function updateUserCustomProduct(int $product_id, string $product_name, int $product_weight, string $weight_type)
    {
       $product =  TempProduct::find($product_id);
       $product->name = $product_name;
       $product->weight = $product_weight;
       $product->weight_type = $weight_type;
       $product->save();
       return $product;

    }

    // Return Collection of data or return NULL
    public function fetchUserCustomProductList(int $user_id)
    {
        return TempProduct::withTrashed()->where('user_id',$user_id)->latest()->get();
    }

}

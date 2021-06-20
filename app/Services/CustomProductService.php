<?php
namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Str;

class CustomProductService
{

    // Return Collection of data or return NULL
    public function addUserCustomProduct(int $user_id, string $name, int $weight, string $weight_type, int $price, int $category_id, string $image_base64)
    {
        $product = new Product();
        $product->user_id = $user_id;
        $product->name = $name;
        $product->slug = Str::slug($name);
        $product->image = saveImageFromBase64(config("constants.disks.PRODUCT"), $product->slug, $image_base64);
        $product->category_id = $category_id;
        $product->weight = $weight;
        $product->weight_type = $weight_type;
        $product->price = $price;
        $product->save();
        return $product;

    }

    public function updateUserCustomProduct(int $product_id, string $name, int $weight, string $weight_type, int $price, int $category_id, $image_base64)
    {
        $product = Product::find($product_id);
        $product->name = $name;
        $product->slug = Str::slug($name);
        if($image_base64){
            $product->image = saveImageFromBase64(config("constants.disks.PRODUCT"), $product->slug, $image_base64, $product->image);
        }
        $product->category_id = $category_id;
        $product->weight = $weight;
        $product->weight_type = $weight_type;
        $product->price = $price;
        $product->save();
        return $product;

    }

    // Return Collection of data or return NULL
    public function fetchUserCustomProductList(int $user_id)
    {
        return Product::select('id', 'name', 'weight', 'weight_type', 'price', 'image', 'category_id', 'created_at')
            ->withTrashed()
            ->with(['category'])
            ->where('user_id', $user_id)
            ->latest()
            ->paginate(20);
    }

}

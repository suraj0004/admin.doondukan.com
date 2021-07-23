<?php

namespace App\Http\Controllers\Api\Ecommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Store;
use App\Models\Stock;
use DB;
use App\Http\Resources\Ecommerce\CategoryCollection;
use App\Http\Resources\Ecommerce\CategoryProductCollection;
use App\Http\Resources\Ecommerce\StoreResource;
use App\Http\Resources\ShopCollection;

class ShopController extends Controller
{
    public function index($seller_id, $slug)
    {
        $getUserId = Store::select('user_id')->where('user_id', $seller_id)->first();
        if (!$getUserId) {
            return response()->json(['statusCode' => 200, 'success' => false, 'message' => "Shop not found."], 200);
        }
        $data = Category::select('categories.id as category_id', 'categories.category_name', 'categories.slug', 'categories.image', DB::raw('COUNT(stocks.id) as product_count'))
            ->join('products', 'products.category_id', '=', 'categories.id')
            ->join('stocks', function ($join) use ($getUserId) {
                $join->on('stocks.product_id', '=', 'products.id')
                    ->on('stocks.price', '>', DB::raw("0"))
                    ->on('stocks.user_id', '=', DB::raw($getUserId->user_id));
            })
            ->groupBy('categories.id')
            ->get();
        if ($data->isEmpty()) {
            return response()->json(['statusCode' => 200, 'success' => false, 'message' => "No data found."], 200);
        }

        return (new CategoryCollection($data))->additional([
            "message" => "Categories Data",
        ]);
    }

    public function getCategoryProducts(Request $request)
    {
        $getUserId = Store::select('user_id')->where('user_id', $request->seller_id)->first();
        if (!$getUserId) {
            return response()->json(['statusCode' => 200, 'success' => false, 'message' => "Shop not found."], 200);
        }

        $data = Category::select('categories.id as category_id', 'categories.category_name', 'categories.slug','products.name','products.slug as product_slug','products.id as product_id','products.weight','products.weight_type','stocks.price','products.image','stocks.quantity')
                ->join('products', 'products.category_id', '=', 'categories.id')
                ->join('stocks', 'stocks.product_id', '=', 'products.id')
                ->where('stocks.user_id',$getUserId->user_id)
                ->where('stocks.price','>',0)
                ->where('categories.slug',$request->categorySlug)
                ->get();

        if ($data->isEmpty()) {
            return response()->json(['statusCode' => 200, 'success' => false, 'message' => "No data found."], 200);
        }

        return (new CategoryProductCollection($data))->additional([
            "message" => "Category Product Data",
        ]);
    }

    public function sellerInfo($seller_id)
    {
        $sellerData = Store::select('id','user_id','name','address','logo','open_at','close_at','delivery_medium','order_within_km','minimum_order_amount','delivery_charges')->where('user_id',$seller_id)->with('user:id,name')->first();
        if(!$sellerData) {
            return response()->json(['statusCode' => 200, 'success' => false, 'message' => "No data found."], 200);
        }

        return (new StoreResource($sellerData))->additional([
            "message" => "Seller information get successfully.",
        ]);
    }

    public function getNearByShop()
    {
        $data = Store::select('id','user_id','name','slug','address','logo','about','open_at','close_at')->with(['user'])->get();
        if($data->isEmpty()){
            return response()->json(['statusCode' => 200, 'success' => false, 'message' => "No data found."], 200);
        }

        return (new ShopCollection($data))->additional([
            "message" => "Shops listing",
        ]);
    }
}

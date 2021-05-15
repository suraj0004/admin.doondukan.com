<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Store;
use App\Models\Stock;
use DB;
use App\Http\Resources\Ecommerce\CategoryCollection;

class ShopController extends Controller
{
    public function index($id, $slug)
    {
        $getUserId = Store::select('user_id')->where('id', $id)->first();
        if (!$getUserId) {
            return response()->json(['statusCode' => 200, 'success' => false, 'message' => "Shop not found."], 200);
        }
        $data = Category::select('categories.id as category_id', 'categories.category_name', 'categories.slug', DB::raw('COUNT(products.id) as product_count'))
            ->leftJoin('products', 'products.category_id', '=', 'categories.id')
            ->leftJoin('stocks', function ($join) use ($getUserId) {
                $join->on('stocks.product_id', '=', 'products.id')
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
        $getUserId = Store::select('user_id')->where('id', $request->id)->first();
        if (!$getUserId) {
            return response()->json(['statusCode' => 200, 'success' => false, 'message' => "Shop not found."], 200);
        }

        $data = Category::select('categories.id as category_id', 'categories.category_name', 'categories.slug','products.name','products.slug as product_slug','products.id as product_id','products.weight','products.weight_type','stocks.price')
                ->join('products', 'products.category_id', '=', 'categories.id')
                ->join('stocks', 'stocks.product_id', '=', 'products.id')
                ->where('stocks.user_id',$getUserId->user_id)
                ->where('categories.slug',$request->categorySlug)
                ->get();

        if ($data->isEmpty()) {
            return response()->json(['statusCode' => 200, 'success' => false, 'message' => "No data found."], 200);
        }

        return response()->json(['statusCode' => 200, 'success' => true, 'message' => "Category Product Data", "data" => $data], 200);
    }
}

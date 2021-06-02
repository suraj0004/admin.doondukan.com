<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\CategoryCollection;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\TempProduct;
use App\Models\Category;

class ProductController extends Controller
{
	//Function to get the list of all the products
    public function getproductList()
    {
    	$user = Auth::User();
    	$productlist = Product::all();
    	$tempProductlist = TempProduct::where('user_id',$user->id)->get();
    	if( count($tempProductlist) <= 0 )
    	{
    		$tempProductlist = [];
    	}
    	$data['main'] = $productlist;
		$data['temp'] = $tempProductlist;
		return response()->json(['statusCode'=>200,'success'=>true,'message'=>'Products List.','data'=>$data], 200);
    }

    public function getProductCatalogue(Request $request)
    {
        $categories = Category::select("id","category_name")->with(['products'])->get();
        return (new CategoryCollection($categories))->additional([
            "message" => "Product Data",
        ]);
    }
}

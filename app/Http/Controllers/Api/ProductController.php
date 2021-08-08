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
    private $per_page = 50;
	//Function to get the list of all the products
    public function getproductList(Request $request)
    {
    	$user = Auth::User();
    	$data = Product::where(function($query) use ($user) {
            $query->whereNull('user_id')->orWhere('user_id', $user->id);
        });
        if(isset($request->search) && !empty($request->search)){
            $data = $data->where('name','LIKE','%'.$request->search . '%');
        }
        $data = $data->paginate($this->per_page);
		return response()->json(['statusCode'=>200,'success'=>true,'message'=>'Products List.','data'=>$data], 200);
    }

    public function getProductCatalogue(Request $request)
    {
        $categories = Category::select("id","category_name")->with(['products' => function($query) use($user){
            $query->whereNull('user_id');
        } ])->get();
        return (new CategoryCollection($categories))->additional([
            "message" => "Product Data",
        ]);
    }

    public function getCategories()
    {
        $categories = Category::select("id","category_name")->get();
        if($categories->isEmpty()) {
            return response()->json(['statusCode'=>200,'success'=>false,'message'=>'No data found.'], 200);
        }

        return response()->json(['statusCode'=>200,'success'=>true,'message'=>'Categories List.','data'=>$categories], 200);
    }
}

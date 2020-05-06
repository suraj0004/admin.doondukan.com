<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
	//Function to get the list of all the products
    public function getproductList()
    {
    	
    	$productlist = Product::all();
		return response()->json(['statusCode'=>200,'success'=>true,'message'=>'Products List.','data'=>$productlist], 200);
    }
}

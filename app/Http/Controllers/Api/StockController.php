<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Price;
use App\Models\Stock;
use Validator;

class StockController extends Controller
{
    //Set Stock Price.This Function is used to add stock price.
	public function setStockprice(Request $request)
	{
		$validator = Validator::make($request->all(), [ 
            'stock_id' => 'required|integer',
            'price' => 'required|numeric',
        ]);

        if ($validator->fails())
		{ 
			$message = $validator->errors()->first();
		    return response()->json(['statusCode'=>400,'success'=>false,'message'=>$message], 400);            
		}

		$user = Auth::User();
		$setStockprice = Price::where('stock_id',$request->stock_id)->where('user_id',$user->id)->first();
		if( !$setStockprice )
		{
			$setStockprice = new Price();
		}

		$setStockprice->user_id = $user->id;
		$setStockprice->stock_id = $request->stock_id;
		$setStockprice->price = $request->price;
		if( $setStockprice->save() ) 
		{
			return response()->json(['statusCode'=>200,'success'=>true,'message'=>'Stock Price Added.'], 200);
		}
		else 
		{
			return response()->json(['statusCode'=>501,'success'=>false,'message'=>'Oops! Something Went Wrong!'], 501);
		}
	}

	//This function will return the list of user stock with product.
	public function getglobalStockList()
	{
		$user = Auth::User();
		$getglobalStockproduct = Stock::with('product')->where('product_source','main')->where('user_id',$user->id)->orderBy('created_at','desc')->get();
		$getglobalStockproducttemp = Stock::with('tempProduct')->where('product_source','temp')->where('user_id',$user->id)->orderBy('created_at','desc')->get();
		if( count($getglobalStockproduct) > 0 || count($getglobalStockproducttemp) > 0 ) 
		{
			$data['main'] = $getglobalStockproduct;
			$data['temp'] = $getglobalStockproducttemp;
			return response()->json(['statusCode'=>200,'success'=>true,'message'=>'Global Stock List.','data'=>$data], 200);
		}
		else 
		{
			return response()->json(['statusCode'=>203,'success'=>false,'message'=>'Stock Not Found'], 203);
		}
	}

	//This function will return the list of user stock and price.
	public function getstocklist()
	{
		$user = Auth::User();
		$getStocklistproduct = Stock::with('product')->where('product_source','main')->where('user_id',$user->id)->orderBy('created_at','desc')->withCasts(['created_at'=>'datetime:d M, Y h:i a'])->get();
		$getStocklistproducttemp = Stock::with('tempProduct')->where('product_source','temp')->where('user_id',$user->id)->orderBy('created_at','desc')->withCasts(['created_at'=>'datetime:d M, Y h:i a'])->get();
		if( count($getStocklistproduct) > 0 || count($getStocklistproducttemp) > 0 ) 
		{
			$data['main'] = $getStocklistproduct;
			$data['temp'] = $getStocklistproducttemp;
			return response()->json(['statusCode'=>200,'success'=>true,'message'=>'Stock List.','data'=>$data], 200);
		}
		else 
		{
			return response()->json(['statusCode'=>203,'success'=>false,'message'=>'Stock Not Found'], 203);
		}		
	}	
}
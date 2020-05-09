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
		$setStockprice = Stock::with('product')->where('user_id',$user->id)->get();
		if( count($setStockprice) > 0 ) 
		{
			return response()->json(['statusCode'=>200,'success'=>true,'message'=>'Global Stock List.','data'=>$setStockprice], 200);
		}
		else 
		{
			return response()->json(['statusCode'=>203,'success'=>false,'message'=>'Stock Not Found'], 203);
		}
	}

	//This function will return the list of user stock and price.
	public function getstocklist()
	{
		dd("Stock List");
	}	
}

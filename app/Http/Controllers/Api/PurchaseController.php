<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Validator;
use Carbon\Carbon;
use App\Models\Purchase;
use App\Models\Stock;


class PurchaseController extends Controller
{
	//Function to add purchases of user
    public function addPurchase(Request $request)
    {
    	$validator = Validator::make($request->all(), [ 
            'price' => 'required|numeric',
            'product_id' => 'required|integer',
            'product_source' => 'required',
            'quantity'=>'required' 
        ]);

        if ($validator->fails())
		{ 
			$message = $validator->errors()->first();
		    return response()->json(['statusCode'=>400,'success'=>false,'message'=>$message], 400);            
		}
		$user = Auth::User();
		$purchase = new Purchase();
		$purchase->user_id = $user->id;
		$purchase->price = $request->price;
		$purchase->product_id = $request->product_id;
		$purchase->product_source = $request->product_source;
		$purchase->quantity = $request->quantity;

		if( $purchase->save() ) 
		{
			//Add Product in Stock
			if( $request->product_source =="main") 
			{
				$product_stock = Stock::where('product_id',$request->product_id)->where('product_source','main')->where('user_id',$user->id)->first();
			}
			else
			{
				$product_stock = Stock::where('product_id',$request->product_id)->where('product_source','temp')->where('user_id',$user->id)->first();
			}
			if( !$product_stock ) 
			{
				$product_stock = new Stock();
			}
			
			$product_stock->user_id = $user->id;
			$product_stock->product_id = $request->product_id;
			$product_stock->quantity = $product_stock->quantity + $request->quantity;
			$product_stock->product_source = $request->product_source;
			$product_stock->last_purchased_at = Carbon::now();
			$product_stock->save();

			return response()->json(['statusCode'=>200,'success'=>true,'message'=>'Purchased Added Succesfully.'], 200);
		}
		else 
		{
			return response()->json(['statusCode'=>501,'success'=>false,'message'=>'Oops! Something Went Wrong!'], 501);
		}
    }

    //Function to get list of purchased products of a particular user
    public function purchasedList()
    {
    	$user = Auth::User();
    	$getpurchaselistproduct = Purchase::with('product')->where('product_source','main')->where('user_id',$user->id)->orderBy('created_at','desc')->withCasts(['created_at'=>'datetime:d M, Y h:i a'])->get();
    	$getpurchaselistproducttemp = Purchase::with('productTemp')->where('product_source','temp')->where('user_id',$user->id)->orderBy('created_at','desc')->withCasts(['created_at'=>'datetime:d M, Y h:i a'])->get();

    	if( count($getpurchaselistproducttemp) > 0 || count($getpurchaselistproduct) > 0 ) 
    	{
    		$data['main'] = $getpurchaselistproduct;
    		$data['temp'] = $getpurchaselistproducttemp;
    		return response()->json(['statusCode'=>200,'success'=>true,'message'=>'Purchased List.','data'=>$data], 200);
    	}
    	else 
    	{
    		return response()->json(['statusCode'=>402,'success'=>false,'message'=>'No Purchased List Found.'], 402);
    	}
    }
}
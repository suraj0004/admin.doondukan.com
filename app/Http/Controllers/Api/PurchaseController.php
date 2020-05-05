<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Validator;
use App\Models\Purchase;

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
		    return response()->json(['statusCode'=>401,'success'=>false,'message'=>$message], 401);            
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
			return response()->json(['statusCode'=>200,'success'=>true,'message'=>'Purchased Added Succesfully.'], 200);
		}
		else 
		{
			return response()->json(['statusCode'=>501,'success'=>true,'message'=>'Oops! Something Went Wrong!'], 501);
		}
    }

    //Function to get list of purchased products of a particular user
    public function purchasedList()
    {
    	$user = Auth::User();
    	$getpurchaselist = Purchase::with('products')->where('user_id',$user->id)->get();

    	if( $getpurchaselist && count($getpurchaselist) > 0 ) 
    	{
    		return response()->json(['statusCode'=>200,'success'=>true,'message'=>'Purchased List.','data'=>$getpurchaselist], 200);
    	}
    	else 
    	{
    		return response()->json(['statusCode'=>200,'success'=>true,'message'=>'No Purchased List Found.'], 200);
    	}
    }
}

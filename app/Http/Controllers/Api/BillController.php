<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Bill;
use App\Models\Stock;
use App\Models\Sale;
use Validator;

class BillController extends Controller
{
    //This Function is used to generate bill for customer.
    public function generateBill(Request $request)
    {
    	$user = Auth::User();
    	$stock_details = [];
    	foreach ($request->sale as $key => $value) 
    	{
    		$checkStock = Stock::where('id',$value['stock_id'])->where('quantity','>',$value['quantity'])->where('user_id',$user->id)->first();
	    	if (!$checkStock)
			{ 
				$message = "Quantity or Stock is unavailable.";
			    return response()->json(['statusCode'=>400,'success'=>false,'message'=>$message], 400);            
			}
			$stock_details[] = $checkStock;
    	}

    	$setCustomerbill = new Bill();
    	$setCustomerbill->user_id = $user->id;
    	$setCustomerbill->customer_name = $request->buyer['name'] ?? null;
    	$setCustomerbill->customer_mobile = $request->buyer['mobile'] ?? null;
    	$setCustomerbill->customer_email = $request->buyer['email'] ?? null;
    	$setCustomerbill->discount = $request->buyer['discount'] ?? null;
    	$setCustomerbill->discount_type = $request->buyer['discount_type'] ?? null;
    	$bill_ID = $setCustomerbill->save();
    	if( $bill_ID ) 
    	{
    		foreach ($stock_details as $key => $stockvalue) 
    		{
    			$stockvalue->quantity = $stockvalue->quantity - $request->sale[$key]['quantity'];
    			if( $stockvalue->save() ) 
    			{
    				$setSale = new Sale();
	    			$setSale->user_id = $user->id;
	    			$setSale->product_id = $stockvalue->product_id;
	    			$setSale->bill_id = $setCustomerbill->id;
	    			$setSale->quantity = $request->sale[$key]['quantity'];
	    			$setSale->price = $stockvalue->price;
	    			$setSale->product_source = $stockvalue->product_source;
	    			if( !$setSale->save() ) 
	    			{
	    				return response()->json(['statusCode'=>501,'success'=>false,'message'=>'Oops! Something Went Wrong!'], 501);
	    			}
    			}
    			else 
    			{
    				return response()->json(['statusCode'=>501,'success'=>false,'message'=>'Oops! Something Went Wrong!'], 501);
    			}
    		}
    		return response()->json(['statusCode'=>200,'success'=>true,'message'=>'Bill Generated Successfully.','data'=>$setCustomerbill->id], 200);
    	}
    	else 
    	{
    		return response()->json(['statusCode'=>501,'success'=>false,'message'=>'Oops! Something Went Wrong!'], 501);
    	}
    }

    //This function is used to get the customer bill list
    public function getBill()
    {
        $user = Auth::User();
        $billData = Bill::where('user_id',$user->id)->paginate(10);

        if(count($billData) > 0 ) 
        {
            return response()->json(['statusCode'=>200,'success'=>true,'message'=>'Bill list.','data'=>$billData], 200);
        }
    }
}
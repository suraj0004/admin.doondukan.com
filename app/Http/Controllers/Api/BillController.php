<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Bill;
use App\Models\Stock;
use App\Models\Sale;
use App\Models\Store;
use App\Models\Purchase;
use App\Models\SaleReturn;
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
    		$checkStock = Stock::where('id',$value['stock_id'])->where('quantity','>=',$value['quantity'])->where('user_id',$user->id)->first();
	    	if (!$checkStock)
			{
				$message = "Quantity or Stock is unavailable.";
			    return response()->json(['statusCode'=>200,'success'=>false,'message'=>$message], 200);
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
                    //Get the last purchase price.
                    $purchase_price = Purchase::select('id','price')->where('product_id',$stockvalue->product_id)->orderBy('id','desc')->limit(1)->first();

    				$setSale = new Sale();
	    			$setSale->user_id = $user->id;
	    			$setSale->product_id = $stockvalue->product_id;
	    			$setSale->bill_id = $setCustomerbill->id;
	    			$setSale->quantity = $request->sale[$key]['quantity'];
	    			$setSale->price = $stockvalue->price;
	    			$setSale->product_source = $stockvalue->product_source;
                    $setSale->purchase_price = $purchase_price->price;
	    			if( !$setSale->save() )
	    			{
	    				return response()->json(['statusCode'=>200,'success'=>false,'message'=>'Oops! Something Went Wrong!'], 200);
	    			}
    			}
    			else
    			{
    				return response()->json(['statusCode'=>200,'success'=>false,'message'=>'Oops! Something Went Wrong!'], 200);
    			}
    		}
    		return response()->json(['statusCode'=>200,'success'=>true,'message'=>'Bill Generated Successfully.','data'=>$setCustomerbill->id], 200);
    	}
    	else
    	{
    		return response()->json(['statusCode'=>200,'success'=>false,'message'=>'Oops! Something Went Wrong!'], 200);
    	}
    }

    //This function is used to get the customer bill list
    public function getBill(Request $request)
    {
        $user = Auth::User();
        $billData = Bill::withCount('sales')->withSum('sales:price*quantity as sales_price')->where('user_id',$user->id);

        if(!empty( $request->status ) )
        {
            $billData = $billData->where('status',$request->status);
        }

        if( !empty($request->search) )
        {
            $billData = $billData->where('id','like','%'.$request->search.'%');
        }

        $billData = $billData->paginate(8);

        if(count($billData) > 0 )
        {
            return response()->json(['statusCode'=>200,'success'=>true,'message'=>'Bill list.','data'=>$billData], 200);
        }
        else
        {
          return response()->json(['statusCode'=>200,'success'=>false,'message'=>'Bill list not found.'], 200);
        }
    }

    //This function is used to create invoice for shop customer
    // $id = contains bill id.
    public function invoice($id)
    {
        $user = Auth::User();
        $data = Bill::with(['mainSaleProduct'=>function($query){
            $query->withSum('returns:quantity as return_quantity')->withTrashed();
        },'tempSaleProduct'=>function($query){
            $query->withSum('returns:quantity as return_quantity')->withTrashed();
        },'mainSaleReturnProduct','tempSaleReturnProduct'])->where('id',$id)->where('user_id',$user->id)->first();


        if( $data )
        {
            $store = Store::where('user_id',$user->id)->first();
            $data->store = $store;
            return response()->json(['statusCode'=>200,'success'=>true,'message'=>'invoice data.','data'=>$data], 200);
        }
        else
        {
          return response()->json(['statusCode'=>200,'success'=>false,'message'=>'invoice not found.'], 200);
        }
    }



    //This function is used to set the status of bill.
    public function setStatusPaid(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'billId'=>'required|numeric'
        ]);

        if ($validator->fails())
        {
            $message = $validator->errors()->first();
            return response()->json(['statusCode'=>200,'success'=>false,'message'=>$message], 200);
        }
        $user = Auth::User();
        $data = Bill::where('id',$request->billId)->where('user_id',$user->id)->first();
        if( $data )
        {
            $data->status = "paid";
            if( $data->save() )
            {
               return response()->json(['statusCode'=>200,'success'=>true,'message'=>'Bill status updated'], 200);
            }
            else
            {
                return response()->json(['statusCode'=>200,'success'=>false,'message'=>'Oops! Something went wrong.' ], 200);
            }
        }
        else
        {
            return response()->json(['statusCode'=>200,'success'=>false,'message'=>'Bill not found.'], 200);
        }
    }
}

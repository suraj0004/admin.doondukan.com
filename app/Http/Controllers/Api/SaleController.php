<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\User;
use App\Models\Sale_Return;
use App\Models\Stock;
use Validator;

class SaleController extends Controller
{
	//This function is used to get the sales list of use.
    public function saleList(Request $request)
    {
    	$user = Auth::User(); 
    	$getsaleListProduct = Sale::with(['product','bill:id,status'])->where('user_id',$user->id)->where('product_source','main');
    	$getsaleListProductTemp = Sale::with(['tempProduct','bill:id,status'])->where('user_id',$user->id)->where('product_source','temp');
        if( !empty($request->status) ) 
        {
            $status = $request->status;
            $getsaleListProduct = $getsaleListProduct->whereHas('bill', function($query) use ($status) {
                $query->where('status',$status);
            });

            $getsaleListProductTemp = $getsaleListProductTemp->whereHas('bill', function($query) use ($status) {
                $query->where('status',$status);
            });
        }

        $getsaleListProduct = $getsaleListProduct->get();
        $getsaleListProductTemp = $getsaleListProductTemp->get();

    	if( count($getsaleListProduct) > 0 || count($getsaleListProductTemp) > 0 ) 
    	{
    		$data['main'] = $getsaleListProduct;
			$data['temp'] = $getsaleListProductTemp;
			return response()->json(['statusCode'=>200,'success'=>true,'message'=>'Sales List.','data'=>$data], 200);	
    	}
    	else 
    	{
    		return response()->json(['statusCode'=>200,'success'=>false,'message'=>'Sales Not Found'], 200);
    	}
    }

    //this function is used to handle sale return.
    public function saleReturn(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'bill_id' => 'required|numeric',
            'sale_id' => 'required|numeric',
            'price'=>'required|integer',
            'quantity'=>'required' 
        ]);

        if ($validator->fails())
        { 
            $message = $validator->errors()->first();
            return response()->json(['statusCode'=>200,'success'=>false,'message'=>$message], 200);            
        }

        $user =Auth::User();
        $getSale = Sale::where('id',$request->sale_id)->where('bill_id',$request->bill_id)->where('user_id',$user->id)->first();
        if( !$getSale ) 
        {
            return response()->json(['statusCode'=>200,'success'=>false,'message'=>'sale not found'], 200);
        }

        $getstock = Stock::where('product_id',$getSale->product_id)->where('user_id',$user->id)->first();
        if(!$getstock) 
        {
            return response()->json(['statusCode'=>200,'success'=>false,'message'=>'stock not found'], 200);   
        }

        $sale_return = new Sale_Return();
        $sale_return->user_id = $user->id;
        $sale_return->bill_id = $request->bill_id;
        $sale_return->sale_id = $request->sale_id;
        $sale_return->stock_id = $getstock->id;
        $sale_return->price = $request->price;
        $sale_return->quantity = $request->quantity;

        if( $sale_return->save() ) 
        {
            $getstock->quantity = $getstock->quantity + $request->quantity;
            $getstock->save();
            return response()->json(['statusCode'=>200,'success'=>true,'message'=>'sale return successfully.'], 200);
        }
        else 
        {
            return response()->json(['statusCode'=>200,'success'=>false,'message'=>'Oop! Something went wrong.'], 200);
        }
    }
}
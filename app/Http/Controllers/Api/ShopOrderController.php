<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Models\Orders;
use App\Models\OrderItem;
use App\Models\Bill;
use App\Models\Stock;
use App\Models\Purchase;
use App\Models\Sale;
use DB;

class ShopOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $shopkeeperId  = Auth::user()->id;
        $orderData     = \App\Models\Orders::with(['orderitem','seller', 'buyer'])->whereSellerId($shopkeeperId)->get();

        return response()->json(['statusCode'=>200,'success'=>true,'message'=>'All orders fetched successfully.','data'=>$orderData], 200);

        // $array = ['test'=>2, 'test2'=> 4];
        // $jsonArray  = json_encode($array);
        // return $jsonArray;
    }

    /**
     * updating status
     */
    public function updateStatus(Request $request)
    {
        // validation missing
        // Need to refactor
        $orderId  =  $request->id;
        $status   = $request->status;


       $orderProducts = OrderItem::select('stocks.id as stock_id','order_items.quantity')
        ->where('order_items.order_id',$orderId)
        ->join('stocks',function ($join){
            $join->on("order_items.product_id" , "=", "stocks.product_id")
            ->on("stocks.user_id","=",DB::raw(Auth::id()));
        })
        ->get();


        $user = Auth::User();
    	$stock_details = [];
    	foreach ($orderProducts as $key => $value)
    	{
    		$checkStock = Stock::where('id',$value['stock_id'])->where('quantity','>=',$value['quantity'])->where('user_id',$user->id)->first();
	    	if (!$checkStock && $status != 3)
			{
				$message = "Quantity or Stock is unavailable.";
			    return response()->json(['statusCode'=>200,'success'=>false,'message'=>$message], 200);
			}
			$stock_details[] = $checkStock;
    	}

        if($status != 2){
            Orders::whereId($orderId)->update(['status'=>$status]); // where user id missing
            return response()->json(['statusCode'=>200,'success'=>true,'message'=>'Order Updates successfully'], 200);
        }

    	$setCustomerbill = new Bill();
    	$setCustomerbill->user_id = $user->id;
    	$bill_ID = $setCustomerbill->save();
    	if( $bill_ID )
    	{
    		foreach ($stock_details as $key => $stockvalue)
    		{
    			$stockvalue->quantity = $stockvalue->quantity - $orderProducts[$key]['quantity'];
    			if( $stockvalue->save() )
    			{
                    //Get the last purchase price.
                    $purchase_price = Purchase::select('id','price')->where('product_id',$stockvalue->product_id)->orderBy('id','desc')->limit(1)->first();

    				$setSale = new Sale();
	    			$setSale->user_id = $user->id;
	    			$setSale->product_id = $stockvalue->product_id;
	    			$setSale->bill_id = $setCustomerbill->id;
	    			$setSale->quantity = $orderProducts[$key]['quantity'];
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

            Orders::whereId($orderId)->update(['status'=>$status]); // where user id missing

    		return response()->json(['statusCode'=>200,'success'=>true,'message'=>'Bill Generated Successfully.','data'=>$setCustomerbill->id], 200);
    	}
    	else
    	{
    		return response()->json(['statusCode'=>200,'success'=>false,'message'=>'Oops! Something Went Wrong!'], 200);
    	}

    }





    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

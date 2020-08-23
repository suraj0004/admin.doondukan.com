<?php

namespace App\Http\Controllers\Api;

/**Dependencies */
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use DB;
use Exception;

/**Requests */
use App\Http\Requests\Api\PurchaseReturnFormRequest;

/**Models */
use App\Models\PurchaseReturn;
use App\Models\Stock;

class PurchaseReturnController extends Controller
{
    /**
     * Display a list of available stock with last purchase history
     * 
     * @return \Illuminate\Http\Response
     */
    public function getStock()
    {
        $user = Auth::User();

		$getglobalStockproduct = Stock::with(['product','purchasePrice' => function($query){
            $query->where('product_source','main');
        }])
        ->where('product_source','main')
        ->where('user_id',$user->id)
        ->where('quantity','!=',0)
        ->latest()
        ->get();


		$getglobalStockproducttemp = Stock::with(['tempProduct','purchasePrice' => function($query){
            $query->where('product_source','temp');
        }])
        ->where('product_source','temp')
        ->where('user_id',$user->id)
        ->where('quantity','!=',0)
        ->latest()
        ->get();


		if( count($getglobalStockproduct) > 0 || count($getglobalStockproducttemp) > 0 ) 
		{
			$data['main'] = $getglobalStockproduct;
			$data['temp'] = $getglobalStockproducttemp;
			return response()->json(['statusCode'=>200,'success'=>true,'message'=>'Global Stock List.','data'=>$data], 200);
		}
		else 
		{
			return response()->json(['statusCode'=>200,'success'=>false,'message'=>'Stock Not Found'], 200);
		}
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        
        $main = PurchaseReturn::with('product')
        ->where('product_source','main')
        ->where('user_id',$user->id)
        ->get();

        $temp = PurchaseReturn::with('tempProduct')
        ->where('product_source','temp')
        ->where('user_id',$user->id)
        ->get();


        if(count($main) > 0 || count($temp) > 0 ){
            return response()->json([
                "success" => true,
                "message" => "Data Fetched Successfully",
                "data" => [
                    "main" => $main,
                    "temp" => $temp,
                ]
            ],200);
        }

        return response()->json([
            "success" => false,
            "message" => "No Record found",
        ],200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Api\PurchaseReturnFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PurchaseReturnFormRequest $request)
    {
        $user = Auth::user();
        $stock = Stock::find($request->stock_id);
        if(!$stock){
            return response()->json([
                "success" => false,
                "message" => "This stock does not exists."
            ],200);
        }

        if($request->quantity > $stock->quantity){
            return response()->json([
                "success" => false,
                "message" => "Return Quantity must be less than ".$stock->quantity,
            ],200);
        }


        DB::beginTransaction();
        try{

            $stock->quantity =  $stock->quantity - $request->quantity;
            $stock->save();

            $return = new PurchaseReturn();
            $return->user_id = $user->id;
            $return->stock_id = $stock->id;
            $return->product_id = $stock->product_id;
            $return->product_source = $stock->product_source;
            $return->price = $request->price;
            $return->quantity = $request->quantity;
            $return->save();
    
           
    
        }catch(Exception $e){
            DB::rollback();
            return response()->json([
                "success" => false,
                "message" => "Oops something went wrong.",
            ],200);
        }
        
        DB::commit();
        return response()->json([
            "success" => true,
            "message" => "Successfully Returned Purchase",
        ],200);
  
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

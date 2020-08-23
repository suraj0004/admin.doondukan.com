<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use DB;
use Exception;

/**Requests */
use App\Http\Requests\Api\SaleReturnFormRequest;

/**Models */
use App\Models\SaleReturn;
use App\Models\Bill;
use App\Models\Sale;
use App\Models\Stock;

class SaleReturnController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        $main = SaleReturn::with('product')
        ->where('product_source','main')
        ->where('user_id',$user->id)
        ->get();

        $temp = SaleReturn::with('tempProduct')
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
     * @param  SaleReturnFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SaleReturnFormRequest $request)
    {
        $user = Auth::user();

        $returns = $request->returns;
        $bill_id = $request->bill_id;

        $exists = Bill::where('id',$bill_id)->exists();

        if(!$exists){
            return response()->json([
                "success" => false,
                "message" => "Bill Does Not Exists."
            ],200);
        }

        if(count($returns) == 0){
           return response()->json([
               "success" => false,
               "message" => "Please select atleast one item."
           ],200);
        }

        $array = array();
        $updates = array();
        foreach($returns as $return){
            if( isset($return["sale_id"]) && isset($return["return_quantity"]) ){

                $sale = Sale::with('mainStock')
                ->select('id as sale_id','user_id','bill_id','price','quantity','product_id','product_source')
                ->where('id',$return["sale_id"])
                ->where('bill_id',$bill_id)
                ->where('product_source','main')
                ->where('quantity','>=',$return["return_quantity"])
                ->first();

                if(!$sale){
                    $sale = Sale::with('tempStock')
                    ->select('id as sale_id','user_id','bill_id','price','quantity','product_id','product_source')
                    ->where('id',$return["sale_id"])
                    ->where('bill_id',$bill_id)
                    ->where('product_source','temp')
                    ->where('quantity','>=',$return["return_quantity"])
                    ->first();

                    if(!$sale){
                        return response()->json([
                            "success" => false,
                            "message" => "Return Quantity must be less than Available Qunatity"
                        ],200);
                    }else{
                        $sale->stock = $sale->tempStock;
                        unset($sale->tempStock);
                    }

                }else{
                    $sale->stock = $sale->mainStock;
                    unset($sale->mainStock);
                }

                array_push($array,[
                    "user_id" => $user->id,
                    "bill_id" =>$sale->bill_id,
                    "sale_id" =>$sale->sale_id,
                    "product_id" =>$sale->product_id,
                    "product_source" =>$sale->product_source,
                    "price" =>$sale->price,
                    "quantity" => $return["return_quantity"],
                     "created_at" => Carbon::now(),
                     "updated_at" => Carbon::now(),
                ]);

                array_push($updates,[
                    "sale_id" =>$sale->sale_id,
                    "return_quantity" => $return["return_quantity"],
                    "stock_id" =>$sale->stock->id,
                ]);
            }
        }

        // return response()->json([
        //     "success" => false,
        //     "message" => "Sucessfully Return Item",
        //     "data" => $array
        // ],200);

        if(count($returns) != count($array)){
            return response()->json([
                "success" => false,
                "message" => "There is some error in your selected items.",
            ],200);
        }

        DB::beginTransaction();
        try{


        /** Mark old sales return as not latest, so the current return will automatically become latest ( 0=> previous, 1=> latest )  */
        SaleReturn::where('user_id',$user->id)
        ->where('bill_id',$bill_id)
        ->update([
            'latest' => 0
        ]);

       /**Store Sale return data */
        SaleReturn::insert($array);

        foreach($updates as $update){
            $sale = Sale::select('id','quantity')
            ->where('user_id',$user->id)
            ->where('bill_id',$bill_id)
            ->where('id',$update["sale_id"])
            ->first();

            /**Update sales table */
            if($sale->quantity == $update["return_quantity"]){
                $sale->delete();
            }else{
                $sale->quantity = $sale->quantity - $update["return_quantity"];
                $sale->save();
            }

            /**Update stocks table */
            $stock = Stock::select('id','quantity')
            ->where('id', $update["stock_id"])
            ->where('user_id',$user->id)
            ->first();

            $stock->quantity = $stock->quantity + $update["return_quantity"];
            $stock->save();
        }


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
            "message" => "Sucessfully Return Item",
            "data" => $bill_id
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
        $user = Auth::User();
        $data = Bill::with(['mainSaleProduct','tempSaleProduct'])->where('id',$id)->where('user_id',$user->id)->where('status','paid')->first();
        if( $data )
        {
            return response()->json(['statusCode'=>200,'success'=>true,'message'=>'invoice data.','data'=>$data], 200);
        }
        else
        {
          return response()->json(['statusCode'=>200,'success'=>false,'message'=>'invoice not found.'], 200);
        }
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

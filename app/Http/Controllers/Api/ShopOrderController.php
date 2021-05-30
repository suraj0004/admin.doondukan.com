<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;


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
      
        $orderId  =  $request->id;
        // aprint($request->all());
        $orderData   =  \App\Models\Orders::whereId($orderId)->update(['status'=>config('constants.ORDERSTATUS.CONFIRM')]);

        $orderProducts = \App\Models\Orders::with(['orderitem'])->whereId($orderId)->first();

        
        // aprint($orderProducts->orderitem);
        // exit;


        foreach ($orderProducts->orderitem as $key => $value) {
            # code...

            $updateQuant = \App\Models\Stock::whereProductId($value->product_id)->whereUserId($orderProducts->seller_id)
                           ->decrement('quantity', $value->quantity);
        }


        return ($request->id);

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

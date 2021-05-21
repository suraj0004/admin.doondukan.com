<?php

namespace App\Http\Controllers\Api\Ecommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Orders;

class OrderController extends Controller
{
    public function confirmOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cart' => 'required|array',
            'seller_id'=>'required|integer'
        ]);

        if ($validator->fails())
		{
			$message = $validator->errors()->first();
		    return response()->json(['statusCode'=>200,'success'=>false,'message'=>$message], 200);
		}


    }
}

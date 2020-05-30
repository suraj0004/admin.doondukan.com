<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\User;

class SaleController extends Controller
{
	//This function is used to get the sales list of use.
    public function saleList(Request $request)
    {
    	$user = Auth::User(); 
    	$getsaleListProduct = Sale::with('product')->where('user_id',$user->id)->where('product_source','main');
    	$getsaleListProductTemp = Sale::with('tempProduct')->where('user_id',$user->id)->where('product_source','temp');
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
}
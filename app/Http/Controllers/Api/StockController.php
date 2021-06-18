<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Stock;
use Validator;
use App\Http\Resources\Shop\StockCollection;

class StockController extends Controller
{
    //Set Stock Price.This Function is used to add stock price.
	public function setStockprice(Request $request)
	{
		$validator = Validator::make($request->all(), [
            'stock_id' => 'required|integer|exists:stocks,id',
            'price' => 'required|numeric',
        ]);

        if ($validator->fails())
		{
			$message = $validator->errors()->first();
		    return response()->json(['statusCode'=>200,'success'=>false,'message'=>$message], 200);
		}

		$user = Auth::User();
		$setStockprice = Stock::where('id',$request->stock_id)->where('user_id',$user->id)->first();
		$setStockprice->user_id = $user->id;
		$setStockprice->price = $request->price;
		if( $setStockprice->save() )
		{
			return response()->json(['statusCode'=>200,'success'=>true,'message'=>'Stock Price Added.'], 200);
		}
		else
		{
			return response()->json(['statusCode'=>200,'success'=>false,'message'=>'Oops! Something Went Wrong!'], 200);
		}
	}

	//This function will return the list of user stock with product.
	public function getglobalStockList()
	{
		$user = Auth::User();
		$getglobalStockproduct = Stock::with('product','purchasePrice')->where('product_source','main')->where('user_id',$user->id)->orderBy('created_at','desc')->get();
		$getglobalStockproducttemp = Stock::with('tempProduct','purchasePrice')->where('product_source','temp')->where('user_id',$user->id)->orderBy('created_at','desc')->get();
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

	//This function will return the list of user stock and price.
	public function getstocklist(Request $request)
	{
		$user = Auth::User();
		$data = Stock::select('id','last_purchased_at','price','quantity','product_id')
        ->with('product:id,name,weight,weight_type,image')
        ->where('product_source','main')
        ->where('user_id',$user->id);

        if(isset($request->stockFilter)){
            if($request->stockFilter == 'in-stock'){
                $data = $data->where('quantity','>',0);
            }else if($request->stockFilter == 'out-of-stock'){
                $data = $data->where('quantity','=',0);
            }
        }

        if(isset($request->sortType)){
            if($request->sortType == 'qty-low-to-high'){
                $data = $data->orderBy('quantity','asc');
            }else if($request->sortType == 'qty-high-to-tow'){
                $data = $data->orderBy('quantity','desc');
            }else if($request->sortType == 'price-low-to-high'){
                $data = $data->orderBy('price','asc');
            }else if($request->sortType == 'price-high-to-low'){
                $data = $data->orderBy('price','desc');
            }else{
                $data = $data->latest('updated_at');
            }
        }

        if(isset($request->search) && !empty($request->search) ){
            $data = $data->whereHas('product', function($query) use($request){
                $query->where('name','LIKE','%'.$request->search.'%');
            });
        }

        $data = $data->paginate(10);
        return new StockCollection($data);

	}

	//This function will return the list of user available stock with product.
	public function getAvailableGlobalStockList()
	{
		$user = Auth::User();
		$getavailableStocklistproduct = Stock::with('product.brand')->where('product_source','main')->where('user_id',$user->id)->where('quantity','!=',0)->where('price','!=',0)->orderBy('created_at','desc')->get();
		$getavailableStocklistproducttemp = Stock::with('tempProduct.brand')->where('product_source','temp')->where('user_id',$user->id)->where('quantity','!=',0)->where('price','!=',0)->orderBy('created_at','desc')->get();
		if( count($getavailableStocklistproduct) > 0 || count($getavailableStocklistproducttemp) > 0 )
		{
			$data['main'] = $getavailableStocklistproduct;
			$data['temp'] = $getavailableStocklistproducttemp;
			return response()->json(['statusCode'=>200,'success'=>true,'message'=>'Available Stock List.','data'=>$data], 200);
		}
		else
		{
			return response()->json(['statusCode'=>200,'success'=>false,'message'=>'Stock Not Found'], 200);
		}
	}
}

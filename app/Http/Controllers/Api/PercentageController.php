<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Stock;
use Carbon\Carbon;
use Exception;

class PercentageController extends Controller
{
    public function getPercentageAndMore()
    {
        $user = Auth::user();
        $yesterday =  Carbon::now()->subDays(1);
        $day_before_yesterday =  Carbon::now()->subDays(2);

        /** Purchase percentage deffirence in last 2 days */
        $yesterday_purchase = Purchase::where("user_id",$user->id)
        ->whereDate('created_at', $yesterday)
        ->selectRaw('sum(price*quantity) as purchase')
        ->first();

        $day_before_yesterday_purchase = Purchase::where("user_id",$user->id)
        ->whereDate('created_at', $day_before_yesterday)
        ->selectRaw('sum(price*quantity) as purchase')
        ->first();
    
        try {
            $difference = $yesterday_purchase->purchase - $day_before_yesterday_purchase->purchase;
            $percentage_change_in_purchase = ( $difference * 100 )/$day_before_yesterday_purchase->purchase;
        } catch (Exception $e) {
            /** Divisible by zero Exception */
            $percentage_change_in_purchase = 0;
        }

        /** Sales percentage deffirence in last 2 days */
        $yesterday_sale = Sale::where("user_id",$user->id)
        ->whereDate('created_at', $yesterday)
        ->selectRaw('sum(price*quantity) as sale')
        ->first();

        $day_before_yesterday_sale = Sale::where("user_id",$user->id)
        ->whereDate('created_at', $day_before_yesterday)
        ->selectRaw('sum(price*quantity) as sale')
        ->first();
    
        try {
            $difference = $yesterday_sale->sale - $day_before_yesterday_sale->sale;
            $percentage_change_in_sale = ( $difference * 100 )/$day_before_yesterday_sale->sale;
        } catch (Exception $e) {
            /** Divisible by zero Exception */
            $percentage_change_in_sale = 0;
        }


        /** Profit percentage deffirence in last 2 days */
        $yesterday_profit =  Sale::where("user_id",$user->id)
        ->whereDate('created_at', $yesterday)
        ->selectRaw('sum(  (price - purchase_price) * quantity  ) as profit')
        ->first();

        $day_before_yesterday_profit = Sale::where("user_id",$user->id)
        ->whereDate('created_at', $day_before_yesterday)
        ->selectRaw('sum(  (price - purchase_price) * quantity  ) as profit')
        ->first();
    
        try {
            $difference = $yesterday_profit->profit - $day_before_yesterday_profit->profit;
            $percentage_change_in_profit = ( $difference * 100 )/$day_before_yesterday_profit->profit;
        } catch (Exception $e) {
            /** Divisible by zero Exception */
            $percentage_change_in_profit = 0;
        }

        if(  ($day_before_yesterday_profit->profit < 0 && $yesterday_profit->profit >= 0) || ($day_before_yesterday_profit->profit < 0 && $yesterday_profit->profit < 0 && $yesterday_profit->profit < $day_before_yesterday_profit->profit )  ){
            $percentage_change_in_profit *= (-1);
        }



        $today =  Carbon::now();

        $products = Purchase::where('user_id',$user->id)
        ->distinct('product_id')
        ->count('product_id');

        $stocks = Stock::where('user_id',$user->id)
        ->where('quantity','!=','0')
        ->distinct('product_id')
        ->count('product_id');

        $item_sold = Sale::where('user_id',$user->id)
        ->whereDate('created_at',$today)
        ->sum('quantity');

        $sales = Sale::where("user_id",$user->id)
        ->whereDate('created_at', $today)
        ->selectRaw('sum(price*quantity) as sale')
        ->first();

        $profit =  Sale::where("sales.user_id",$user->id)
        ->whereDate('sales.created_at', $today)
        ->selectRaw('sum(  (sales.price - purchases.price) * sales.quantity  ) as profit')
        ->join('purchases',function($join) use($user){
            $join->on('sales.product_id','=','purchases.product_id')
            ->where('purchases.user_id',$user->id);
        })
        ->first();

        return response()->json([
            "success" => true,
            "message" => "Data Fetched Successfullt",
            "percentage" => [
                "purchase" => round($percentage_change_in_purchase),
                "sale" => round($percentage_change_in_sale),
                "profit" => round($percentage_change_in_profit),
            ],
            "amount" =>[
                "purchase" => round($yesterday_purchase->purchase),
                "sale" => round($yesterday_sale->sale),
                "profit" => round($yesterday_profit->profit),
            ],
            'more' =>[
                "products" =>$products,
                "stocks" =>$stocks,
                "item_sold" =>$item_sold,
                "sales" => $sales->sale,
                "profit" => $profit->profit,
            ]
        ],200);

    }
}




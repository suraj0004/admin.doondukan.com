<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Sale;
use App\Models\Purchase;

use Carbon\Carbon;
class ComparisionController extends Controller
{
    public function getSaleVsProfit(Request $request)
    {
        $range = $request->range;

        if( !in_array($range,["month","year"])  ){
            return response()->json([
                "success" =>false,
                "message" => "Invalid Parameters"
            ],200);
        }

        $user = Auth::user();

        $year = "";
        if($range === "month"){

            $year = Carbon::now()->year;
            $salesAndProfit = Sale::selectRaw("SUM(price*quantity) as sale, SUM( (price - purchase_price)*quantity ) as profit, created_at, extract(month from created_at) as month")
            ->where('user_id',$user->id)
            ->whereYear('created_at',$year)
            ->orderBy("created_at","asc")
            ->groupBy("month")->withCasts(['created_at'=>'datetime:M Y'])
            ->get();

        }else{

            $salesAndProfit = Sale::selectRaw("SUM(price*quantity) as sale, SUM( (price - purchase_price)*quantity ) as profit, created_at, extract(year from created_at) as year")
            ->where('user_id',$user->id)
            ->orderBy("created_at","asc")
            ->groupBy("year")->withCasts(['created_at'=>'datetime:Y'])
            ->get();

        }
      
        return response()->json([
            "success" => true,
            "message" => "Data Fetched Successfullt",
            "data" => $salesAndProfit,
            "year" => $year,
        ],200);
    }


    public function getAll_inOne(Request $request)
    {
        $range = $request->range;

        if( !in_array($range,["month","year"])  ){
            return response()->json([
                "success" =>false,
                "message" => "Invalid Parameters"
            ],200);
        }

        $user = Auth::user();

        $year = "";
        if($range === "month"){

            $year = Carbon::now()->year;
            $salesAndProfit = Sale::selectRaw("SUM(price*quantity) as sale, SUM( (price - purchase_price)*quantity ) as profit,SUM(purchase_price*quantity) as cost, created_at, extract(month from created_at) as month")
            ->where('user_id',$user->id)
            ->whereYear('created_at',$year)
            ->orderBy("created_at","asc")
            ->groupBy("month")->withCasts(['created_at'=>'datetime:M Y'])
            ->get();

        }else{

            $salesAndProfit = Sale::selectRaw("SUM(price*quantity) as sale, SUM( (price - purchase_price)*quantity ) as profit, SUM(purchase_price*quantity) as cost, created_at, extract(year from created_at) as year")
            ->where('user_id',$user->id)
            ->orderBy("created_at","asc")
            ->groupBy("year")->withCasts(['created_at'=>'datetime:Y'])
            ->get();


        }
      
        return response()->json([
            "success" => true,
            "message" => "Data Fetched Successfullt",
            "data" => $salesAndProfit,
            "year" => $year,
        ],200);
    }

    public function getQuantityVsSale(Request $request)
    {
        $range = $request->range;

        if( !in_array($range,["month","year"])  ){
            return response()->json([
                "success" =>false,
                "message" => "Invalid Parameters"
            ],200);
        }

        $user = Auth::user();

        $year = "";
        if($range === "month"){

            $year = Carbon::now()->year;
            $quantityAndSale = Sale::selectRaw("SUM(price*quantity) as sale, SUM(quantity) as item, created_at, extract(month from created_at) as month")
            ->where('user_id',$user->id)
            ->whereYear('created_at',$year)
            ->orderBy("created_at","asc")
            ->groupBy("month")->withCasts(['created_at'=>'datetime:M Y'])
            ->get();

        }else{

            $quantityAndSale = Sale::selectRaw("SUM(price*quantity) as sale, SUM(quantity) as item, created_at, extract(year from created_at) as year")
            ->where('user_id',$user->id)
            ->orderBy("created_at","asc")
            ->groupBy("year")->withCasts(['created_at'=>'datetime:Y'])
            ->get();

        }
      
        return response()->json([
            "success" => true,
            "message" => "Data Fetched Successfullt",
            "data" => $quantityAndSale,
            "year" => $year,
        ],200);
    }

    public function getQuantityVsProfit(Request $request)
    {
        $range = $request->range;

        if( !in_array($range,["month","year"])  ){
            return response()->json([
                "success" =>false,
                "message" => "Invalid Parameters"
            ],200);
        }

        $user = Auth::user();

        $year = "";
        if($range === "month"){

            $year = Carbon::now()->year;
            $quantityAndProfit = Sale::selectRaw("SUM( (price - purchase_price)*quantity ) as profit, SUM(quantity) as item, created_at, extract(month from created_at) as month")
            ->where('user_id',$user->id)
            ->whereYear('created_at',$year)
            ->orderBy("created_at","asc")
            ->groupBy("month")->withCasts(['created_at'=>'datetime:M Y'])
            ->get();

        }else{

            $quantityAndProfit = Sale::selectRaw("SUM( (price - purchase_price)*quantity ) as profit, SUM(quantity) as item, created_at, extract(year from created_at) as year")
            ->where('user_id',$user->id)
            ->orderBy("created_at","asc")
            ->groupBy("year")->withCasts(['created_at'=>'datetime:Y'])
            ->get();

        }
      
        return response()->json([
            "success" => true,
            "message" => "Data Fetched Successfullt",
            "data" => $quantityAndProfit,
            "year" => $year,
        ],200);
    }
}

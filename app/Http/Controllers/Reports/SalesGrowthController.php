<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Sale;

use Carbon\Carbon;
class SalesGrowthController extends Controller
{

    public function __invoke(Request $request)
    {
       $type = $request->type;
       $range = $request->range;

       if(!in_array($type,["rupees","quantity"]) || !in_array($range,["week","month","months","year"]) ){

        return response()->json([
            "success" =>false,
            "message" => "Invalid Parameters"
        ],200);

       }

       $user = Auth::user();
       $carbon = Carbon::now();

       if($range === "week"){

           $range = $carbon->subWeek();
           $group_by_column = "day";
           $dateCaste = "datetime:d M, Y";

       }else if($range === "month"){

           $range = $carbon->subMonth();
           $group_by_column = "day";
           $dateCaste = "datetime:d M, Y";

       }else{

       
        if($range === "months"){
            $group_by_column = "month";
            $dateCaste = "datetime:M Y";
        }else{
            $group_by_column = "year";
            $dateCaste = "datetime:Y";
        }
        $range = ($user->created_at);
     

       }


        if($type === "quantity"){

            $data = Sale::selectRaw(" SUM(quantity) as data, created_at, extract(".$group_by_column." from created_at) as group_by_column ");
 
         }else{

             $data = Sale::selectRaw(" SUM(quantity*price) as data, created_at, extract(".$group_by_column." from created_at) as group_by_column ");
         }

       $data =  $data->where('user_id',$user->id)
       ->whereDate('created_at','>=',$range)
       ->whereDate('created_at','<=',Carbon::now())
       ->orderBy("created_at","asc")
       ->groupBy("group_by_column")
       ->withCasts(['created_at'=>$dateCaste])
       ->get();

     
       return response()->json([
           "success" => true,
           "message" => "Data fetched successfully",
           "data" => $data,
           "from" => Carbon::now(),
           "to" => $range,
       ],200);
    
    }

   
}

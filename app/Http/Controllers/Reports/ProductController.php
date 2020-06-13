<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Sale;
use App\Models\User;

use Carbon\Carbon;
class ProductController extends Controller
{
    public function getTopHighestSellingProducts(Request $request)
    {
        $top = (int)$request->top;
        $range = $request->range;

        if( !in_array($top,[20,30,40,50]) || !in_array($range,["week","month","months","now"])  ){
            return response()->json([
                "success" =>false,
                "message" => "Invalid Parameters"
            ],200);
        }

        $user = Auth::user();

        $carbon  = Carbon::now();
        switch ($range) {
            case 'week':
                $range = $carbon->subWeek();
                break;
                case 'month':
                    $range = $carbon->subMonth();
                    break;
                    case 'months':
                        $range = $carbon->subMonths(6);
                        break;
            default:
            $range = ($user->created_at);
                break;
        }

  
        $sales = Sale::with('product')
        ->select('product_id','price')
        ->selectRaw('sum(quantity) as qty')
        ->where('user_id',$user->id)
        ->where('created_at','>=',$range)
        ->groupBy('product_id')
        ->orderBy('qty','desc')
        ->limit($top)
        ->get();

        
        

        return response()->json([
            "success" => true,
            "message" => "Data Fetched Successfully.",
            "data" => $sales,
            "from" => $range,
            "to" => Carbon::now(),
        ],200);
    }
}

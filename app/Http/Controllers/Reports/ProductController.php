<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Sale;
use App\Models\User;
use App\Models\Stock;

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

  
        $sales = Sale::with('product','stock:quantity,product_id')
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


    public function getTopLowestSellingProducts(Request $request)
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

        $sales = Stock::with('product')
        ->select('stocks.product_id','stocks.quantity as available')
        ->leftJoin('sales',function($join) use($user,$range) {
            $join->on('stocks.product_id','=','sales.product_id')
            ->where('sales.user_id',$user->id)
            ->where('sales.created_at','>=',$range);
        })
        ->selectRaw('sum(sales.quantity) as qty')
        ->where('stocks.user_id',$user->id)
        ->groupBy('stocks.product_id')
        ->orderBy('qty','asc')
        ->limit($top)
        ->get();

        
        

        return response()->json([
            "success" => true,
            "message" => "Data Fetched Successfullt",
            "data" => $sales,
            "from" => $range,
            "to" => Carbon::now(),
        ],200);

    }


    public function getTopProfitableProducts(Request $request)
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

  
        $profit = Sale::with('product','stock:quantity,product_id')
        ->select('product_id')
        ->selectRaw('sum(quantity) as qty, SUM( ( (price - purchase_price)*quantity ) ) as profit, (price - purchase_price) as margin ')
        ->where('user_id',$user->id)
        ->where('created_at','>=',$range)
        ->having('profit','>','0')
        ->groupBy('product_id')
        ->orderBy('profit','desc')
        ->limit($top)
        ->get();

        
        

        return response()->json([
            "success" => true,
            "message" => "Data Fetched Successfullt",
            "data" => $profit,
            "from" => $range,
            "to" => Carbon::now(),
        ],200);


    }


    public function getTopLessProfitableProducts(Request $request)
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

  
        $profit = Sale::with('product','stock:quantity,product_id')
        ->select('product_id')
        ->selectRaw('sum(quantity) as qty, SUM( ( (price - purchase_price)*quantity ) ) as profit, (price - purchase_price) as margin ')
        ->where('user_id',$user->id)
        ->where('created_at','>=',$range)
        ->having('profit','>','0')
        ->groupBy('product_id')
        ->orderBy('profit','asc')
        ->limit($top)
        ->get();

        return response()->json([
            "success" => true,
            "message" => "Data Fetched Successfullt",
            "data" => $profit,
            "from" => $range,
            "to" => Carbon::now(),
        ],200);

    }


    
    public function getLooselyProducts(Request $request)
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

        $loss = Sale::with('product')
        ->rightJoin('stocks',function($join) use($user) {
            $join->on('sales.product_id','=','stocks.product_id')
            ->where('stocks.user_id',$user->id);    
        })
        ->select('stocks.product_id','stocks.quantity as available','sales.purchase_price','sales.price')
        ->selectRaw('sum(sales.quantity) as qty, SUM( ( (sales.price - sales.purchase_price) * sales.quantity ) ) as loss, (sales.price - sales.purchase_price) as margin ')
        ->where(function($query) use($user){
            $query->where('sales.user_id',$user->id)
            ->orWhereNull('sales.user_id');
        })
        ->where(function($query) use($range){
            $query->where('sales.created_at','>=',$range)
            ->orWhereNull('sales.created_at');
        })
        
        ->havingRaw('loss <= 0 or loss is NULL')
        ->groupBy('stocks.product_id')
        ->orderBy('loss','desc')
        ->limit($top)
        ->get();
        
        

        return response()->json([
            "success" => true,
            "message" => "Data Fetched Successfullt",
            "data" => $loss,
            "from" => $range,
            "to" => Carbon::now(),
        ],200);

    }


}

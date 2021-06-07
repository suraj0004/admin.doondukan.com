<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Auth;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Bill;
use QrCode;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        /** Today Sale & Profit By every hour, like sales between 4:00 pm to 5:00 pm is 500 Rs */
        $todaySaleAndProfit = Sale::where('user_id',$user->id)
        ->selectRaw('SUM(quantity) as quantity, SUM(quantity*price) as sale, SUM(quantity*(price - purchase_price )) as profit, created_at, extract(HOUR from created_at) as hour')
        ->whereDate('created_at',today())
        ->orderBy("created_at","asc")
        ->groupBy("hour")
        ->get();

         /** Today Purchase By every hour, like purchase between 4:00 pm to 5:00 pm is 500 Rs */
         $todayPurchase = Purchase::where('user_id',$user->id)
         ->selectRaw('SUM(quantity) as quantity, SUM(quantity*price) as purchase, created_at, extract(HOUR from created_at) as hour')
         ->whereDate('created_at',today())
         ->orderBy("created_at","asc")
         ->groupBy("hour")
         ->get();

         /** Latest ten bills of today */
         $todayRecentBills = Bill::select('id','status','created_at')
         ->withSum('sales:price*quantity as sales_price')
         ->whereDate('created_at',today())
         ->where('user_id',$user->id)
         ->orderByDesc('created_at')
         ->limit(10)
         ->get();

         $today = Sale::selectRaw("SUM( quantity ) as item_sold, SUM( price*quantity ) as sale, SUM( quantity * (price - purchase_price) ) as profit, SUM(quantity*purchase_price) as cost")
         ->whereDate('created_at',today())
         ->where('user_id',$user->id)
         ->first();

         $purchase = Purchase::selectRaw("SUM( price*quantity ) as purchase, SUM(quantity) as item_purchase")
         ->whereDate('created_at',today())
         ->where('user_id',$user->id)
         ->first();


        $store = $user->store;
        $link = "";
        $message = "";
        $qr_code = "";
        if($store){
            $link = __("message.share_shop.link",["seller_id" => $store->user_id, "shop_slug" => $store->slug]);
            $message = __("message.share_shop.message",["shop" => $store->name, "link" => $link, "mobile" => $store->mobile]);
            $qr_code = "data:image/png;base64,".base64_encode(QrCode::size(500)->format('png')->generate($link));
        }

        return response()->json([
            "success" => true,
            "message" => "Data fetched successfully",
            "sale_and_profit" => $todaySaleAndProfit,
            "purchase" => $todayPurchase,
            "bill" => $todayRecentBills,
            "today" => [
                "purchase" => ($purchase)?$purchase->purchase:0,
                "item_purchase" => ($purchase)?(int)$purchase->item_purchase:0,
                "item_sold" => ($today)?(int)$today->item_sold:0,
                "sale" => ($today)?$today->sale:0,
                "profit" => ($today)?$today->profit:0,
                "cost" => ($today)?$today->cost:0,
            ],
            "share" => [
                "link" => $link,
                "message" => $message,
                "qr_code" => $qr_code,
            ]
        ],200);
    }
}

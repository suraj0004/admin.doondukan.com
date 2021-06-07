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
            // $qr_code = base64_encode(QrCode::size(500)->format('png')->generate($link));
            $qr_code = "data:image/png;base64,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";
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

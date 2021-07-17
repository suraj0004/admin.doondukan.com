<?php

namespace App\Http\Controllers\Api;

use App\Events\OrderConfirmed;
use App\Http\Controllers\Controller;
use App\Http\Resources\Shop\OrderListCollection;
use App\Http\Resources\Shop\ProductResource;
use App\Models\Bill;
use App\Models\OrderItem;
use App\Models\Orders;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Stock;
use Auth;
use DB;
use Illuminate\Http\Request;

class ShopOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $shopkeeperId = Auth::user()->id;
        $orderData = Orders::select("orders.*", "products.image")
            ->with(['buyer'])
            ->withCount('orderitem')
            ->whereSellerId($shopkeeperId)
            ->join('order_items', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->groupBy('orders.id');
        $status = [0, 1, 2, 3];
        if (isset($request->status) && in_array($request->status, $status)) {
            $orderData = $orderData->where('orders.status', $request->status);
        }

        if (!empty($request->search)) {
            $orderData = $orderData->where('orders.order_no', 'like', '%' . $request->search . '%');
        }

        $orderData = $orderData->latest('orders.from_time')->paginate(5);
        return (new OrderListCollection($orderData))->additional([
            "message" => "Orders get successfully.",
        ]);
        return response()->json(['statusCode' => 200, 'success' => true, 'message' => 'All orders fetched successfully.', 'data' => $orderData], 200);
    }

    public function getOrderDetail($id)
    {
        $user = Auth::User();
        $data = Orders::with(["orderitem.product", "buyer"])->where('id', $id)->where('seller_id', $user->id)->first();

        if ($data) {
            $data->orderitem = $data->orderitem->map(function ($orderItem) {
                $product = new ProductResource($orderItem->product);
                unset($orderItem->product);
                $orderItem->product = $product;
                return $orderItem;
            });
            return response()->json(['statusCode' => 200, 'success' => true, 'message' => 'invoice data.', 'data' => $data], 200);
        } else {
            return response()->json(['statusCode' => 200, 'success' => false, 'message' => 'invoice not found.'], 200);
        }
    }

    /**
     * updating status
     */
    public function updateStatus(Request $request)
    {
        // validation missing
        // Need to refactor
        $orderId = $request->id;
        $status = $request->status;

        $orderProducts = OrderItem::select('stocks.id as stock_id', 'order_items.quantity')
            ->where('order_items.order_id', $orderId)
            ->join('stocks', function ($join) {
                $join->on("order_items.product_id", "=", "stocks.product_id")
                    ->on("stocks.user_id", "=", DB::raw(Auth::id()));
            })
            ->get();

        $user = Auth::User();
        $stock_details = [];
        foreach ($orderProducts as $key => $value) {
            $checkStock = Stock::where('id', $value['stock_id'])->where('quantity', '>=', $value['quantity'])->where('user_id', $user->id)->first();
            if (!$checkStock && $status != 3) {
                $message = "Quantity or Stock is unavailable.";
                return response()->json(['statusCode' => 200, 'success' => false, 'message' => $message], 200);
            }
            $stock_details[] = $checkStock;
        }

        if ($status != 2) {
            Orders::whereId($orderId)->update(['status' => $status]); // where user id missing

            if ($status == 1) {
                $order = Orders::find($orderId);
                OrderConfirmed::dispatch($order);
            }
            return response()->json(['statusCode' => 200, 'success' => true, 'message' => 'Order Updates successfully'], 200);
        }

        $setCustomerbill = new Bill();
        $setCustomerbill->user_id = $user->id;
        $bill_ID = $setCustomerbill->save();
        if ($bill_ID) {
            foreach ($stock_details as $key => $stockvalue) {
                $stockvalue->quantity = $stockvalue->quantity - $orderProducts[$key]['quantity'];
                if ($stockvalue->save()) {
                    //Get the last purchase price.
                    $purchase_price = Purchase::select('id', 'price')->where('product_id', $stockvalue->product_id)->orderBy('id', 'desc')->first();

                    $setSale = new Sale();
                    $setSale->user_id = $user->id;
                    $setSale->product_id = $stockvalue->product_id;
                    $setSale->bill_id = $setCustomerbill->id;
                    $setSale->quantity = $orderProducts[$key]['quantity'];
                    $setSale->price = $stockvalue->price;
                    $setSale->product_source = $stockvalue->product_source;
                    $setSale->purchase_price = $purchase_price->price;
                    if (!$setSale->save()) {
                        return response()->json(['statusCode' => 200, 'success' => false, 'message' => 'Oops! Something Went Wrong!'], 200);
                    }
                } else {
                    return response()->json(['statusCode' => 200, 'success' => false, 'message' => 'Oops! Something Went Wrong!'], 200);
                }
            }

            Orders::whereId($orderId)->update(['status' => $status, 'bill_id' => $setCustomerbill->id]); // where user id missing
            return response()->json(['statusCode' => 200, 'success' => true, 'message' => 'Bill Generated Successfully.', 'data' => $setCustomerbill->id], 200);
        } else {
            return response()->json(['statusCode' => 200, 'success' => false, 'message' => 'Oops! Something Went Wrong!'], 200);
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

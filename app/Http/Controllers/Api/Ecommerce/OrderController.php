<?php

namespace App\Http\Controllers\Api\Ecommerce;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Orders;
use App\Models\Cart;
use DB;
use App\Models\OrderItem;
use Validator;
use App\Http\Resources\Ecommerce\OrderCollection;
use App\Http\Resources\Ecommerce\OrderResource;
use App\Http\Resources\Ecommerce\OrderItemCollection;


class OrderController extends Controller
{
    public function checkout(Request $request, $seller_id, $shop_slug)
    {
        $validator = Validator::make($request->all(), [
            'fromDate' => 'required|date_format:Y-m-d H:i:s',
            'toDate' => 'required|date_format:Y-m-d H:i:s|after:fromDate'
         ]);

        if ($validator->fails()) {
            $message = $validator->errors()->first();
            return response()->json(['statusCode'=>200,'success'=>false,'message'=>$message], 200);
        }

        $buyer = Auth::User();

        $cartData = Cart::select('carts.*','stocks.price')
                    ->join('stocks', function ($join) use ($seller_id) {
                        $join->on('stocks.product_id', '=', 'carts.product_id')
                        ->on('stocks.user_id', '=', DB::raw($seller_id));
                    })->where('buyer_id',$buyer->id)
                    ->where('seller_id',$seller_id)->get();

        if($cartData->isEmpty()) {
            return response()->json(['statusCode' => 200, 'success' => false, 'message' => "Oops! Cart Is Empty."], 200);
        }

        $orderData = new Orders();
        $orderData->buyer_id = $buyer->id;
        $orderData->seller_id = $cartData->first()->seller_id;
        $orderData->order_no = $this->getOrderNumber();
        $orderData->order_amount = $cartData->reduce(function ($carry,$item) { return $carry + ($item->price * $item->quantity); });
        $orderData->from_time = $request->fromDate;
        $orderData->to_time = $request->toDate;

        if(!$orderData->save()) {
            return response()->json(['statusCode' => 200, 'success' => false, 'message' => "Oops! Something went wrong. Please try again later."], 200);
        }
        foreach ($cartData as $key => $value) {
            $orderItemData = new OrderItem();
            $orderItemData->order_id = $orderData->id;
            $orderItemData->product_id = $value->product_id;
            $orderItemData->price = $value->price;
            $orderItemData->quantity = $value->quantity;
            $orderItemData->save();
        }
        $this->destroyCart($buyer,$seller_id);

        //Todo Sent message to seller

        return response()->json(['statusCode' => 200, 'success' => true, 'message' => "Order Placed Successfully."], 200);
    }

    private function destroyCart($buyer,$seller_id)
    {
        Cart::where('buyer_id',$buyer->id)->where('seller_id',$seller_id)->delete();
    }

    private function getOrderNumber()
    {
        $orderNumber = rand(12125460894,9923564785);
        $checkOrderNumber = Orders::where('order_no',$orderNumber)->first();
        if($checkOrderNumber) {
            $this->getOrderNumber();
        }
        return $orderNumber;
    }

    public function orderList()
    {
        $user = Auth::User();
        $data = Orders::select('order_no','order_amount','status','created_at','seller_id')
                    ->withCount("orderitem")
                    ->with("store")
                    ->where('buyer_id',$user->id)
                    ->get();

        if($data->isEmpty()) {
            return response()->json(['statusCode' => 200, 'success' => false, 'message' => "No order found."], 200);
        }

        return (new OrderCollection($data))->additional([
            "message" => "Order list Data",
        ]);

    }

    public function orderDetails($order_no)
    {
        $user = Auth::User();
        $data = Orders::select('id','seller_id','order_no','order_amount','status','created_at')
                ->with(['store','orderitem:id,order_id,product_id,quantity,price'])
                ->where('buyer_id',$user->id)
                ->where('order_no',$order_no)
                ->first();
        if(!$data) {
            return response()->json(['statusCode' => 200, 'success' => false, 'message' => "Order not found."], 200);
        }
        return (new OrderResource($data))->additional([
            "message" => "Order Detail Data",
            "data" => [
                "items" =>new OrderItemCollection($data->orderitem),
                "buyer" => $user
            ]
        ]);

    }
}

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
use App\Events\OrderPlaced;
use App\Models\User;
use App\Rules\IsShopOpen;
use PDF;

class OrderController extends Controller
{
    public function checkout(Request $request, $seller_id, $shop_slug)
    {
        $validator = Validator::make($request->all(), [
            'fromTime' => ['required','date_format:Y-m-d H:i:s', new IsShopOpen($seller_id)],
            'toTime' => ['required','date_format:Y-m-d H:i:s','after:fromTime', new IsShopOpen($seller_id)]
         ],[
             'toTime.after' => "The to time must be a time after from time."
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
        $orderData->from_time = $request->fromTime;
        $orderData->to_time = $request->toTime;

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

        OrderPlaced::dispatch($orderData);

        $this->destroyCart($buyer,$seller_id);

        return response()->json(['statusCode' => 200, 'success' => true, 'message' => "Order Placed Successfully.","data" => $orderData], 200);
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
        $data = Orders::select('id','seller_id','buyer_id','order_no','order_amount','status','created_at')
                ->with(['store','orderitem:id,order_id,product_id,quantity,price','buyer','seller'])
                ->where('buyer_id',$user->id)
                ->where('order_no',$order_no)
                ->first();
        if(!$data) {
            return response()->json(['statusCode' => 200, 'success' => false, 'message' => "Order not found."], 200);
        }
        return (new OrderResource($data));
    }

    public function cancleOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_no' => 'required|numeric|exists:orders,order_no',
         ]);

        if ($validator->fails()) {
            $message = $validator->errors()->first();
            return response()->json(['statusCode'=>200,'success'=>false,'message'=>$message], 200);
        }

        Orders::where('order_no',$request->order_no)
                ->update([
                    "status" => 3
                ]);

        return response()->json([
            'statusCode'=>200,
            'success'=>true,
            'message'=> "Order cancelled successfully."
        ], 200);

    }

    public function downloadInvoice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_no' => 'required|numeric|exists:orders,order_no',
        ]);

        if ($validator->fails()) {
            $message = $validator->errors()->first();
            return response()->json(['statusCode'=>422,'success'=>false,'message'=>$message], 422);
        }

        $order_no = $request->order_no;
        $user = Auth::User();
        $data = Orders::select('id','seller_id','buyer_id','order_no','order_amount','status','created_at')
                ->with(['store','orderitem:id,order_id,product_id,quantity,price','buyer','seller'])
                ->where('buyer_id',$user->id)
                ->where('order_no',$order_no)
                ->first();
        if(!$data) {
            return response()->json(['statusCode' => 422, 'success' => false, 'message' => "Order not found."], 422);
        }

        $pdf = PDF::loadView('pdf.invoice', [
            "data" => json_encode(new OrderResource($data))
        ]);
        return $pdf->setPaper('a4')->setWarnings(false)->download('invoice.pdf');
    }
}

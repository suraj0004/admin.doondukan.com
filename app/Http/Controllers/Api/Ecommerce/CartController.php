<?php

namespace App\Http\Controllers\Api\Ecommerce;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Requests\Api\AddCartRequest;
use App\Models\User;
use App\Models\Stock;
use App\Models\Cart;


class CartController extends Controller
{

    public function add(AddCartRequest $req)
    {

        try {
            $buyer  = Auth::User(); //logged in user->customer(buyer)
            $seller = User::where('id',$req->seller_id)->with('store')->first(); //seller with his store_id
            $stock  = Stock::where('product_id',$req->product_id)->first(); //product present in stock

            $productExists = Cart::where('product_id',$stock->product_id)->where('buyer_id',$buyer->id)->exists();
            if($productExists){
                return response()->json([
                    'success'=>true,
                    'message'=>'product already present in your cart.'
                ]);
            }

            $cart   = new Cart();
            $cart->buyer_id = $buyer->id;
            $cart->seller_id = $seller->id;
            $cart->store_id  = $seller->store->id;
            $cart->product_id = $stock->product_id;
            $cart->quantity = $req->quantity;
            $cart->price = $stock->price;

            $data = $cart->save();
            return response()->json([
                    'statusCode'=>200,
                    'success'=>true,
                    'message'=>'Added Sucessfully'
                ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'statusCode'=>401,
                'success'=>false,
                'message'=>'Something went wrong.',
                'error'=>$e->getMessage(),
            ], 401);
        }

    }

    // public function delete( Reqest $req)
    // {
    //     $req->cart_id;
    // }

    // public function updateQuntity(Reqest $req)
    // {
    //     $req->cart_id,
    //     $req->quantity;
    // }

    // public function listing($seller_id)
    // {
    //     //
    //     // Auth::user('cart'); //  Cart::where('buyer_id,Auth::user('id) cart details);
    // }

    // public function addMultiple()
    // {

    // }
}

<?php

namespace App\Http\Controllers\Api\Ecommerce;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AddCartRequest;
use App\Http\Requests\Api\Ecommerce\CartSyncRequest;
use App\Models\Cart;
use App\Models\Stock;
use App\Models\Store;
use App\Models\User;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{

    public function add(AddCartRequest $req)
    {

        try {

            $buyer = Auth::User();

            $productExists = Cart::where('product_id', $req->product_id)->where('buyer_id', $buyer->id)->exists();
            if ($productExists) {
                return response()->json([
                    'success' => true,
                    'message' => 'product already present in your cart.',
                ]);
            }

            $checkIsOtherSellerProduct = Cart::where('buyer_id', $buyer->id)->first();
            if (!empty($checkIsOtherSellerProduct) && $checkIsOtherSellerProduct->seller_id != $req->seller_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Other seller product not allowed in same cart.',
                ], 200);
            }

            $seller = User::where('id', $req->seller_id)->with('store:id,user_id')->first();
            $stock = Stock::select('price')->where('user_id', $req->seller_id)->where('product_id', $req->product_id)->first();
            $cart = new Cart();
            $cart->buyer_id = $buyer->id;
            $cart->seller_id = $seller->id;
            $cart->store_id = $seller->store->id;
            $cart->product_id = $req->product_id;
            $cart->quantity = $req->quantity;
            $cart->price = $stock->price;
            $data = $cart->save();

            return response()->json([
                'statusCode' => 200,
                'success' => true,
                'message' => 'Added Sucessfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'statusCode' => 401,
                'success' => false,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage(),
            ], 401);
        }

    }

    /**
     * function for deleting cart product
     */
    public function deleteCartProduct($cartId)
    {
        $userId = Auth::user()->id;
        try {
            //code...
            $deleteProduct = Cart::whereId($cartId)->whereBuyerId($userId)->delete();
            return response()->json([
                'statusCode' => 200,
                'success' => true,
                'message' => 'Product Deleted Successfully',
            ], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'statusCode' => 401,
                'success' => false,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage(),
            ], 401);
        }

    }

    public function updateQuantity(Request $request)
    {
        $userId = Auht::user()->id;
        $buyerId = $request->buyer_id;
        $productId = $request->product_id;
        $quantity = $request->quantity;

        try {
            //code...
            $cartData = Cart::whereBuyerId($buyerId)->whereProductId($productId)->first();
            $cartData->quantity = $quantity;
            $cartData->save();
            return response()->json([
                'statusCode' => 200,
                'success' => true,
                'message' => 'Quantity Updated Successfully',
            ], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'statusCode' => 401,
                'success' => false,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage(),
            ], 401);
        }

    }

    /**
     * syncing cart products from local storage to db table
     */
    public function syncCart(CartSyncRequest $request, int $seller_id, string $slug)
    {
        $buyer_id = Auth::id();
        $store_id = Store::select("id")->where("user_id", $seller_id)->first()->id;

        $requestData = collect($request->cart);
        $productIds = $requestData->pluck('product_id');
        $productQuantity = $requestData->mapWithKeys(function ($item) {
            return [$item['product_id'] => $item['quantity']];
        });

        $products = Stock::select("price", "product_id")
            ->where('user_id', $seller_id)
            ->where(function ($query) use ($requestData) {
                foreach ($requestData as $data) {
                    $query->orWhere(function ($q) use ($data) {
                        $q->where('product_id', $data["product_id"])
                            ->where('quantity', ">=", $data["quantity"]);
                    });
                }
            })
            ->get();

        try {
            DB::beginTransaction();
            foreach ($products as $product) {
                Cart::updateOrCreate(
                    ["buyer_id" => $buyer_id, "seller_id" => $seller_id, "store_id" => $store_id, "product_id" => $product->product_id],
                    ["quantity" => $productQuantity[$product->product_id], "price" => $product->price]
                );
            }
            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'statusCode' => 200,
                'success' => false,
                'message' => 'Opps something went wrong',
            ], 200);
        }

        return response()->json([
            'statusCode' => 200,
            'success' => true,
            'message' => 'Cart Sync Successfully.',
        ], 200);

    }

    /**
     * function for getting cart products listing
     */
    public function fetchCartProducts()
    {
        $userId = Auth::user()->id;
        try {
            //code...
            $data = \App\Models\Cart::whereBuyerId($userId)->get();

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'statusCode' => 401,
                'success' => false,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage(),
            ], 401);
        }

    }

}

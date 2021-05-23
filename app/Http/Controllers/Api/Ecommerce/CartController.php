<?php

namespace App\Http\Controllers\Api\Ecommerce;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AddCartRequest;
use App\Http\Requests\Api\Ecommerce\CartSyncRequest;
use App\Http\Resources\Ecommerce\CartCollection;
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

    public function add(AddCartRequest $request, int $seller_id, string $slug)
    {
        $buyer_id = Auth::id();

        $store = Store::where('user_id', $seller_id)->first();

        if (!$store) {
            return response()->json([
                'success' => false,
                'message' => 'shop not available.',
            ], 200);
        }

        $stock = Stock::select("price")
            ->where('user_id', $seller_id)
            ->where('product_id', $request->product_id)
            ->where('quantity', '>', 0)
            ->where('price', '>', 0)
            ->first();

        if (!$stock) {
            return response()->json([
                'success' => false,
                'message' => 'product not available.',
            ], 200);
        }

        $productExists = Cart::where('product_id', $request->product_id)
            ->where('buyer_id', $buyer_id)
            ->where('seller_id', $seller_id)
            ->exists();

        if ($productExists) {
            return response()->json([
                'success' => false,
                'message' => 'product already present in your cart.',
            ], 200);
        }

        $cart = new Cart();
        $cart->buyer_id = $buyer_id;
        $cart->seller_id = $seller_id;
        $cart->store_id = $store->id;
        $cart->product_id = $request->product_id;
        $cart->quantity = 1;
        $cart->price = $stock->price;
        $data = $cart->save();
        $cart->load(["product"]);

        return response()->json([
            'statusCode' => 200,
            'success' => true,
            'message' => 'Added Sucessfully',
            'data' => $cart
        ], 200);

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
    public function fetchCartProducts(int $seller_id, string $slug)
    {
        $user_id = Auth::user()->id;
        $cart = Cart::select("id", "product_id", "quantity")
            ->where("buyer_id", $user_id)
            ->where('seller_id', $seller_id)
            ->with(["product"])
            ->get();

        return (new CartCollection($cart))->additional([
            "message" => "Cart Data",
        ]);

    }

}

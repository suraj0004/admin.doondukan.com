<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Shop\PurchaseCollection;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Stock;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class PurchaseController extends Controller
{
    //Function to add purchases of user
    public function addPurchase(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'price' => 'required|numeric',
            'selling_price' => 'required|numeric',
            'product_id' => 'required|integer',
            'product_source' => 'required',
            'quantity' => 'required',
        ]);

        if ($validator->fails()) {
            $message = $validator->errors()->first();
            return response()->json(['statusCode' => 200, 'success' => false, 'message' => $message], 200);
        }
        $user = Auth::User();
        $purchase = new Purchase();
        $purchase->user_id = $user->id;
        $purchase->price = $request->price;
        $purchase->product_id = $request->product_id;
        $purchase->product_source = $request->product_source;
        $purchase->quantity = $request->quantity;

        if ($purchase->save()) {
            //Add Product in Stock
            if ($request->product_source == "main") {
                $product_stock = Stock::where('product_id', $request->product_id)->where('product_source', 'main')->where('user_id', $user->id)->first();
            } else {
                $product_stock = Stock::where('product_id', $request->product_id)->where('product_source', 'temp')->where('user_id', $user->id)->first();
            }
            if (!$product_stock) {
                $product_stock = new Stock();
            }

            $product_stock->user_id = $user->id;
            $product_stock->product_id = $request->product_id;
            $product_stock->quantity = $product_stock->quantity + $request->quantity;
            $product_stock->product_source = $request->product_source;
            $product_stock->price = $request->selling_price;
            $product_stock->last_purchased_at = Carbon::now();
            $product_stock->save();

            return response()->json(['statusCode' => 200, 'success' => true, 'message' => 'Purchased Added Succesfully.'], 200);
        } else {
            return response()->json(['statusCode' => 200, 'success' => false, 'message' => 'Oops! Something Went Wrong!'], 200);
        }
    }

    //Function to get list of purchased products of a particular user
    public function purchasedList(Request $request)
    {
        $user = Auth::User();
        $data = Purchase::with('product')
            ->where('product_source', 'main')
            ->where('user_id', $user->id);

        if (isset($request->purchaseDate) && !empty($request->purchaseDate)) {
            $purchaseDate = Carbon::parse($request->purchaseDate)->format('Y-m-d');
            $data = $data->whereDate('created_at', $purchaseDate);
        }

        if (isset($request->sortType) && !empty($request->sortType)) {
            if ($request->sortType == 'purchase-date-latest') {
                $data = $data->latest('created_at');
            } else if ($request->sortType == 'purchase-date-oldest') {
                $data = $data->oldest('created_at');
            } else if ($request->sortType == 'qty-low-to-high') {
                $data = $data->orderBy('quantity', 'asc');
            } else if ($request->sortType == 'qty-high-to-low') {
                $data = $data->orderBy('quantity', 'desc');
            }
        }else{
            $data = $data->latest('created_at');
        }

        if (isset($request->search) && !empty($request->search)) {
            $data = $data->whereHas('product', function ($query) use ($request) {
                $query->where('name', 'LIKE', '%' . $request->search . '%');
            });
        }

        $data = $data->paginate(20);
        return new PurchaseCollection($data);

    }

    public function addProductToCatalogue(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_ids' => 'required|array',
        ]);

        if ($validator->fails()) {
            $message = $validator->errors()->first();
            return response()->json(['statusCode' => 200, 'success' => false, 'message' => $message], 200);
        }
        $product_ids = array_unique($request->product_ids);
        $user = Auth::User();
        $product = Product::whereIn('id', $product_ids)->get();
        if ($product->isEmpty()) {
            return response()->json(['statusCode' => 200, 'success' => false, 'message' => 'No Product Found.'], 200);
        }
        DB::beginTransaction();
        try {
            foreach ($product as $key => $value) {
                $purchase = new Purchase();
                $purchase->user_id = $user->id;
                $purchase->price = $value->price;
                $purchase->product_id = $value->id;
                $purchase->product_source = "main";
                $purchase->quantity = config("constants.DEFAULT_QTY");
                $purchase->save();
                $product_stock = Stock::where('product_id', $value->id)->where('user_id', $user->id)->first();
                if (!$product_stock) {
                    $product_stock = new Stock();
                }
                $product_stock->user_id = $user->id;
                $product_stock->product_id = $value->id;
                $product_stock->quantity = config("constants.DEFAULT_QTY") + $product_stock->quantity;
                $price = $value->price;
                if (!empty($product_stock->price)) {
                    $price = $product_stock->price;
                }
                $product_stock->price = $price;
                $product_stock->product_source = "main";
                $product_stock->last_purchased_at = Carbon::now();
                $product_stock->save();
            }
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(["success" => false, "message" => "Oops something went wrong."], 200);
        }

        DB::commit();
        return response()->json(['statusCode' => 200, 'success' => true, 'message' => 'Product Succesfully Added.'], 200);
    }
}

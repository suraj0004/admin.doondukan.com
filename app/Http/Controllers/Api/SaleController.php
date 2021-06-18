<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Shop\SaleCollection;
use App\Models\Sale;
use App\Models\Sale_Return;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use Carbon\Carbon;

class SaleController extends Controller
{
    //This function is used to get the sales list of use.
    public function saleList(Request $request)
    {
        $user = Auth::User();
        $data = Sale::with(['product', 'bill:id,status'])
            ->where('user_id', $user->id)
            ->where('product_source', 'main');

        if (isset($request->status) && !empty($request->status)) {
            $status = $request->status;
            $data = $data->whereHas('bill', function ($query) use ($status) {
                $query->where('status', $status);
            });
        }

        if (isset($request->saleDate) && !empty($request->saleDate)) {
            $saleDate = Carbon::parse($request->saleDate)->format('Y-m-d');
            $data = $data->whereDate('created_at', $saleDate);
        }

        if (isset($request->sortType) && !empty($request->sortType)) {
            if ($request->sortType == 'sale-date-latest') {
                $data = $data->latest('created_at');
            } else if ($request->sortType == 'sale-date-oldest') {
                $data = $data->oldest('created_at');
            } else if ($request->sortType == 'qty-low-to-high') {
                $data = $data->orderBy('quantity', 'asc');
            } else if ($request->sortType == 'qty-high-to-low') {
                $data = $data->orderBy('quantity', 'desc');
            }
        } else {
            $data = $data->latest('created_at');
        }

        if (isset($request->search) && !empty($request->search)) {
            $data = $data->whereHas('product', function ($query) use ($request) {
                $query->where('name', 'LIKE', '%' . $request->search . '%');
            });
        }

        $data = $data->paginate(20);
        return new SaleCollection($data);

    }

    //this function is used to handle sale return.
    public function saleReturn(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bill_id' => 'required|numeric',
            'sale_id' => 'required|numeric',
            'price' => 'required|integer',
            'quantity' => 'required',
        ]);

        if ($validator->fails()) {
            $message = $validator->errors()->first();
            return response()->json(['statusCode' => 200, 'success' => false, 'message' => $message], 200);
        }

        $user = Auth::User();
        $getSale = Sale::where('id', $request->sale_id)->where('bill_id', $request->bill_id)->where('user_id', $user->id)->first();
        if (!$getSale) {
            return response()->json(['statusCode' => 200, 'success' => false, 'message' => 'sale not found'], 200);
        }

        $getstock = Stock::where('product_id', $getSale->product_id)->where('user_id', $user->id)->first();
        if (!$getstock) {
            return response()->json(['statusCode' => 200, 'success' => false, 'message' => 'stock not found'], 200);
        }

        $sale_return = new Sale_Return();
        $sale_return->user_id = $user->id;
        $sale_return->bill_id = $request->bill_id;
        $sale_return->sale_id = $request->sale_id;
        $sale_return->stock_id = $getstock->id;
        $sale_return->price = $request->price;
        $sale_return->quantity = $request->quantity;

        if ($sale_return->save()) {
            $getstock->quantity = $getstock->quantity + $request->quantity;
            $getstock->save();
            return response()->json(['statusCode' => 200, 'success' => true, 'message' => 'sale return successfully.'], 200);
        } else {
            return response()->json(['statusCode' => 200, 'success' => false, 'message' => 'Oop! Something went wrong.'], 200);
        }
    }
}

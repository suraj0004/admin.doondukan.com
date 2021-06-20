<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AddCustomProductFormRequest;
use App\Http\Resources\Shop\ProductCollection;
use App\Services\CustomProductService;
use Auth;
use Illuminate\Http\Request;

class CustomProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $service = new CustomProductService();
        $data = $service->fetchUserCustomProductList($user->id);
        return new ProductCollection($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AddCustomProductFormRequest $request)
    {
        $user = Auth::user();
        $service = new CustomProductService();
        $product = $service->addUserCustomProduct($user->id, $request->name, (int) $request->weight, $request->weight_type, (int) $request->price, (int) $request->category_id, $request->image);
        if ($product) {
            return response()->json([
                "success" => true,
                "message" => "Your custom product have been added successfully ",
                "data" => $product,
            ], 200);
        }
        return response()->json([
            "success" => false,
            "message" => "Sorry! Please try again or contact to Adminstrator",
        ], 200);
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AddCustomProductFormRequest $request, $product_id)
    {
        $service = new CustomProductService();
        $product = $service->updateUserCustomProduct($product_id, $request->product, (int) $request->weight, $request->weight_type);
        if ($product) {
            return response()->json([
                "success" => true,
                "message" => "Your custom product have been updated successfully ",
                "data" => $product,
            ], 200);
        }
        return response()->json([
            "success" => false,
            "message" => "Sorry! Please try again or contact to Adminstrator",
        ], 200);
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

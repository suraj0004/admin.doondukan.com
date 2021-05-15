<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

use App\Http\Requests\Api\AddCustomProductFormRequest;
use App\Services\CustomProductService;

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
        $customProductList = $service->fetchUserCustomProductList($user->id);
        if($customProductList)
            return response()->json([
                "success" => true,
                "data" => $customProductList
            ],200);

        return response()->json([
            "success" => false,
            "message" => "You have not added any custom product"
            ],200);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AddCustomProductFormRequest $request)
    {
        $input = $request->all();
        $user = Auth::user();
        $service = new CustomProductService();
        $product = $service->addUserCustomProduct($user->id,$request->product,(int)$request->weight,$request->weight_type);
        if($product){
            return response()->json([
                "success" => true,
                "message" =>"Your custom product have been added successfully ",
                "data" => $product
            ],200);
        }
        return response()->json([
            "success" => false,
            "message" => "Sorry! Please try again or contact to Adminstrator"
        ],200);
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
        $product = $service->updateUserCustomProduct($product_id,$request->product,(int)$request->weight,$request->weight_type);
        if($product){
            return response()->json([
                "success" => true,
                "message" =>"Your custom product have been updated successfully ",
                "data" => $product
            ],200);
        }
        return response()->json([
            "success" => false,
            "message" => "Sorry! Please try again or contact to Adminstrator"
        ],200);
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

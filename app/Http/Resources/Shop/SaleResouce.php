<?php

namespace App\Http\Resources\Shop;

use Illuminate\Http\Resources\Json\JsonResource;

class SaleResouce extends JsonResource
{
    public function toArray($request)
    {
        return [
            "created_at" => $this->created_at,
            "price" => $this->price,
            "quantity" => $this->quantity,
            "product" => new ProductResource($this->product),
            "bill" => [
                "id" => $this->bill->id,
                "status" => $this->bill->status,
            ]
        ];
    }

    public function with($request)
    {
        return [
            'statusCode' => 200,
            'success' => true,
        ];
    }
}

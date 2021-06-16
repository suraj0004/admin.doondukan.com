<?php

namespace App\Http\Resources\Shop;

use Illuminate\Http\Resources\Json\JsonResource;

class StockResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "last_purchased_at" => $this->last_purchased_at,
            "price" => $this->price,
            "quantity" => $this->quantity,
            "in_stock" => (bool)$this->quantity,
            "product" => new ProductResource($this->product),
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

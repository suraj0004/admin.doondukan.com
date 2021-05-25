<?php

namespace App\Http\Resources\Ecommerce;

use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
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
            "product_id" => $this->product_id,
            "quantity" => $this->quantity,
            "product" => [
                "id" => $this->product->id,
                "slug" => $this->product->slug,
                "name" => $this->product->name,
                "price" => $this->product_price,
                "weight" => $this->product->weight . ' ' . $this->product->weight_type,
                "image" => getFileUrl(config("constants.disks.PRODUCT"), $this->product->image),
                "thumbnail" => getFileUrl(config("constants.disks.PRODUCT"), "thumb_".$this->product->image)
            ],
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function with($request)
    {
        return [
            'statusCode' => 200,
            'success' => true,
        ];
    }
}

<?php

namespace App\Http\Resources\Ecommerce;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
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
            "price" => $this->price,
            "quantity" => $this->quantity,
            "product" => [
                "name" => $this->product->name,
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

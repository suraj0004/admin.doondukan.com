<?php

namespace App\Http\Resources\Ecommerce;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryProductResource extends JsonResource
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
            "id" => $this->product_id,
            "slug" => $this->product_slug,
            "name" => $this->name,
            "price" => $this->price,
            "weight" => $this->weight . ' ' . $this->weight_type,
            "out_of_stock" => ($this->quantity == 0),
            "image" => getFileUrl(config("constants.disks.PRODUCT"), $this->image),
            "thumbnail" => getFileUrl(config("constants.disks.PRODUCT"), "thumb_".$this->image)
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

<?php

namespace App\Http\Resources\Shop;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            "name" => $this->name,
            "weight" => $this->weight,
            "weight_type" => $this->weight_type,
            $this->mergeWhen((isset($this->price) && !is_null($this->price)), [
                "price" => $this->price,
            ]),
            $this->mergeWhen((isset($this->category_id) && !is_null($this->category_id)), [
                "category" => $this->category,
            ]),
            $this->mergeWhen((isset($this->created_at) && !is_null($this->created_at)), [
                "created_at" => $this->created_at,
            ]),
            "image" => getFileUrl(config("constants.disks.PRODUCT"), $this->image),
            "thumbnail" => getFileUrl(config("constants.disks.PRODUCT"), "thumb_" . $this->image),
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

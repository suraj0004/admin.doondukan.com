<?php

namespace App\Http\Resources;

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
                "weight_type"=>$this->weight_type,
                "price"=>$this->price,
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

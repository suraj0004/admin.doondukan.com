<?php

namespace App\Http\Resources\Ecommerce;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            "id" => $this->category_id,
            "slug" => $this->slug,
            "name" => $this->category_name,
            "product_count" => $this->product_count,
            "image" => getFileUrl(config("constants.disks.CATEGORY"), $this->image),
            "thumbnail" => getFileUrl(config("constants.disks.CATEGORY"), "thumb_".$this->image)
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

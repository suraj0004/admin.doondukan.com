<?php

namespace App\Http\Resources\Shop;

use Illuminate\Http\Resources\Json\JsonResource;

class BillResource extends JsonResource
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
            "created_at" => $this->created_at,
            "order_type" => $this->order_type,
            "sales_count" => $this->sales_count,
            "sales_price" => $this->sales_price,
            "image" => getFileUrl(config("constants.disks.PRODUCT"), $this->image),
        ];
    }
}

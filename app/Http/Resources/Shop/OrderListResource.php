<?php

namespace App\Http\Resources\Shop;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderListResource extends JsonResource
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
            "from_time" => $this->from_time,
            "to_time" => $this->to_time,
            "buyer" => $this->buyer,
            "order_amount" => $this->order_amount,
            "order_no" => $this->order_no,
            "orderitem_count" => $this->orderitem_count,
            "image" => getFileUrl(config("constants.disks.PRODUCT"), $this->image),
            "status" => $this->status,
        ];
    }
}

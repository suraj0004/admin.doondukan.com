<?php

namespace App\Http\Resources\Ecommerce;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            "order_no" => $this->order_no,
            "order_amount" => $this->order_amount,
            "orderitem_count" => $this->orderitem_count,
            "status" => $this->status,
            "created_at" => $this->created_at,
            "store" => [
                "name" => $this->store->name,
                "image" => getFileUrl(config("constants.disks.STORE"), $this->store->logo),
                "thumbnail" => getFileUrl(config("constants.disks.STORE"), "thumb_".$this->store->logo)
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

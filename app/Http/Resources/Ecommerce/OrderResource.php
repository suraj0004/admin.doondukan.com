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
            "store" => new StoreResource($this->store),
            "items" =>new OrderItemCollection($this->orderitem),
            "buyer" => $this->buyer,
            "seller" => $this->seller,
            'delivery_type' => $this->delivery_type == 'user-self-collected' ? 'I will go to Shop':'Home Delivery',
            'is_home_delivery' => $this->delivery_type != 'user-self-collected',
            'from_time' => $this->from_time,
            'to_time' => $this->to_time,
            'delivery_charges' => $this->delivery_charges,
            'delivery_address' => $this->shippingAddress,
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

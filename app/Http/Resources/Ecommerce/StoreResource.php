<?php

namespace App\Http\Resources\Ecommerce;

use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
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
            "shop_name" => $this->name,
            "mobile" => $this->user->phone,
            "address" => $this->address,
            "open_at" => $this->open_at,
            "close_at" => $this->close_at,
            "shop_url" => "https://app.doondukan.com/".$this->user_id."-".$this->slug,
            "logo" => getFileUrl(config("constants.disks.STORE"), $this->logo),
            "seller_name" => $this->user->name,
            'delivery_medium'=>$this->delivery_medium,
            'order_within_km'=>$this->order_within_km,
            'minimum_order_amount'=>$this->minimum_order_amount,
            'delivery_charges'=>($this->delivery_medium == 'delivery-partner')?config("constants.DELIVERY_CHARGES"):$this->delivery_charges
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

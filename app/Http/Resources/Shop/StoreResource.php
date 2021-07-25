<?php

namespace App\Http\Resources\Shop;

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
            "id"=>$this->id,
            "user_id"=>$this->user_id,
            "shop_name" => $this->name,
            "slug"=>$this->slug,
            "email"=>$this->email,
            "mobile" => $this->mobile,
            "address" => $this->address,
            "about" => $this->about,
            "open_at" => $this->open_at,
            "close_at" => $this->close_at,
            "registration_date"=>$this->registration_date,
            "valid_upto"=>$this->valid_upto,
            "created_at"=>$this->created_at,
            "shop_url" => "https://app.doondukan.com/".$this->user_id."-".$this->slug,
            "logo" => getFileUrl(config("constants.disks.STORE"), $this->logo),
            'delivery_medium'=>$this->delivery_medium,
            'order_within_km'=>$this->order_within_km,
            'minimum_order_amount'=>$this->minimum_order_amount,
            'delivery_charges'=>$this->delivery_charges
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

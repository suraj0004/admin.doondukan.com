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
            "mobile" => $this->mobile,
            "address" => $this->address,
            "open_at" => $this->open_at,
            "close_at" => $this->close_at,
            "logo" => getFileUrl(config("constants.disks.STORE"), $this->logo),
            "seller_name" => $this->user->name,
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
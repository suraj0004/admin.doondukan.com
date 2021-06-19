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
            "open_at" => $this->open_at,
            "close_at" => $this->close_at,
            "registration_date"=>$this->registration_date,
            "valid_upto"=>$this->valid_upto,
            "created_at"=>$this->created_at,
            "shop_url" => "https://app.doondukan.com/".$this->user_id."-".$this->slug,
            "logo" => getFileUrl(config("constants.disks.STORE"), $this->logo)
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

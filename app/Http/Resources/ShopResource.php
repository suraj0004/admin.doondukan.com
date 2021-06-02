<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ShopResource extends JsonResource
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
            "id"=> $this->id,
            "user_id" => $this->user_id,
            "name" => $this->name,
            "slug" => $this->slug,
            "address" => $this->address,
            "logo" => getFileUrl(config("constants.disks.STORE"), $this->logo),
        ];
    }

    public function with($request)
    {
        return [
            'statusCode' => 200,
            'success' => true,
        ];
    }
}

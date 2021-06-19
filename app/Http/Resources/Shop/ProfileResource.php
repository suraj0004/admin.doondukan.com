<?php

namespace App\Http\Resources\Shop;

use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
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
            "role" => $this->role,
            "name" => $this->name,
            "phone"=>$this->phone,
            "email"=>$this->email,
            "lat"=>$this->lat,
            "lng"=>$this->lng,
            "created_at"=>$this->created_at,
            "store"=>new StoreResource($this->store),
            "image" => getFileUrl(config("constants.disks.PROFILE"), $this->image),
            "thumbnail" => getFileUrl(config("constants.disks.PROFILE"), "thumb_".$this->image)
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

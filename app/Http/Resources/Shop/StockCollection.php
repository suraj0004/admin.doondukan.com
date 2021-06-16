<?php

namespace App\Http\Resources\Shop;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Models\Stock;

class StockCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $this->collection->transform(function (Stock $stock) {
            return (new StockResource($stock));
        });
        return parent::toArray($request);
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

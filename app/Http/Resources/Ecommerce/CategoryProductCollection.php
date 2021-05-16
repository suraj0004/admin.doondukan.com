<?php

namespace App\Http\Resources\Ecommerce;
use App\Models\Category;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CategoryProductCollection extends ResourceCollection
{
     /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $this->collection->transform(function (Category $category) {
            return (new CategoryProductResource($category));
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

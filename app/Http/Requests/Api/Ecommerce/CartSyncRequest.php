<?php

namespace App\Http\Requests\Api\Ecommerce;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CartSyncRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "cart" => "required|array",
            "cart.*.product_id" => "required|numeric|distinct",
            "cart.*.quantity" => "required|numeric",
        ];
    }

    public function messages()
    {
        return [
            "cart.*.product_id.required" => "Product is required",
            "cart.*.product_id.numeric" => "Product is required",
            "cart.*.product_id.distinct" => "Product is required",
            "cart.*.quantity.required" => "Quantity is required",
            "cart.*.quantity.numeric" => "Quantity is required",
        ];
    }

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            "success" => false,
            "message" => $validator->errors()->first()
        ], 200));
    }

    public function getData($seller_id, $store_id)
    {
        # code...
    }


}

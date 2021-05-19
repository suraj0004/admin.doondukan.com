<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Rules\IsProductAvailable;

class AddCartRequest extends FormRequest
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
            'seller_id' => 'required|exists:Users,id|numeric',
            'product_id' => 'required|exists:Stocks,product_id|numeric',
            'quantity' => [
                            'required',
                            'numeric',
                            'min:1',
                            new IsProductAvailable($this->product_id,$this->seller_id),
                         ],
        ];
    }

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            "success" => false,
            "message" => $validator->errors()->all()
        ], 200));
    }
}

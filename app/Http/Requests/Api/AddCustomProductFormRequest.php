<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
class AddCustomProductFormRequest extends FormRequest
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
            "product" => "required|string",
            "weight_type" => "required|in:kg,gm,L,ml",
            "weight" => "required|numeric",
        ];
    }

    protected function failedValidation(Validator $validator) { 
        throw new HttpResponseException(response()->json([
            "success" => false,
            "message" => $validator->errors()->first()
        ], 200)); 
    }
}

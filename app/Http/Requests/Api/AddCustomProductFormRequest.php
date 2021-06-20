<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Rules\ValidBase64Image;

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
            "name" => "required|string",
            "weight" => "required|numeric",
            "weight_type" => "required|string",
            "price" => "required|numeric",
            "category_id" => "required|numeric|exists:categories,id",
            "image" => [
                "required",
                "string",
                new ValidBase64Image
            ]
        ];
    }

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            "success" => false,
            "message" => $validator->errors()->first()
        ], 200));
    }
}

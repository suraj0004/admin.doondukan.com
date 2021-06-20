<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Api\AddCustomProductFormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Rules\ValidBase64Image;

class EditCustomProductRequest extends AddCustomProductFormRequest
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
        $rule = parent::rules();
        $rule["image"] =  [
            "nullable",
            "string",
            new ValidBase64Image
        ];
        return $rule;
    }

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            "success" => false,
            "message" => $validator->errors()->first()
        ], 200));
    }
}

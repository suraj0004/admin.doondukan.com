<?php

namespace App\Http\Requests\Api\Ecommerce;

use Illuminate\Foundation\Http\FormRequest;
use Hash;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateProfileRequest extends FormRequest
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
            "name" => "required|string|max:50",
            "phone" => "required|numeric|digits:10|unique:users,phone,".$this->user()->id,
            "email" => "nullable|unique:users,email,".$this->user()->id,
            "password" => "nullable|confirmed",
        ];
    }

    public function getData()
    {
        $data = [
            "name" => $this->name,
            "phone" => $this->phone,
        ];

        if($this->has('email') && !empty($this->email)){
            $data["email"] = $this->email;
        }

        if($this->has('password') && !empty($this->password)){
            $data["password"] = Hash::make($this->password);
        }

        return $data;

    }

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            "success" => false,
            "message" => $validator->errors()->first()
        ], 200));
    }
}

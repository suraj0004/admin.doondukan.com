<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidBase64Image implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $image_extensions = config("constants.BASE64_IMAGE_EXTENSION");
        foreach ($image_extensions as $extension) {
            if(stripos($value,$extension) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The given :attribute should be JPG or PNG. ';
    }
}

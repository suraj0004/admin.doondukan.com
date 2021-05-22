<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Stock;

class IsProductAvailable implements Rule
{
    private $product_id;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($product_id,$seller_id)
    {
        $this->product_id = $product_id;
        $this->seller_id = $seller_id;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($key, $value)
    {
        $stock  = Stock::where('product_id',$this->product_id)->where('user_id',$this->seller_id)->first();

        if(!$stock || $stock->quantity < $value ){
            return false;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Product invalid or Quantity is less';
    }
}

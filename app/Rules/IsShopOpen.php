<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Store;
use Carbon\Carbon;

class IsShopOpen implements Rule
{
    private $seller_id;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($seller_id)
    {
        $this->seller_id = $seller_id;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $time
     * @return bool
     */
    public function passes($attribute, $time)
    {
        $time = Carbon::parse($time)->format('H:i:s');
        return Store::where('user_id',$this->seller_id)
                ->where('open_at','<=',$time)
                ->where('close_at','>=',$time)
                ->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Sorry, Shop is close on your given time. please select different :attribute.';
    }
}

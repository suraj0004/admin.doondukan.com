<?php

namespace App\Http\ViewComposers;

use App\Models\TempProduct;

class TempProductComposer
{
    public function compose($view)
    {
        $view->with('tempProductCount', TempProduct::count());
    }
}

?>
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Sale;
class Bill extends Model
{
	use SoftDeletes;
	
    protected $table = "bills";
    protected $dates = ['deleted_at'];
    protected $hidden = ['deleted_at'];

    public function sales()
    {
    	return $this->hasMany(Sale::class, 'bill_id', 'id')->with('product');
    }
}

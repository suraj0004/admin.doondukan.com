<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Alexmg86\LaravelSubQuery\Traits\LaravelSubQueryTrait;
use App\Models\Sale;
class Bill extends Model
{
	use SoftDeletes;
	use LaravelSubQueryTrait;
    protected $table = "bills";
    protected $dates = ['deleted_at'];
    protected $hidden = ['deleted_at'];

    public function sales()
    {
    	return $this->hasMany(Sale::class, 'bill_id', 'id')->with(['product' => function($query){ $query->with('brand'); }]);
    }
}

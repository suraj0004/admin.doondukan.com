<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Alexmg86\LaravelSubQuery\Traits\LaravelSubQueryTrait;
use App\Models\Product;
use App\Models\TempProduct;
use App\Models\Bill;
use App\Models\Stock;
use App\Models\SaleReturn;

class Sale extends Model
{
    use SoftDeletes;
    use LaravelSubQueryTrait;
	protected $table = "sales";
	protected $dates = [];
	protected $hidden = [];

	public function product()
    {
    	return $this->belongsTo(Product::class,'product_id','id');
    }

    public function tempProduct()
    {
    	return $this->belongsTo(TempProduct::class,'product_id','id');
    }

    public function bill()
    {
        return $this->belongsTo(Bill::class,'bill_id','id');
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class,'product_id','product_id');
    }

    public function mainStock()
    {
        return $this->belongsTo(Stock::class,'product_id','product_id')->where('product_source','main');
    }

    public function tempStock()
    {
        return $this->belongsTo(Stock::class,'product_id','product_id')->where('product_source','temp');
    }

    public function returns()
    {
        return $this->hasMany(SaleReturn::class);
    }


}

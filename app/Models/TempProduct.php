<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TempProduct extends Model
{
    use SoftDeletes;
    protected $table = "temp_products";

    public function user()
    {
    	return $this->belongsTo(User::class,'user_id','id');
    }
}

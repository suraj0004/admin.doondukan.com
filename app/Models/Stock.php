<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stock extends Model
{
	use SoftDeletes;
	
    protected $table = "stocks";
    protected $dates = ['deleted_at'];
    protected $hidden = ['deleted_at'];
}

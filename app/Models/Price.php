<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Price extends Model
{
	use SoftDeletes;
	
    protected $table = "prices";
    protected $dates = ['deleted_at'];
    protected $hidden = ['deleted_at'];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bill extends Model
{
	use SoftDeletes;
	
    protected $table = "bills";
    protected $dates = ['deleted_at'];
    protected $hidden = ['deleted_at'];
}

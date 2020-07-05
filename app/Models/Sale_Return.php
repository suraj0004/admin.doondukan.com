<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale_Return extends Model
{
	use SoftDeletes;
    protected $table = "sale_returns";
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
   use SoftDeletes;
   
   protected $table = "stores";
   protected $dates = ['deleted_at'];
   protected $hidden = ['deleted_at'];
}

<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Models\Store;
use App\Models\Stock;
use App\Models\Purchase;
use App\Models\Sale;

class User extends Authenticatable
{
    use Notifiable,HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','phone',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token','deleted_at',
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $table = "users";
    
    public function store() 
    {
        return $this->hasOne(Store::class, 'user_id', 'id');
    }

    public function stocks() 
    {
        return $this->hasMany(Stock::class, 'user_id', 'id');
    }

    public function purchases() 
    {
        return $this->hasMany(Purchase::class, 'user_id', 'id');
    }

    public function sales() 
    {
        return $this->hasMany(Sale::class, 'user_id', 'id');
    }

    public function availableStocks() 
    {
        return $this->hasMany(Stock::class, 'user_id', 'id')->where('quantity','>',0);
    }    
}
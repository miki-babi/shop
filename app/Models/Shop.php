<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    /** @use HasFactory<\Database\Factories\ShopFactory> */
    use HasFactory;
    protected $fillable = ['name'];
    
    // public function orders()
    // {
    //     return $this->hasMany(Order::class);
    // }
}

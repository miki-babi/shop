<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;
    protected $fillable = [
        'shop_id',
        'order_id',
        'unique_mailitem_id',
        'identifier',
        'event',
    ];
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}

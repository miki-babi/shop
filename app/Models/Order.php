<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;
    protected $fillable = [
        'user_id', // Changed from 'shop_id' to 'user_id'
        'order_id',
        'unique_mailitem_id',
        'identifier',
        'event',
        'RecipientPOBox',
        'ForceDuplicate',
        'MailProductType',
        'EventType',
        'Username',
        'Facility',
        'Timestamp',
        'Weight',
        'Condition',
        'SenderName',
        'SenderAddress',
        'SenderPostcode',
        'SenderCity',
        'SenderPhone',
        'SenderEmail',
        'SenderPOBox',
        'RecipientName',
        'RecipientAddress',
        'RecipientPostcode',
        'RecipientCity',
        'RecipientPhone',
        'RecipientEmail',
        'order_status',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

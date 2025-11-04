<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'receiver_id',
        'angel_id',
        'status',
        'escrow_amount',
        'escrow_asset',
        'payment_method',
        'dispute_reason',
    ];

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function angel()
    {
        return $this->belongsTo(User::class, 'angel_id');
    }
}
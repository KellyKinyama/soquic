<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = [
        'user_id',
        'coin_balance',
        'gift_card_balance',
    ];

    protected $attributes = [
        'coin_balance' => 0.00,
        'gift_card_balance' => 0.00,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

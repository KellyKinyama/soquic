<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Wallet; // 1. Imported Wallet Model

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_angel', // Added for mass assignment if Fortify handles this field
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'is_angel',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The "booted" method of the model.
     * This registers an event listener to create a Wallet after a User is created.
     */
    protected static function boot() // 2. Added static boot method
    {
        parent::boot();

        static::created(function ($user) {
            // Automatically create a new wallet with default balances (0.00)
            $user->wallet()->create([
                'coin_balance' => 0.00,
                'gift_card_balance' => 0.00,
            ]);
        });
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }
}
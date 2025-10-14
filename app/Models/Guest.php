<?php

namespace App\Models;

use App\Models\Traits\CanResetGuestPassword;
use App\Notifications\GuestResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
class Guest extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,CanResetGuestPassword;
    protected $guard_name = 'guest';
    
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'identification_number',
        'email_verified_at',
        'is_blocked',
        'google_id',    
    ];

    protected $hidden = [
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_blocked' => 'boolean',
        'password' => 'hashed',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'guest_id');
    }

    public function roomServiceOrders()
    {
        return $this->hasMany(RoomServiceOrder::class, 'guest_id');
    }
    public function sendPasswordResetNotification($token)
    {
        // رابط React frontend
        $url = config('app.frontend_url') . "/reset-password?token=$token&email={$this->email}";

        $this->notify(new \App\Notifications\ResetPasswordCustom($url));
    }
    public function contactMessages()
    {
        return $this->hasMany(ContactMessage::class, 'user_id', 'user_id');
    }
}


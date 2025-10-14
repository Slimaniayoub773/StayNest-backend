<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';
    
    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    protected $fillable = [
        'type',
        'notifiable_type',
        'notifiable_id',
        'data',
        'read_at',
        'action_url', // Optional
        'icon',       // Optional
        'created_by', // Optional
    ];

    /**
     * Polymorphic relation to the notifiable entity (User, Guest, etc.)
     */
    public function notifiable()
    {
        return $this->morphTo();
    }

    /**
     * Scope to get only unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }
}

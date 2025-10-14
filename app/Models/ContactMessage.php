<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    use HasFactory;

    protected $table = 'contact_messages';   // table name

    protected $primaryKey = 'contact_id';    // primary key

    protected $fillable = [
        'user_id',
        'guest_id',
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'status',
    ];

    /**
     * Relationships
     */

    // A contact message can belong to a user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // A contact message can belong to a guest
    public function guest()
    {
        return $this->belongsTo(Guest::class, 'guest_id', 'guest_id');
    }
}

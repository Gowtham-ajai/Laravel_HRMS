<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'body',
        'sender_name',
        'sender_email',
        'recipient_email',
        'type',
        'is_sent',
        'sent_at'
    ];

    protected $casts = [
        'is_sent' => 'boolean',
        'sent_at' => 'datetime'
    ];
}

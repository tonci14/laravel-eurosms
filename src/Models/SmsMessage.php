<?php

namespace Tonci14\LaravelEuroSMS\Models;

use Illuminate\Database\Eloquent\Model;

class SmsMessage extends Model
{
    protected $table = 'euro_sms_queue';

    protected $fillable = [
        'user_id',
        'phone',
        'message',
        'status',
        'error',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];
}

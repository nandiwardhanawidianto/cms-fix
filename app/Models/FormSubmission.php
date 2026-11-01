<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormSubmission extends Model
{
    protected $fillable = [
        'order_number', 'customer_whatsapp', 'shopee_username', 'display_order',
        'photo_package', 'photo_count', 'payload', 'raw_form', 'status',
        'published_slug_id', 'published_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'raw_form' => 'array',
        'published_at' => 'datetime',
    ];
}

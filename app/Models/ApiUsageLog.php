<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiUsageLog extends Model
{
    protected $fillable = [
        'user_id',
        'service',
        'endpoint',
        'request_payload',
        'http_status',
        'success',
        'response_summary',
        'latency_ms',
        'ip_address',
        'requested_at',
    ];

    protected $casts = [
        'request_payload'  => 'array',
        'response_summary' => 'array',
        'success'          => 'boolean',
        'requested_at'     => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'address',
        'city',
        'gst',
        'photo',
        'sop',
        'channel_partner_id',
        'additional_emails',
        // Add other fields as needed
    ];

    protected $casts = [
        'additional_emails' => 'array',
    ];

    public function channelPartner(): BelongsTo
    {
        return $this->belongsTo(ChannelPartner::class);
    }

    public function phases(): HasMany
    {
        return $this->hasMany(Phase::class, "group_id");
    }
}

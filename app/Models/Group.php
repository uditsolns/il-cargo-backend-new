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
        'policy_no',
        'policy_expiry_date',
        // Add other fields as needed
    ];

    protected $casts = [
        'additional_emails' => 'array',
        // 'date:Y-m-d' keeps a real Carbon instance (isPolicyExpired()
        // needs isPast()) but serializes as a plain date, consistent with
        // other date fields in the API (e.g. estimated_date_of_arrival)
        // instead of a full ISO datetime with a misleading midnight time.
        'policy_expiry_date' => 'date:Y-m-d',
    ];

    /**
     * A null expiry means no policy is on file yet - not the same as an
     * expired one - and does not block anything. See docs/adr for the
     * dispatch-creation policy check this backs.
     */
    public function isPolicyExpired(): bool
    {
        return $this->policy_expiry_date !== null
            && $this->policy_expiry_date->isPast();
    }

    public function channelPartner(): BelongsTo
    {
        return $this->belongsTo(ChannelPartner::class);
    }

    public function phases(): HasMany
    {
        return $this->hasMany(Phase::class, "group_id");
    }
}

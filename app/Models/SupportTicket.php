<?php

namespace App\Models;

use App\Support\GroupVisibility;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupportTicket extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const STATUSES = ['open', 'in_progress', 'resolved', 'closed'];

    public const PENDING_STATUSES = ['open', 'in_progress'];

    protected $fillable = [
        'ticket_id',
        'subject',
        'description',
        'status',
        'cargo_detail_id',
        'group_id',
        'user_id',
    ];

    protected static function booted(): void
    {
        static::creating(function (SupportTicket $ticket) {
            $ticket->status ??= 'open';
        });

        static::created(function (SupportTicket $ticket) {
            $ticket->update(['ticket_id' => sprintf('TCK-%06d', $ticket->id)]);
        });
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function cargoDetail(): BelongsTo
    {
        return $this->belongsTo(CargoDetail::class);
    }

    public function raiser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeVisibleTo($query, User $user)
    {
        return GroupVisibility::apply($query, $user);
    }
}

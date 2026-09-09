<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FastagTransaction extends Model
{
    use HasFactory;

    protected $fillable = ['cargo_detail_id', 'payload', 'dedupe_hash', 'fetched_at'];

    protected $casts = [
        'payload' => 'array',
        'fetched_at' => 'datetime',
    ];

    public function cargoDetail(): BelongsTo
    {
        return $this->belongsTo(CargoDetail::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Checklist extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'question',
        'answer',
        'instruction',
        'cargo_id',
        'phase_id',
        'zone_id',
        'answer_type',
        'preferred_compliance',
        'is_sop_breached',
        // Add other fields as needed
    ];

    protected $casts = [
        "preferred_compliance" => "string",
    ];

    protected $primaryKey = 'id';

    // other fields for checklists

    public function cargo()
    {
        return $this->belongsTo(CargoDetail::class);
    }

    public function checklistPhotos()
    {
        return $this->hasMany(ChecklistPhoto::class);
    }

    public function phase()
    {
        return $this->belongsTo(Phase::class, "phase_id");
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class, 'zone_id');
    }
}

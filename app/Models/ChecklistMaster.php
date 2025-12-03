<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChecklistMaster extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'question',
        'instruction',
        'group_id',
        'phase_id',
        'zone_id',
        'answer',
        'preferred_compliance',
        'answer_type',
        // Add other fields as needed
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
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

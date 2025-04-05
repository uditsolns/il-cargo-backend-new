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
        'answer_type',
        // Add other fields as needed
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
}

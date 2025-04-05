<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChecklistMaster extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    protected $fillable = [
        'question',
        'instruction',
        'group_id',
        'answer',
        'answer_type',
        // Add other fields as needed
    ];
    
    
}

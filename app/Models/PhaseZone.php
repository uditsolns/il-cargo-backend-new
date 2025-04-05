<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhaseZone extends Model
{
    use HasFactory;
    
    protected  $fillable = ['user_id', 'zone_id', 'phase_id']; 
    
        public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    // Define the relationship with the Phase model
    public function phase()
    {
        return $this->belongsTo(Phase::class);
    }


}

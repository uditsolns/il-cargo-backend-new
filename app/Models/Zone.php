<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    use HasFactory;
    
  protected  $fillable = ['name', 'description', 'group_id', 'zone_id', 'phase_id']; 
    
    public function users()
    {
        return $this->belongsToMany(User::class, 'phase_zone', 'zone_id', 'user_id')->withPivot('phase_id');
    }
    
     public function group()
    {
        return $this->belongsTo(Group::class);
    }
    
    public function phase()
    {
        return $this->belongsTo(Phase::class);
    }
}

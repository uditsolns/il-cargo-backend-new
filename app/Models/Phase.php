<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Phase extends Model
{
    use HasFactory;
    
    protected  $fillable = ['name', 'description', 'group_id']; 
    
    public function users()
    {
        return $this->belongsToMany(User::class, 'phase_zone', 'phase_id', 'user_id')->withPivot('zone_id');
    }
    
     public function group()
    {
        return $this->belongsTo(Group::class);
    }
    
    public function zone()
    {
        return $this->hasMany(Zone::class);
    }
}

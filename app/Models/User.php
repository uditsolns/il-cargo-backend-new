<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Group;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'group_id',
        'phone',
        'channel_partner_id',
        'user_status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    public function group()
    {
        return $this->belongsTo(Group::class);
    }
    
    public function channel()
    {
        return $this->belongsTo(ChannelPartner::class);
    }
    
    public function zones()
    {
        return $this->belongsToMany(Zone::class, 'phase_zones', 'user_id', 'zone_id')->withPivot('phase_id');
    }

    public function phases()
    {
        return $this->belongsToMany(Phase::class, 'phase_zones', 'user_id', 'phase_id')->withPivot('zone_id');
    }
}

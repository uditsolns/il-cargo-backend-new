<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use HasFactory;
     use SoftDeletes;

    protected $fillable = [
        'name',
        'address',
        'city',
        'gst',
        'photo',
        'channel_partner_id',
        'additional_emails',
        // Add other fields as needed
    ];
    
    protected $casts = [
        'additional_emails' => 'array',
    ];
    
    public function channelPartner()
    {
        return $this->belongsTo(ChannelPartner::class);
    }
}

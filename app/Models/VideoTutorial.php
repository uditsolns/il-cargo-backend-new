<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoTutorial extends Model
{
    use HasFactory;

    protected $fillable = [
        "title",
        "description",
        "video_url",
        "is_active",
        "created_by",
    ];

    protected $casts = [
        "is_active" => "boolean",
    ];

    public function cargoDetails()
    {
        return $this->belongsToMany(
            CargoDetail::class,
            "cargo_detail_video",
        )->withTimestamps();
    }

    public function watchRecords()
    {
        return $this->hasMany(VideoWatchRecord::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, "created_by");
    }
}

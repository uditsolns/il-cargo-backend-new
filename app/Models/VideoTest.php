<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoTest extends Model
{
    use HasFactory;

    protected $fillable = [
        "video_tutorial_id",
        "pass_percentage",
        "is_active",
        "created_by",
    ];

    protected $casts = [
        "is_active" => "boolean",
        "pass_percentage" => "integer",
    ];

    public function videoTutorial()
    {
        return $this->belongsTo(VideoTutorial::class);
    }

    public function questions()
    {
        return $this->hasMany(VideoTestQuestion::class)->orderBy("position");
    }

    public function attempts()
    {
        return $this->hasMany(VideoTestAttempt::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, "created_by");
    }
}

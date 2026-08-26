<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoTestAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        "video_watch_record_id",
        "video_test_id",
        "score_percent",
        "passed",
        "submitted_at",
    ];

    protected $casts = [
        "score_percent" => "float",
        "passed" => "boolean",
        "submitted_at" => "datetime",
    ];

    public function videoWatchRecord()
    {
        return $this->belongsTo(VideoWatchRecord::class);
    }

    public function videoTest()
    {
        return $this->belongsTo(VideoTest::class);
    }

    public function answers()
    {
        return $this->hasMany(VideoTestAttemptAnswer::class);
    }
}

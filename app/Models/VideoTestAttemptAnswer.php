<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoTestAttemptAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        "video_test_attempt_id",
        "video_test_question_id",
        "selected_option_id",
        "is_correct",
    ];

    protected $casts = [
        "is_correct" => "boolean",
    ];

    public function attempt()
    {
        return $this->belongsTo(VideoTestAttempt::class, "video_test_attempt_id");
    }

    public function question()
    {
        return $this->belongsTo(VideoTestQuestion::class, "video_test_question_id");
    }

    public function selectedOption()
    {
        return $this->belongsTo(VideoTestQuestionOption::class, "selected_option_id");
    }
}

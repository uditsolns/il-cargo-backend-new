<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoTestQuestionOption extends Model
{
    use HasFactory;

    protected $fillable = [
        "video_test_question_id",
        "option_text",
        "is_correct",
        "position",
    ];

    protected $casts = [
        "is_correct" => "boolean",
    ];

    /**
     * Hidden by default - driver-facing endpoints must never leak the
     * answer key. Admin-facing endpoints that need it should use
     * ->makeVisible('is_correct') explicitly, so exposure is always a
     * deliberate choice at the call site.
     */
    protected $hidden = ["is_correct"];

    public function question()
    {
        return $this->belongsTo(VideoTestQuestion::class, "video_test_question_id");
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoTestQuestion extends Model
{
    use HasFactory;

    protected $fillable = ["video_test_id", "question_text", "position"];

    public function videoTest()
    {
        return $this->belongsTo(VideoTest::class);
    }

    public function options()
    {
        return $this->hasMany(VideoTestQuestionOption::class)->orderBy(
            "position",
        );
    }
}

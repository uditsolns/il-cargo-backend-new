<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoWatchRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        "driver_id",
        "video_tutorial_id",
        "first_required_by_cargo_id",
        "selfie_path",
        "selfie_captured_at",
        "latitude",
        "longitude",
        "started_at",
        "completed_at",
        "status",
        "is_assisted",
        "assisted_by_user_id",
    ];

    protected $casts = [
        "selfie_captured_at" => "datetime",
        "started_at" => "datetime",
        "completed_at" => "datetime",
        "is_assisted" => "boolean",
    ];
    protected $appends = ["selfie_url"];

    public function getSelfieUrlAttribute(): ?string
    {
        return $this->selfie_path
            ? Storage::disk("public")->url($this->selfie_path)
            : null;
    }

    public function driver()
    {
        return $this->belongsTo(User::class, "driver_id");
    }

    public function videoTutorial()
    {
        return $this->belongsTo(VideoTutorial::class);
    }

    public function firstRequiredByCargo()
    {
        return $this->belongsTo(
            CargoDetail::class,
            "first_required_by_cargo_id",
        );
    }

    public function assistedBy()
    {
        return $this->belongsTo(User::class, "assisted_by_user_id");
    }

    public function isCompleted(): bool
    {
        return $this->status === "completed";
    }
}

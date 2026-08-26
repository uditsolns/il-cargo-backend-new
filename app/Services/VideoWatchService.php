<?php

namespace App\Services;

use App\Models\CargoDetail;
use App\Models\User;
use App\Models\VideoTutorial;
use App\Models\VideoWatchRecord;
use Illuminate\Support\Facades\DB;

class VideoWatchService
{
    /**
     * Find or create a Driver-role user from the loose email/mobile fields
     * captured on the trip form. Mirrors the existing Consignee
     * auto-creation pattern already in CargoDetailController@store.
     */
    public function findOrCreateDriver(
        ?string $name,
        ?string $email,
        ?string $mobile,
    ): ?User {
        if (!$email || !$mobile) {
            return null;
        }

        $driver = User::where("email", $email)
            ->orWhere("phone", $mobile)
            ->first();

        if (!$driver) {
            $driver = User::create([
                "name" => $name,
                "email" => $email,
                "phone" => $mobile,
                "password" => bcrypt($mobile),
                "role" => "Driver",
                "is_admin" => 0,
            ]);
        }

        return $driver;
    }

    /**
     * Sync which videos are applicable to a trip and recalculate
     * that trip's driver_videos_status.
     */
    public function syncTripVideos(
        CargoDetail $cargo,
        array $videoTutorialIds,
    ): void {
        $cargo->videoTutorials()->sync($videoTutorialIds);
        $this->recalculateStatus($cargo->fresh());
    }

    /**
     * Recompute and persist driver_videos_status for a single trip.
     *   null      -> no videos assigned
     *   pending   -> videos assigned, not all completed yet (or no driver yet)
     *   completed -> driver has completed every assigned video
     */
    public function recalculateStatus(CargoDetail $cargo): void
    {
        $videoIds = $cargo->videoTutorials()->pluck("video_tutorials.id");

        if ($videoIds->isEmpty()) {
            $status = null;
        } elseif (!$cargo->driver_id) {
            $status = "pending";
        } else {
            $completedCount = VideoWatchRecord::where(
                "driver_id",
                $cargo->driver_id,
            )
                ->whereIn("video_tutorial_id", $videoIds)
                ->where("status", "completed")
                ->count();

            $status =
                $completedCount >= $videoIds->count() ? "completed" : "pending";
        }

        $cargo->update(["driver_videos_status" => $status]);
    }

    /**
     * A watch record is global per driver+video (not per trip), so completing
     * one video can flip the status of several trips at once. Recalculate all of them.
     */
    public function recalculateStatusForDriverVideo(
        int $driverId,
        int $videoTutorialId,
    ): void {
        CargoDetail::where("driver_id", $driverId)
            ->whereHas(
                "videoTutorials",
                fn($q) => $q->where("video_tutorials.id", $videoTutorialId),
            )
            ->get()
            ->each(fn(CargoDetail $cargo) => $this->recalculateStatus($cargo));
    }

    /**
     * Every video applicable to the driver's trips that isn't done yet -
     * one row per video, `status` tells the frontend what to do with it.
     * A video only ever reaches "watched" if it has a test attached
     * (completeWatch() takes a test-less video straight to "completed"),
     * so `status === "watched"` unambiguously means "take the test", and
     * anything else means "watch it" - no separate has-test flag needed.
     * A failed attempt resets the record to "not_started" (see
     * VideoTestService::submitAttempt()), which naturally brings the video
     * back into this same list rather than needing separate handling.
     */
    public function pendingVideoTutorialsForDriver(User $driver)
    {
        $videoIds = $this->applicableVideoIdsForDriver($driver);

        $records = VideoWatchRecord::where("driver_id", $driver->id)
            ->whereIn("video_tutorial_id", $videoIds)
            ->get()
            ->keyBy("video_tutorial_id");

        return VideoTutorial::where("is_active", true)
            ->whereIn("id", $videoIds)
            ->get(["id", "title", "description", "video_url"])
            ->map(function (VideoTutorial $video) use ($records) {
                $record = $records->get($video->id);

                return [
                    "id" => $video->id,
                    "title" => $video->title,
                    "description" => $video->description,
                    "video_url" => $video->video_url,
                    "status" => $record->status ?? "not_started",
                    "video_watch_record_id" => $record?->id,
                ];
            })
            ->reject(fn(array $video) => $video["status"] === "completed")
            ->values();
    }

    private function applicableVideoIdsForDriver(User $driver)
    {
        return DB::table("cargo_detail_video")
            ->whereIn(
                "cargo_detail_id",
                CargoDetail::where("driver_id", $driver->id)->pluck("id"),
            )
            ->pluck("video_tutorial_id")
            ->unique();
    }

    /**
     * A driver may only start a video that's actually applicable to
     * at least one of their trips.
     */
    public function isVideoApplicableToDriver(
        User $driver,
        VideoTutorial $video,
    ): bool {
        return CargoDetail::where("driver_id", $driver->id)
            ->whereHas(
                "videoTutorials",
                fn($q) => $q->where("video_tutorials.id", $video->id),
            )
            ->exists();
    }

    public function startWatch(
        User $driver,
        VideoTutorial $video,
        string $selfiePath,
        $latitude = null,
        $longitude = null,
        bool $assisted = false,
        ?User $assistedBy = null,
        ?CargoDetail $firstCargo = null,
    ): VideoWatchRecord {
        $existing = VideoWatchRecord::where("driver_id", $driver->id)
            ->where("video_tutorial_id", $video->id)
            ->first();

        if ($existing && $existing->isCompleted()) {
            abort(422, "This driver has already watched this video.");
        }

        return VideoWatchRecord::updateOrCreate(
            ["driver_id" => $driver->id, "video_tutorial_id" => $video->id],
            [
                "first_required_by_cargo_id" =>
                    $existing->first_required_by_cargo_id ?? $firstCargo?->id,
                "selfie_path" => $selfiePath,
                "selfie_captured_at" => now(),
                "latitude" => $latitude,
                "longitude" => $longitude,
                "started_at" => $existing->started_at ?? now(),
                "status" => "in_progress",
                "is_assisted" => $assisted,
                "assisted_by_user_id" => $assisted ? $assistedBy?->id : null,
            ],
        );
    }

    /**
     * Marks watching finished. If the video has no test, that's the whole
     * requirement and it goes straight to "completed" (unchanged prior
     * behavior). If it has a test, it stops at "watched" - "completed" is
     * only reached once VideoTestService::submitAttempt() records a pass.
     */
    public function completeWatch(
        User $driver,
        VideoTutorial $video,
    ): VideoWatchRecord {
        $record = VideoWatchRecord::where("driver_id", $driver->id)
            ->where("video_tutorial_id", $video->id)
            ->where("status", "in_progress")
            ->firstOrFail();

        $hasTest = $video->videoTest()->exists();

        $record->update([
            "status" => $hasTest ? "watched" : "completed",
            "watched_at" => now(),
            "completed_at" => $hasTest ? null : now(),
        ]);

        $this->recalculateStatusForDriverVideo($driver->id, $video->id);

        return $record;
    }
}

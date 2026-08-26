<?php

namespace App\Services;

use App\Models\VideoTest;
use App\Models\VideoTestAttempt;
use App\Models\VideoTutorial;
use App\Models\VideoWatchRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VideoTestService
{
    public function __construct(private VideoWatchService $videoWatchService) {}

    /**
     * Replace the test (and all its questions/options) attached to a video
     * in one shot. Tests aren't versioned yet, so editing one always
     * rewrites it wholesale rather than diffing question-by-question.
     * Passing null/empty removes the requirement entirely.
     */
    public function saveTestForVideo(
        VideoTutorial $video,
        ?array $testData,
    ): ?VideoTest {
        return DB::transaction(function () use ($video, $testData) {
            if (empty($testData)) {
                $video->videoTest?->delete();

                return null;
            }

            $test = $video->videoTest()->updateOrCreate([], [
                "pass_percentage" => $testData["pass_percentage"] ?? 50,
                "is_active" => true,
                "created_by" => Auth::id(),
            ]);

            $test->questions()->delete();

            foreach ($testData["questions"] as $questionIndex => $questionData) {
                $question = $test->questions()->create([
                    "question_text" => $questionData["question_text"],
                    "position" => $questionIndex,
                ]);

                foreach ($questionData["options"] as $optionIndex => $optionData) {
                    $question->options()->create([
                        "option_text" => $optionData["option_text"],
                        "is_correct" => (bool) $optionData["is_correct"],
                        "position" => $optionIndex,
                    ]);
                }
            }

            return $test->fresh(["questions.options"]);
        });
    }

    /**
     * Score and record a driver's attempt. `answers` is
     * [video_test_question_id => selected_option_id, ...], already
     * validated to cover every question in the test.
     */
    public function submitAttempt(
        VideoWatchRecord $watchRecord,
        VideoTest $test,
        array $answers,
    ): VideoTestAttempt {
        abort_if(
            $watchRecord->isCompleted(),
            422,
            "This test has already been passed.",
        );

        abort_unless(
            $watchRecord->status === "watched",
            422,
            "Watch the video before taking its test.",
        );

        $questions = $test->questions()->with("options")->get();

        return DB::transaction(function () use (
            $watchRecord,
            $test,
            $questions,
            $answers,
        ) {
            $correctCount = 0;

            $attempt = VideoTestAttempt::create([
                "video_watch_record_id" => $watchRecord->id,
                "video_test_id" => $test->id,
                "score_percent" => 0,
                "passed" => false,
                "submitted_at" => now(),
            ]);

            foreach ($questions as $question) {
                $selectedOptionId = $answers[$question->id];
                $selectedOption = $question->options->firstWhere(
                    "id",
                    $selectedOptionId,
                );
                $isCorrect = (bool) $selectedOption?->is_correct;
                $correctCount += (int) $isCorrect;

                $attempt->answers()->create([
                    "video_test_question_id" => $question->id,
                    "selected_option_id" => $selectedOptionId,
                    "is_correct" => $isCorrect,
                ]);
            }

            $scorePercent = round(($correctCount / max($questions->count(), 1)) * 100, 2);
            $passed = $scorePercent > $test->pass_percentage;

            $attempt->update([
                "score_percent" => $scorePercent,
                "passed" => $passed,
            ]);

            if ($passed) {
                $watchRecord->update([
                    "status" => "completed",
                    "completed_at" => now(),
                ]);

                $this->videoWatchService->recalculateStatusForDriverVideo(
                    $watchRecord->driver_id,
                    $watchRecord->video_tutorial_id,
                );
            } else {
                // Failing sends the driver back to rewatch the video before
                // retaking the test - selfie/watch evidence must be
                // recaptured fresh, not reused from the failed attempt.
                // startWatch() treats this exactly like a never-started
                // video, so no other code path needs to change.
                $watchRecord->update([
                    "status" => "not_started",
                    "started_at" => null,
                    "watched_at" => null,
                    "selfie_path" => null,
                    "selfie_captured_at" => null,
                    "latitude" => null,
                    "longitude" => null,
                ]);
            }

            return $attempt->fresh("answers");
        });
    }
}

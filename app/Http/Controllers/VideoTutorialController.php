<?php

namespace App\Http\Controllers;

use App\Models\VideoTutorial;
use App\Services\VideoTestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class VideoTutorialController extends Controller
{
    public function __construct(private VideoTestService $videoTestService) {}

    public function index()
    {
        return response()->json([
            "video_tutorials" => VideoTutorial::with("videoTest")
                ->latest()
                ->get(),
        ]);
    }

    /**
     * Authoring/editing the test requires seeing which option is correct -
     * VideoTestQuestionOption hides `is_correct` by default for
     * driver-facing endpoints, so admin responses must opt back in
     * explicitly.
     */
    private function withAnswerKeys(VideoTutorial $video): VideoTutorial
    {
        $video->videoTest?->questions?->each(
            fn($question) => $question->options->makeVisible("is_correct"),
        );

        return $video;
    }

    public function store(Request $request)
    {
        $fields = Validator::make($request->all(), $this->rules());
        $fields->after(fn($validator) => $this->validateTestAnswerKeys($validator, $request));

        if ($fields->fails()) {
            return response()->json(["error" => $fields->errors()], 422);
        }

        $video = VideoTutorial::create([
            "title" => $request->input("title"),
            "description" => $request->input("description"),
            "video_url" => $request->input("video_url"),
            "is_active" => $request->input("is_active", true),
            "created_by" => Auth::id(),
        ]);

        $this->videoTestService->saveTestForVideo(
            $video,
            $request->input("test"),
        );

        return response()->json([
            "video_tutorial" => $this->withAnswerKeys(
                $video->load("videoTest.questions.options"),
            ),
        ], 201);
    }

    public function show($id)
    {
        $video = VideoTutorial::with("videoTest.questions.options")->findOrFail(
            $id,
        );

        return response()->json([
            "video_tutorial" => $this->withAnswerKeys($video),
        ]);
    }

    public function update(Request $request, $id)
    {
        $video = VideoTutorial::findOrFail($id);

        $fields = Validator::make($request->all(), $this->rules(sometimes: true));
        $fields->after(fn($validator) => $this->validateTestAnswerKeys($validator, $request));

        if ($fields->fails()) {
            return response()->json(["error" => $fields->errors()], 422);
        }

        $video->fill(
            $request->only(["title", "description", "video_url", "is_active"]),
        );
        $video->save();

        if ($request->has("test")) {
            $this->videoTestService->saveTestForVideo(
                $video,
                $request->input("test"),
            );
        }

        return response()->json([
            "video_tutorial" => $this->withAnswerKeys(
                $video->load("videoTest.questions.options"),
            ),
        ]);
    }

    public function destroy($id)
    {
        VideoTutorial::findOrFail($id)->delete();

        return response()->json(null, 204);
    }

    private function rules(bool $sometimes = false): array
    {
        return [
            "title" => $sometimes ? "sometimes|required|string" : "required|string",
            "description" => "nullable|string",
            "video_url" => $sometimes ? "sometimes|required|url" : "required|url",
            "is_active" => "boolean",

            // Test is optional - a video may have no comprehension check at all.
            "test" => "nullable|array",
            "test.pass_percentage" => "sometimes|integer|min:1|max:100",
            "test.questions" => "required_with:test|array|min:1",
            "test.questions.*.question_text" => "required|string",
            "test.questions.*.options" => "required|array|size:4",
            "test.questions.*.options.*.option_text" => "required|string",
            "test.questions.*.options.*.is_correct" => "required|boolean",
        ];
    }

    /**
     * "Exactly one correct option per question" can't be expressed with
     * Laravel's built-in rules, so it's checked here.
     */
    private function validateTestAnswerKeys($validator, Request $request): void
    {
        $questions = $request->input("test.questions", []);

        foreach ($questions as $index => $question) {
            $correctCount = collect($question["options"] ?? [])
                ->filter(fn($option) => (bool) ($option["is_correct"] ?? false))
                ->count();

            if ($correctCount !== 1) {
                $validator->errors()->add(
                    "test.questions.{$index}.options",
                    "Question " . ($index + 1) . " must have exactly one correct option.",
                );
            }
        }
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideoTestAttemptAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('video_test_attempt_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('video_test_attempt_id')->constrained('video_test_attempts')->cascadeOnDelete();
            $table->foreignId('video_test_question_id')->constrained('video_test_questions')->cascadeOnDelete();
            $table->foreignId('selected_option_id')->constrained('video_test_question_options')->cascadeOnDelete();
            // Snapshotted at answer time, not recomputed via the option's current is_correct flag.
            $table->boolean('is_correct');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('video_test_attempt_answers');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideoTestQuestionOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * "Exactly one correct option per question" is enforced in the
     * application layer (VideoTestService), not here - a DB-level check
     * across sibling rows needs a trigger, which isn't worth it for v1.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('video_test_question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('video_test_question_id')->constrained('video_test_questions')->cascadeOnDelete();
            $table->string('option_text');
            $table->boolean('is_correct')->default(false);
            $table->unsignedInteger('position')->default(0);
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
        Schema::dropIfExists('video_test_question_options');
    }
}

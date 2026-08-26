<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideoTestAttemptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('video_test_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('video_watch_record_id')->constrained('video_watch_records')->cascadeOnDelete();
            $table->foreignId('video_test_id')->constrained('video_tests')->cascadeOnDelete();
            $table->decimal('score_percent', 5, 2);
            $table->boolean('passed');
            $table->timestamp('submitted_at');
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
        Schema::dropIfExists('video_test_attempts');
    }
}

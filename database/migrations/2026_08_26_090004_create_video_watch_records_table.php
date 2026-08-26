<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideoWatchRecordsTable extends Migration
{
    /**
     * This table already exists in shipped environments. Guarded so this
     * migration is safe to run anywhere. Includes the new `watched_at`
     * column and the extended `status` step ("watched") that the MCQ test
     * feature introduces between "watching finished" and "understanding
     * confirmed".
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('video_watch_records')) {
            $this->addWatchedAtColumnIfMissing();

            return;
        }

        Schema::create('video_watch_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('video_tutorial_id')->constrained('video_tutorials')->cascadeOnDelete();
            $table->foreignId('first_required_by_cargo_id')->nullable()->constrained('cargo_details')->nullOnDelete();
            $table->string('selfie_path')->nullable();
            $table->timestamp('selfie_captured_at')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('watched_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            // not_started -> in_progress -> watched -> completed
            // "completed" is only reached once a linked test (if any) is passed.
            $table->string('status')->default('not_started');
            $table->boolean('is_assisted')->default(false);
            $table->foreignId('assisted_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['driver_id', 'video_tutorial_id']);
        });
    }

    private function addWatchedAtColumnIfMissing()
    {
        if (Schema::hasColumn('video_watch_records', 'watched_at')) {
            return;
        }

        Schema::table('video_watch_records', function (Blueprint $table) {
            $table->timestamp('watched_at')->nullable()->after('started_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('video_watch_records');
    }
}

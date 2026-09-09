<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContainerTrackingEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('container_tracking_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cargo_detail_id')->constrained('cargo_details')->cascadeOnDelete();
            $table->json('payload');
            $table->string('dedupe_hash', 64);
            $table->timestamp('fetched_at');
            $table->timestamps();

            $table->unique(['cargo_detail_id', 'dedupe_hash']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('container_tracking_events');
    }
}

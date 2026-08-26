<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCargoDetailVideoTable extends Migration
{
    /**
     * Pivot table already exists in shipped environments. Guarded so this
     * migration is safe to run anywhere.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('cargo_detail_video')) {
            return;
        }

        Schema::create('cargo_detail_video', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cargo_detail_id')->constrained('cargo_details')->cascadeOnDelete();
            $table->foreignId('video_tutorial_id')->constrained('video_tutorials')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['cargo_detail_id', 'video_tutorial_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cargo_detail_video');
    }
}

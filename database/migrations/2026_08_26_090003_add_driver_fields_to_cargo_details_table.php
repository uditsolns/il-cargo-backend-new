<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDriverFieldsToCargoDetailsTable extends Migration
{
    /**
     * These columns already exist in shipped environments (added outside of
     * migrations). Guarded per-column so this migration is safe to run
     * anywhere.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cargo_details', function (Blueprint $table) {
            if (!Schema::hasColumn('cargo_details', 'driver_id')) {
                $table->foreignId('driver_id')->nullable()->after('consignee_id')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('cargo_details', 'driver_email')) {
                $table->string('driver_email')->nullable()->after('driver_id');
            }
            if (!Schema::hasColumn('cargo_details', 'driver_mobile_no')) {
                $table->string('driver_mobile_no')->nullable()->after('driver_email');
            }
            if (!Schema::hasColumn('cargo_details', 'driver_videos_status')) {
                // Advisory rollup only ("pending"/"completed") - not a dispatch gate.
                $table->string('driver_videos_status')->nullable()->after('driver_mobile_no');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cargo_details', function (Blueprint $table) {
            $table->dropConstrainedForeignId('driver_id');
            $table->dropColumn(['driver_email', 'driver_mobile_no', 'driver_videos_status']);
        });
    }
}

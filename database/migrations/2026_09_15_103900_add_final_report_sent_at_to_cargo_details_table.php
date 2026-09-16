<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddFinalReportSentAtToCargoDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cargo_details', function (Blueprint $table) {
            // Set once the completion (final report) email has actually
            // been sent for this dispatch, so CargoCompleted's scheduled
            // job (every minute) sends it exactly once instead of
            // re-sending on every subsequent run while pending_servey
            // stays at 1.
            $table->timestamp('final_report_sent_at')->nullable()->after('pending_servey');
        });

        // Dispatches that were already completed before this feature
        // shipped should not suddenly get a "trip completed" email on
        // deploy - only trips that complete from here on should. Mark
        // every already-completed dispatch as already handled.
        DB::table('cargo_details')
            ->where('pending_servey', 1)
            ->update(['final_report_sent_at' => now()]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cargo_details', function (Blueprint $table) {
            $table->dropColumn('final_report_sent_at');
        });
    }
}

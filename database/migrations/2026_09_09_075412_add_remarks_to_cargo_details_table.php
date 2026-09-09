<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRemarksToCargoDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cargo_details', function (Blueprint $table) {
            // Never tracked by a migration despite being in CargoDetail's
            // $fillable and used by store()/update() since day one - added
            // directly to production outside the migration system, same
            // pattern as several other undocumented columns in this app.
            // Backfilling it here so this (and the already-pending
            // add_verification_fields migration, which positions its
            // columns ->after('remarks')) can actually run on a dev DB
            // that was never manually patched to match.
            $table->text('remarks')->nullable()->after('destination_address');
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
            $table->dropColumn('remarks');
        });
    }
}

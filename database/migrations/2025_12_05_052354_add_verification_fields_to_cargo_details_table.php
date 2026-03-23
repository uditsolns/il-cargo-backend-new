<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVerificationFieldsToCargoDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cargo_details', function (Blueprint $table) {
            $table->string('dl_no')->nullable()->after('remarks');
            $table->date('dl_dob')->nullable()->after('dl_no');
            $table->string('driver_aadhaar_no')->nullable()->after('dl_dob');
            $table->boolean('is_rc_verified')->default(false)->after('driver_aadhaar_no');
            $table->boolean('is_dl_verified')->default(false)->after('is_rc_verified');
            $table->boolean('is_aadhaar_verified')->default(false)->after('is_dl_verified');
            $table->boolean('is_verification_done')->default(false)->after('is_aadhaar_verified');

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
            $table->dropColumn('dl_no');
            $table->dropColumn('dl_dob');
            $table->dropColumn('driver_aadhaar_no');
            $table->dropColumn('is_rc_verified');
            $table->dropColumn('is_dl_verified');
            $table->dropColumn('is_aadhaar_verified');
            $table->dropColumn('is_verification_done');
        });
    }
}

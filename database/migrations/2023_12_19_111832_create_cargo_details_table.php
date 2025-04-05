<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCargoDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cargo_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('trip_id');
            $table->json('veh_reg_no');  // JSON field for vehicle registration number
            $table->string('cargo_unit_serial_no', 20);
            $table->string('driver_lic_no', 20);
            $table->json('veh_fitness_cert');  // JSON field for vehicle fitness certificate
            $table->json('veh_carrying_capacity');  // JSON field for vehicle carrying capacity
            $table->string('invoice', 20);
            $table->string('packing_list', 20);
            $table->string('serial_no', 20);
            $table->string('invoice_value', 20);
            $table->decimal('dispatch_lat', 10, 6);
            $table->decimal('dispatch_long', 10, 6);
            $table->decimal('destination_long', 10, 6);
            $table->decimal('destination_lat', 10, 6);
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
        Schema::dropIfExists('cargo_details');
    }
}

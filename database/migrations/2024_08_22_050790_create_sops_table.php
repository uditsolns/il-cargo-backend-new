<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('sops', function (Blueprint $table) {
            $table->id();

            $table->string("pdf");
            $table->string("expiry_date");
            $table->string("status")->nullable();

            $table->foreignId("customer_id")->constrained("customers")
                ->cascadeOnUpdate()->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('sops');
    }
};

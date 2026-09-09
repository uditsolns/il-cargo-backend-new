<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFastagTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fastag_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cargo_detail_id')->constrained('cargo_details')->cascadeOnDelete();
            $table->json('payload');
            // Dedupe key: the government payload's real field names aren't
            // modeled anywhere yet (ulip-apis passes them through
            // untouched), so this hashes the normalized entry itself
            // rather than relying on a specific transaction-id field.
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
        Schema::dropIfExists('fastag_transactions');
    }
}

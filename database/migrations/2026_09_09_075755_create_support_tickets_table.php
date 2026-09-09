<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupportTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            // Nullable: backfilled with a sequential 'TCK-000123' reference
            // right after insert, once the id is known - see
            // SupportTicket::booted()'s `created` hook.
            $table->string('ticket_id')->nullable()->unique();
            $table->string('subject');
            $table->text('description')->nullable();
            $table->string('status')->default('open'); // open|in_progress|resolved|closed
            $table->foreignId('cargo_detail_id')->nullable()->constrained('cargo_details')->nullOnDelete();
            $table->foreignId('group_id')->nullable()->constrained('groups')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['group_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('support_tickets');
    }
}

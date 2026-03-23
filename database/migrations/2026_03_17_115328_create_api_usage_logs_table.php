<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_usage_logs', function (Blueprint $table) {
            $table->id();

            // Who made the call
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // What was called
            $table->string('service')->default('APIClub');
            $table->string('endpoint');

            // Sanitized request payload — sensitive fields are masked before storage
            $table->json('request_payload')->nullable();

            // Outcome
            $table->unsignedSmallInteger('http_status');
            $table->boolean('success');
            $table->json('response_summary')->nullable();

            // Performance
            $table->unsignedInteger('latency_ms');

            // Context
            $table->ipAddress('ip_address')->nullable();
            $table->timestamp('requested_at');

            $table->timestamps();

            // Indexes for report queries
            $table->index(['endpoint', 'requested_at']);
            $table->index(['user_id', 'requested_at']);
            $table->index(['success', 'requested_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_usage_logs');
    }
};

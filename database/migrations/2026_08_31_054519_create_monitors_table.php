<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monitors', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('name');
            $table->string('email')->nullable();
            $table->string('mobile')->nullable();
            $table->string('url')->nullable();

            $table->unsignedInteger('check_interval')->default(60);

            $table->timestamp('last_checked_at')->nullable();
            $table->timestamp('last_up_at')->nullable();
            $table->timestamp('last_down_at')->nullable();

            $table->decimal('uptime_percentage', 5, 2)->default(100);

            $table->string('status')->nullable();

            // No ->after() here
            $table->unsignedInteger('response_time')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monitors');
    }
};
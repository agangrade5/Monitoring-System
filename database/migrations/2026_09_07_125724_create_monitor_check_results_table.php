<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('monitor_check_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monitor_id')->constrained()->cascadeOnDelete();
            $table->string('php_version')->nullable();
            $table->string('php_status')->nullable();
            $table->timestamp('php_checked_at')->nullable();
            $table->string('domain_status')->nullable();
            $table->date('domain_expires_at')->nullable();
            $table->integer('domain_days_remaining')->nullable();
            $table->timestamp('domain_checked_at')->nullable();
            $table->string('domain_registrar')->nullable();
            $table->string('ssl_status')->nullable();
            $table->boolean('ssl_enabled')->default(false);    
            $table->integer('ssl_days_remaining')->nullable();
            $table->date('ssl_expires_at')->nullable();
            $table->string('ssl_issuer')->nullable();
            $table->string('security_grade')->nullable();
            $table->string('server_info')->nullable();
            $table->string('open_ports')->nullable();
            $table->json('security_headers')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitor_check_results');
    }
};

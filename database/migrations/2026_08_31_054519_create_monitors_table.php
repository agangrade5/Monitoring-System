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
            $table->string('php_version')->nullable();
            $table->string('php_status')->nullable();
            $table->timestamp('php_checked_at')->nullable();
            $table->string('domain_status')->nullable();
            $table->date('domain_expires_at')->nullable()->after('domain_status');
            $table->timestamp('domain_checked_at')->nullable();
            $table->string('ssl_status')->nullable()->after('domain_checked_at');
            $table->boolean('ssl_enabled')->default(false);    
            $table->integer('ssl_days_remaining')->nullable()->after('ssl_enabled');                                         
            $table->date('ssl_expires_at')->nullable()->after('ssl_days_remaining');
            $table->string('ssl_issuer')->nullable()->after('ssl_expires_at');
            $table->string('security_grade')->nullable()->after('ssl_issuer');
            $table->string('server_info')->nullable()->after('security_grade');
            $table->string('open_ports')->nullable()->after('server_info');
            $table->json('security_headers')->nullable();
            $table->unsignedInteger('check_interval')->default(60);
            $table->timestamp('last_checked_at')->nullable();
            $table->timestamp('last_up_at')->nullable();
            $table->timestamp('last_down_at')->nullable();
            $table->decimal('uptime_percentage', 5, 2)->default(100);
            $table->string('status')->nullable();
            // No ->after() here
            
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monitors');
    }
};
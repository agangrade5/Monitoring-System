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
        Schema::table('monitors', function (Blueprint $table) {
            $table->string('ssl_status')->nullable()->after('response_time');
            $table->boolean('ssl_enabled')->default(false);    
            $table->integer('ssl_days_remaining')->nullable()->after('ssl_enabled');                                         
            $table->date('ssl_expires_at')->nullable()->after('ssl_days_remaining');
            $table->string('ssl_issuer')->nullable()->after('ssl_expires_at');
            $table->date('domain_expires_at')->nullable()->after('ssl_issuer');
            $table->string('security_grade')->nullable()->after('domain_expires_at');
            $table->string('server_info')->nullable()->after('security_grade');
            $table->string('open_ports')->nullable()->after('server_info');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monitors', function (Blueprint $table) {
            $table->dropColumn([
                'response_time',
                'ssl_status',
                'ssl_expires_at',
                'ssl_issuer',
                'domain_expires_at',
                'security_grade',
                'server_info',
                'open_ports',
            ]);
        });
    }
};

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
            $table->boolean('check_uptime')->default(true)->after('url');
            $table->boolean('check_ssl')->default(true)->after('check_uptime');
            $table->boolean('check_php')->default(true)->after('check_ssl');
            $table->boolean('check_domain')->default(true)->after('check_php');
            $table->boolean('check_security_headers')->default(true)->after('check_domain');
        });
    }

    public function down(): void
    {
        Schema::table('monitors', function (Blueprint $table) {
            $table->dropColumn([
                'check_uptime',
                'check_ssl',
                'check_php',
                'check_domain',
                'check_security_headers'
            ]);
        });
    }
};

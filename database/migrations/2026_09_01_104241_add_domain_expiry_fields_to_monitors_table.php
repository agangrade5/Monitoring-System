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
            $table->string('domain_status')->nullable();
            $table->timestamp('domain_checked_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('monitors', function (Blueprint $table) {
            $table->dropColumn([
                'domain_status',
                'domain_checked_at',
            ]);
        });
    }
};

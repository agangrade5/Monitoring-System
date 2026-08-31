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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();

            $table->boolean('email_notification')->default(true);
            $table->boolean('sms_notification')->default(false);
            $table->boolean('two_factor_authentication')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};

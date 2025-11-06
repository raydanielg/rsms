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
        Schema::table('emas_centers', function (Blueprint $table) {
            $table->string('coordinator_username')->nullable()->after('coordinator_phone');
            $table->string('coordinator_password')->nullable()->after('coordinator_username');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('emas_centers', function (Blueprint $table) {
            $table->dropColumn(['coordinator_username', 'coordinator_password']);
        });
    }
};

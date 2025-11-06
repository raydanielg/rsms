<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Note: This migration is informational only.
     * The 'marker' role is now supported in the application logic.
     * The existing 'role' column already accepts any string value,
     * so no database schema changes are needed.
     */
    public function up(): void
    {
        // No database changes needed
        // The role column already exists and can accept 'marker' as a value
        // Role validation is handled in the application layer
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No changes to revert
    }
};

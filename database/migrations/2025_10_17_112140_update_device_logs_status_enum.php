<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Raw SQL to update the enum type to include 'unknown'
        DB::statement("ALTER TABLE device_logs MODIFY COLUMN status ENUM('up', 'down', 'unknown') DEFAULT 'unknown'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to the original enum values
        DB::statement("ALTER TABLE device_logs MODIFY COLUMN status ENUM('up', 'down') DEFAULT 'down'");
    }
};

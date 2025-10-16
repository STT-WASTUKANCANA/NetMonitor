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
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('ip_address');
            $table->enum('type', ['router', 'switch', 'access_point', 'server', 'other'])->default('other');
            $table->enum('hierarchy_level', ['utama', 'sub', 'device'])->default('device');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('location')->nullable();
            $table->enum('status', ['up', 'down', 'unknown'])->default('unknown');
            $table->decimal('response_time', 8, 2)->nullable(); // in milliseconds
            $table->timestamp('last_checked_at')->nullable();
            $table->timestamps();
            
            $table->foreign('parent_id')->references('id')->on('devices')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};

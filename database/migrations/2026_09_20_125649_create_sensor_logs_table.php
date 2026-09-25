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
    Schema::create('sensor_logs', function (Blueprint $table) {
        $table->id();

        $table->foreignId('device_id')
            ->constrained()
            ->restrictOnDelete();

        $table->dateTime('measured_at');

        $table->decimal('temperature', 5, 2)->nullable();
        $table->decimal('humidity', 5, 2)->nullable();
        $table->decimal('water_temperature', 5, 2)->nullable();
        $table->decimal('ec', 6, 3)->nullable();
        $table->decimal('ph', 4, 2)->nullable();
        $table->unsignedInteger('light')->nullable();
        $table->decimal('water_level', 6, 2)->nullable();

        $table->timestamps();

        $table->index(['device_id', 'measured_at']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensor_logs');
    }
};

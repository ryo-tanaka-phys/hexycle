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
    Schema::create('event_programs', function (Blueprint $table) {
        $table->id();

        $table->foreignId('event_id')
            ->constrained()
            ->cascadeOnDelete();

        $table->string('title');
        $table->text('description')->nullable();

        $table->dateTime('start_at')->nullable();
        $table->dateTime('end_at')->nullable();

        $table->string('location')->nullable();

        $table->unsignedInteger('capacity')->nullable();

        $table->string('status')->default('draft');

        $table->string('image_path')->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_programs');
    }
};

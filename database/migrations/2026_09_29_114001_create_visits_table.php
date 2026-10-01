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
    Schema::create('visits', function (Blueprint $table) {
        $table->id();

        $table->foreignId('event_program_id')
            ->constrained()
            ->cascadeOnDelete();

        $table->foreignId('user_id')
            ->nullable()
            ->constrained()
            ->nullOnDelete();

        $table->string('guest_name')->nullable();

        $table->dateTime('entered_at');
        $table->dateTime('exited_at')->nullable();

        $table->string('status')->default('inside');

        $table->string('check_in_code')
            ->nullable()
            ->unique();

        $table->timestamps();

        $table->index([
            'event_program_id',
            'status',
        ]);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};

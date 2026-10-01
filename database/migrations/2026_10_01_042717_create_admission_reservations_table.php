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
    Schema::create('admission_reservations', function (Blueprint $table) {
        $table->id();

        $table->foreignId('event_program_id')
            ->constrained()
            ->cascadeOnDelete();

        $table->foreignId('user_id')
            ->nullable()
            ->constrained()
            ->nullOnDelete();

        $table->string('guest_name')->nullable();

        $table->dateTime('slot_start');
        $table->dateTime('slot_end');

        $table->unsignedInteger('party_size')->default(1);

        $table->string('status')->default('reserved');

        $table->string('reservation_code')
            ->unique();

        $table->timestamps();

        $table->index([
            'event_program_id',
            'slot_start',
            'status',
        ]);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admission_reservations');
    }
};

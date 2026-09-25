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
    Schema::create('event_products', function (Blueprint $table) {
        $table->id();

        $table->foreignId('event_id')
            ->constrained()
            ->cascadeOnDelete();

        $table->foreignId('product_id')
            ->constrained()
            ->restrictOnDelete();

        $table->unsignedInteger('price');
        $table->unsignedInteger('stock');

        $table->unsignedInteger('reservation_limit')->nullable();

        $table->boolean('is_available')->default(true);

        $table->timestamps();

        $table->unique(['event_id', 'product_id']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_products');
    }
};

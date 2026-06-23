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
        Schema::create('kasir_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kasir_session_id')->constrained('kasir_sessions')->cascadeOnDelete();
            $table->foreignId('hpp_product_id')->nullable()->constrained('hpp_products')->nullOnDelete();
            $table->string('product_name', 120);
            $table->decimal('product_price', 12, 2);
            $table->integer('quantity')->default(1);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kasir_items');
    }
};

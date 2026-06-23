<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_sales', function (Blueprint $table) {
            $table->id();
            $table->date('sale_date');
            $table->string('shift');
            $table->foreignId('hpp_product_id')->constrained('hpp_products');
            $table->string('product_name');
            $table->decimal('unit_price', 15, 2);
            $table->decimal('hpp_per_unit', 15, 2);
            $table->integer('quantity_sold');
            $table->decimal('subtotal', 15, 2);
            $table->decimal('hpp_total', 15, 2);
            $table->decimal('profit', 15, 2);
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_sales');
    }
};

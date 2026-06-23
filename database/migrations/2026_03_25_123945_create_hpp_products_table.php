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
        Schema::create('hpp_products', function (Blueprint $table) {
            $table->id();
            $table->string('name');                          // Nama produk (Kopi Susu, Americano, dll)
            $table->string('category')->nullable();          // Kategori (minuman panas, dingin, makanan)
            $table->decimal('bahan_baku', 12, 2)->default(0);       // Biaya bahan baku per unit
            $table->decimal('tenaga_kerja', 12, 2)->default(0);     // Biaya tenaga kerja per unit
            $table->decimal('overhead', 12, 2)->default(0);         // Biaya overhead per unit
            $table->decimal('harga_jual', 12, 2)->default(0);       // Harga jual ke konsumen
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hpp_products');
    }
};

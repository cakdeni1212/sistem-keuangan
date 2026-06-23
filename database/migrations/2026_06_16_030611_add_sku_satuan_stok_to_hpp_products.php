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
        Schema::table('hpp_products', function (Blueprint $table) {
            $table->string('sku')->nullable()->after('name');
            $table->string('satuan')->nullable()->after('category');
            $table->integer('stok_minimum')->default(0)->after('satuan');
        });
    }

    public function down(): void
    {
        Schema::table('hpp_products', function (Blueprint $table) {
            $table->dropColumn(['sku', 'satuan', 'stok_minimum']);
        });
    }
};

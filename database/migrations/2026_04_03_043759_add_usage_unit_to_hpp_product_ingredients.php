<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hpp_product_ingredients', function (Blueprint $table) {
            $table->string('usage_unit', 20)->nullable()->after('quantity');
        });
    }

    public function down(): void
    {
        Schema::table('hpp_product_ingredients', function (Blueprint $table) {
            $table->dropColumn('usage_unit');
        });
    }
};

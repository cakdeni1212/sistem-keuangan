<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tax_rates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 10)->unique();
            $table->decimal('rate', 5, 2); // percentage, e.g. 11.00 for PPN 11%
            $table->enum('type', ['ppn', 'pph21', 'pph22', 'pph23', 'pph25', 'pph_final', 'other']);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('tax_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tax_rate_id')->constrained('tax_rates');
            $table->decimal('base_amount', 15, 2);
            $table->decimal('tax_amount', 15, 2);
            $table->date('transaction_date');
            $table->string('reference')->nullable();
            $table->nullableMorphs('taxable'); // polymorphic to link to journal/invoice
            $table->foreignId('journal_id')->nullable()->constrained('journals')->nullOnDelete();
            $table->boolean('is_paid')->default(false);
            $table->date('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_transactions');
        Schema::dropIfExists('tax_rates');
    }
};

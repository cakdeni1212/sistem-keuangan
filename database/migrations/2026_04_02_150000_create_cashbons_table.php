<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cashbons', function (Blueprint $table) {
            $table->id();
            $table->string('debtor_name', 100);
            $table->enum('debtor_type', ['karyawan', 'pelanggan', 'supplier', 'lainnya'])->default('lainnya');
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->decimal('amount', 15, 2);
            $table->text('description')->nullable();
            $table->date('debt_date');
            $table->date('due_date')->nullable();
            $table->date('paid_at')->nullable();
            $table->enum('status', ['belum_bayar', 'lunas'])->default('belum_bayar');
            $table->text('notes')->nullable();
            $table->foreignId('out_transaction_id')->nullable()->constrained('transactions')->nullOnDelete();
            $table->foreignId('in_transaction_id')->nullable()->constrained('transactions')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cashbons');
    }
};

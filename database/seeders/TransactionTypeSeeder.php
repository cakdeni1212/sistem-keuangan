<?php

namespace Database\Seeders;

use App\Models\TransactionType;
use Illuminate\Database\Seeder;

class TransactionTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            // Pengeluaran
            ['name' => 'Gas',           'category' => 'pengeluaran', 'description' => 'Pembelian gas LPG'],
            ['name' => 'Kopi',          'category' => 'pengeluaran', 'description' => 'Pembelian kopi'],
            ['name' => 'Susu',          'category' => 'pengeluaran', 'description' => 'Pembelian susu'],
            ['name' => 'Listrik',       'category' => 'pengeluaran', 'description' => 'Tagihan listrik PLN'],
            ['name' => 'Air',           'category' => 'pengeluaran', 'description' => 'Tagihan air PDAM'],
            ['name' => 'Internet',      'category' => 'pengeluaran', 'description' => 'Tagihan internet'],
            ['name' => 'Transportasi',  'category' => 'pengeluaran', 'description' => 'Biaya transportasi'],
            ['name' => 'Gaji Karyawan', 'category' => 'pengeluaran', 'description' => 'Pembayaran gaji karyawan'],
            ['name' => 'Alat Tulis',    'category' => 'pengeluaran', 'description' => 'Pembelian alat tulis kantor'],
            ['name' => 'Lain-lain (Pengeluaran)', 'category' => 'pengeluaran', 'description' => 'Pengeluaran lain-lain'],
            // Pemasukan
            ['name' => 'Penjualan',     'category' => 'pemasukan',   'description' => 'Pendapatan dari penjualan'],
            ['name' => 'Jasa',          'category' => 'pemasukan',   'description' => 'Pendapatan dari jasa'],
            ['name' => 'Investasi',     'category' => 'pemasukan',   'description' => 'Hasil investasi'],
            ['name' => 'Bunga Bank',    'category' => 'pemasukan',   'description' => 'Bunga simpanan bank'],
            ['name' => 'Lain-lain (Pemasukan)', 'category' => 'pemasukan', 'description' => 'Pemasukan lain-lain'],
        ];

        foreach ($types as $type) {
            TransactionType::firstOrCreate(['name' => $type['name'], 'category' => $type['category']], $type);
        }
    }
}

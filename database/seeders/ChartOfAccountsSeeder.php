<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\FiscalPeriod;
use Illuminate\Database\Seeder;

class ChartOfAccountsSeeder extends Seeder
{
    public function run(): void
    {
        // Create fiscal period current year
        FiscalPeriod::firstOrCreate(
            ['name' => 'FY ' . date('Y')],
            [
                'start_date' => date('Y') . '-01-01',
                'end_date' => date('Y') . '-12-31',
                'is_closed' => false,
            ]
        );

        $accounts = [
            // ===== ASET (1) =====
            ['code' => '1-0000', 'name' => 'ASET', 'account_type' => 'asset', 'normal_balance' => 'debit', 'is_active' => true],
            ['code' => '1-1000', 'name' => 'Aset Lancar', 'account_type' => 'asset', 'normal_balance' => 'debit', 'parent_code' => '1-0000', 'is_active' => true],
            ['code' => '1-1100', 'name' => 'Kas', 'account_type' => 'asset', 'normal_balance' => 'debit', 'parent_code' => '1-1000', 'is_active' => true],
            ['code' => '1-1101', 'name' => 'Kas Tunai', 'account_type' => 'asset', 'normal_balance' => 'debit', 'parent_code' => '1-1100', 'is_active' => true],
            ['code' => '1-1110', 'name' => 'Bank', 'account_type' => 'asset', 'normal_balance' => 'debit', 'parent_code' => '1-1000', 'is_active' => true],
            ['code' => '1-1120', 'name' => 'Piutang Usaha', 'account_type' => 'asset', 'normal_balance' => 'debit', 'parent_code' => '1-1000', 'is_active' => true],
            ['code' => '1-1130', 'name' => 'Piutang Karyawan', 'account_type' => 'asset', 'normal_balance' => 'debit', 'parent_code' => '1-1000', 'is_active' => true],
            ['code' => '1-1140', 'name' => 'Persediaan', 'account_type' => 'asset', 'normal_balance' => 'debit', 'parent_code' => '1-1000', 'is_active' => true],
            ['code' => '1-1150', 'name' => 'PPN Masukan', 'account_type' => 'asset', 'normal_balance' => 'debit', 'parent_code' => '1-1000', 'is_active' => true],
            ['code' => '1-1200', 'name' => 'Uang Muka / Deposit', 'account_type' => 'asset', 'normal_balance' => 'debit', 'parent_code' => '1-1000', 'is_active' => true],

            ['code' => '1-2000', 'name' => 'Aset Tetap', 'account_type' => 'asset', 'normal_balance' => 'debit', 'parent_code' => '1-0000', 'is_active' => true],
            ['code' => '1-2100', 'name' => 'Peralatan & Mesin', 'account_type' => 'asset', 'normal_balance' => 'debit', 'parent_code' => '1-2000', 'is_active' => true],
            ['code' => '1-2110', 'name' => 'Akumulasi Penyusutan Peralatan', 'account_type' => 'asset', 'normal_balance' => 'credit', 'parent_code' => '1-2000', 'is_active' => true],
            ['code' => '1-2200', 'name' => 'Kendaraan', 'account_type' => 'asset', 'normal_balance' => 'debit', 'parent_code' => '1-2000', 'is_active' => true],
            ['code' => '1-2210', 'name' => 'Akumulasi Penyusutan Kendaraan', 'account_type' => 'asset', 'normal_balance' => 'credit', 'parent_code' => '1-2000', 'is_active' => true],
            ['code' => '1-2300', 'name' => 'Bangunan', 'account_type' => 'asset', 'normal_balance' => 'debit', 'parent_code' => '1-2000', 'is_active' => true],
            ['code' => '1-2310', 'name' => 'Akumulasi Penyusutan Bangunan', 'account_type' => 'asset', 'normal_balance' => 'credit', 'parent_code' => '1-2000', 'is_active' => true],

            // ===== LIABILITAS (2) =====
            ['code' => '2-0000', 'name' => 'KEWAJIBAN', 'account_type' => 'liability', 'normal_balance' => 'credit', 'is_active' => true],
            ['code' => '2-1000', 'name' => 'Kewajiban Jangka Pendek', 'account_type' => 'liability', 'normal_balance' => 'credit', 'parent_code' => '2-0000', 'is_active' => true],
            ['code' => '2-1100', 'name' => 'Hutang Usaha', 'account_type' => 'liability', 'normal_balance' => 'credit', 'parent_code' => '2-1000', 'is_active' => true],
            ['code' => '2-1110', 'name' => 'Hutang Supplier', 'account_type' => 'liability', 'normal_balance' => 'credit', 'parent_code' => '2-1000', 'is_active' => true],
            ['code' => '2-1120', 'name' => 'PPN Keluaran', 'account_type' => 'liability', 'normal_balance' => 'credit', 'parent_code' => '2-1000', 'is_active' => true],
            ['code' => '2-1130', 'name' => 'PPh Terutang', 'account_type' => 'liability', 'normal_balance' => 'credit', 'parent_code' => '2-1000', 'is_active' => true],
            ['code' => '2-1140', 'name' => 'Gaji Terutang', 'account_type' => 'liability', 'normal_balance' => 'credit', 'parent_code' => '2-1000', 'is_active' => true],

            // ===== EKUITAS (3) =====
            ['code' => '3-0000', 'name' => 'EKUITAS', 'account_type' => 'equity', 'normal_balance' => 'credit', 'is_active' => true],
            ['code' => '3-1000', 'name' => 'Modal', 'account_type' => 'equity', 'normal_balance' => 'credit', 'parent_code' => '3-0000', 'is_active' => true],
            ['code' => '3-1100', 'name' => 'Modal Disetor', 'account_type' => 'equity', 'normal_balance' => 'credit', 'parent_code' => '3-1000', 'is_active' => true],
            ['code' => '3-2000', 'name' => 'Laba Ditahan', 'account_type' => 'equity', 'normal_balance' => 'credit', 'parent_code' => '3-0000', 'is_active' => true],
            ['code' => '3-3000', 'name' => 'Laba Tahun Berjalan', 'account_type' => 'equity', 'normal_balance' => 'credit', 'parent_code' => '3-0000', 'is_active' => true],

            // ===== PENDAPATAN (4) =====
            ['code' => '4-0000', 'name' => 'PENDAPATAN', 'account_type' => 'revenue', 'normal_balance' => 'credit', 'is_active' => true],
            ['code' => '4-1000', 'name' => 'Pendapatan Penjualan', 'account_type' => 'revenue', 'normal_balance' => 'credit', 'parent_code' => '4-0000', 'is_active' => true],
            ['code' => '4-1100', 'name' => 'Penjualan Makanan', 'account_type' => 'revenue', 'normal_balance' => 'credit', 'parent_code' => '4-1000', 'is_active' => true],
            ['code' => '4-1200', 'name' => 'Penjualan Minuman', 'account_type' => 'revenue', 'normal_balance' => 'credit', 'parent_code' => '4-1000', 'is_active' => true],
            ['code' => '4-2000', 'name' => 'Pendapatan Lain-lain', 'account_type' => 'revenue', 'normal_balance' => 'credit', 'parent_code' => '4-0000', 'is_active' => true],

            // ===== BEBAN (5) =====
            ['code' => '5-0000', 'name' => 'BEBAN', 'account_type' => 'expense', 'normal_balance' => 'debit', 'is_active' => true],
            ['code' => '5-1000', 'name' => 'Harga Pokok Penjualan (HPP)', 'account_type' => 'expense', 'normal_balance' => 'debit', 'parent_code' => '5-0000', 'is_active' => true],
            ['code' => '5-1100', 'name' => 'Bahan Baku', 'account_type' => 'expense', 'normal_balance' => 'debit', 'parent_code' => '5-1000', 'is_active' => true],
            ['code' => '5-1200', 'name' => 'Tenaga Kerja Langsung', 'account_type' => 'expense', 'normal_balance' => 'debit', 'parent_code' => '5-1000', 'is_active' => true],
            ['code' => '5-2000', 'name' => 'Beban Operasional', 'account_type' => 'expense', 'normal_balance' => 'debit', 'parent_code' => '5-0000', 'is_active' => true],
            ['code' => '5-2100', 'name' => 'Gaji Karyawan', 'account_type' => 'expense', 'normal_balance' => 'debit', 'parent_code' => '5-2000', 'is_active' => true],
            ['code' => '5-2200', 'name' => 'Sewa Tempat', 'account_type' => 'expense', 'normal_balance' => 'debit', 'parent_code' => '5-2000', 'is_active' => true],
            ['code' => '5-2300', 'name' => 'Listrik & Air', 'account_type' => 'expense', 'normal_balance' => 'debit', 'parent_code' => '5-2000', 'is_active' => true],
            ['code' => '5-2400', 'name' => 'Gas', 'account_type' => 'expense', 'normal_balance' => 'debit', 'parent_code' => '5-2000', 'is_active' => true],
            ['code' => '5-2500', 'name' => 'Perlengkapan', 'account_type' => 'expense', 'normal_balance' => 'debit', 'parent_code' => '5-2000', 'is_active' => true],
            ['code' => '5-2600', 'name' => 'Transportasi', 'account_type' => 'expense', 'normal_balance' => 'debit', 'parent_code' => '5-2000', 'is_active' => true],
            ['code' => '5-2700', 'name' => 'Pemasaran & Promosi', 'account_type' => 'expense', 'normal_balance' => 'debit', 'parent_code' => '5-2000', 'is_active' => true],
            ['code' => '5-2800', 'name' => 'Penyusutan Aset', 'account_type' => 'expense', 'normal_balance' => 'debit', 'parent_code' => '5-2000', 'is_active' => true],
            ['code' => '5-2900', 'name' => 'Beban Lain-lain', 'account_type' => 'expense', 'normal_balance' => 'debit', 'parent_code' => '5-2000', 'is_active' => true],
        ];

        $accountMap = [];

        foreach ($accounts as $data) {
            $parentId = null;
            if (isset($data['parent_code'])) {
                $parentId = $accountMap[$data['parent_code']] ?? null;
            }

            $account = Account::firstOrCreate(
                ['code' => $data['code']],
                [
                    'name' => $data['name'],
                    'account_type' => $data['account_type'],
                    'normal_balance' => $data['normal_balance'],
                    'parent_id' => $parentId,
                    'is_active' => $data['is_active'],
                ]
            );

            $accountMap[$data['code']] = $account->id;
        }
    }
}

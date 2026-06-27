<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\CalculatorController;
use App\Http\Controllers\CashbonController;
use App\Http\Controllers\DailyRevenueController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeSalaryController;
use App\Http\Controllers\HppProductController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RawMaterialController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TransactionTypeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified', 'role:kasir|admin|owner'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified', 'role:admin|owner'])->group(function () {
    Route::resource('users', UserController::class)->except(['show']);
});

// Jenis Transaksi (admin & manajer_keuangan)
Route::middleware(['auth', 'verified', 'role:admin|owner'])->group(function () {
    Route::resource('transaction-types', TransactionTypeController::class)->except(['show']);
});

// Chart of Accounts
Route::middleware(['auth', 'verified', 'role:admin|owner'])->group(function () {
    Route::resource('accounts', AccountController::class)->except(['show']);
});

Route::middleware(['auth', 'verified', 'permission:view transactions'])->group(function () {
    Route::get('/pengeluaran', [PengeluaranController::class, 'index'])->name('pengeluaran.index');
});

// Transaksi — routes literal (create) HARUS didaftarkan sebelum route parameter ({transaction})
Route::middleware(['auth', 'verified', 'permission:create transactions'])->group(function () {
    Route::get('/transactions/create', [TransactionController::class, 'create'])->name('transactions.create');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
});

Route::middleware(['auth', 'verified', 'permission:view transactions'])->group(function () {
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');
});

Route::middleware(['auth', 'verified', 'permission:edit transactions'])->group(function () {
    Route::get('/transactions/{transaction}/edit', [TransactionController::class, 'edit'])->name('transactions.edit');
    Route::put('/transactions/{transaction}', [TransactionController::class, 'update'])->name('transactions.update');
});

Route::middleware(['auth', 'verified', 'permission:delete transactions'])->group(function () {
    Route::delete('/transactions/{transaction}', [TransactionController::class, 'destroy'])->name('transactions.destroy');
});

Route::middleware(['auth', 'verified', 'permission:approve transactions'])->group(function () {
    Route::post('/transactions/{transaction}/approve', [TransactionController::class, 'approve'])->name('transactions.approve');
    Route::post('/transactions/{transaction}/reject', [TransactionController::class, 'reject'])->name('transactions.reject');
});

// Omset Harian — literal route (create) harus sebelum parameter ({dailyRevenue})
Route::middleware(['auth', 'verified', 'permission:create daily revenues'])->group(function () {
    Route::get('/omset-harian/create', [DailyRevenueController::class, 'create'])->name('daily-revenues.create');
    Route::post('/omset-harian', [DailyRevenueController::class, 'store'])->name('daily-revenues.store');
    Route::get('/omset-harian/upload', [DailyRevenueController::class, 'uploadForm'])->name('daily-revenues.upload-form');
    Route::post('/omset-harian/upload', [DailyRevenueController::class, 'upload'])->name('daily-revenues.upload');
    Route::get('/omset-harian/template', [DailyRevenueController::class, 'downloadTemplate'])->name('daily-revenues.template');
});

Route::middleware(['auth', 'verified', 'permission:view daily revenues'])->group(function () {
    Route::get('/omset-harian', [DailyRevenueController::class, 'index'])->name('daily-revenues.index');
});

Route::middleware(['auth', 'verified', 'permission:edit daily revenues'])->group(function () {
    Route::get('/omset-harian/{dailyRevenue}/edit', [DailyRevenueController::class, 'edit'])->name('daily-revenues.edit');
    Route::put('/omset-harian/{dailyRevenue}', [DailyRevenueController::class, 'update'])->name('daily-revenues.update');
});

Route::middleware(['auth', 'verified', 'permission:delete daily revenues'])->group(function () {
    Route::delete('/omset-harian/{dailyRevenue}', [DailyRevenueController::class, 'destroy'])->name('daily-revenues.destroy');
});

// HPP Produk
Route::middleware(['auth', 'verified', 'permission:create hpp'])->group(function () {
    Route::get('/hpp-produk/create', [HppProductController::class, 'create'])->name('hpp-products.create');
    Route::post('/hpp-produk', [HppProductController::class, 'store'])->name('hpp-products.store');
    Route::get('/hpp-produk/import', [HppProductController::class, 'importForm'])->name('hpp-products.import');
    Route::post('/hpp-produk/import', [HppProductController::class, 'importExcel'])->name('hpp-products.import.excel');
});

Route::middleware(['auth', 'verified', 'permission:view hpp'])->group(function () {
    Route::get('/hpp-produk', [HppProductController::class, 'index'])->name('hpp-products.index');
});

Route::middleware(['auth', 'verified', 'permission:edit hpp'])->group(function () {
    Route::get('/hpp-produk/{hppProduct}/edit', [HppProductController::class, 'edit'])->name('hpp-products.edit');
    Route::put('/hpp-produk/{hppProduct}', [HppProductController::class, 'update'])->name('hpp-products.update');
});

Route::middleware(['auth', 'verified', 'permission:delete hpp'])->group(function () {
    Route::delete('/hpp-produk/{hppProduct}', [HppProductController::class, 'destroy'])->name('hpp-products.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/kalkulator', [CalculatorController::class, 'index'])->name('calculator.index');
});

// Bahan Baku (Raw Materials)
Route::middleware(['auth', 'verified', 'permission:create raw-material'])->group(function () {
    Route::get('/bahan-baku/create', [RawMaterialController::class, 'create'])->name('raw-materials.create');
    Route::post('/bahan-baku', [RawMaterialController::class, 'store'])->name('raw-materials.store');
});

Route::middleware(['auth', 'verified', 'permission:view raw-material'])->group(function () {
    Route::get('/bahan-baku', [RawMaterialController::class, 'index'])->name('raw-materials.index');
});

Route::middleware(['auth', 'verified', 'permission:edit raw-material'])->group(function () {
    Route::get('/bahan-baku/{rawMaterial}/edit', [RawMaterialController::class, 'edit'])->name('raw-materials.edit');
    Route::put('/bahan-baku/{rawMaterial}', [RawMaterialController::class, 'update'])->name('raw-materials.update');
});

Route::middleware(['auth', 'verified', 'permission:delete raw-material'])->group(function () {
    Route::delete('/bahan-baku/{rawMaterial}', [RawMaterialController::class, 'destroy'])->name('raw-materials.destroy');
});

// Karyawan — literal route (create) HARUS sebelum route parameter ({employee})
Route::middleware(['auth', 'verified', 'permission:create employee'])->group(function () {
    Route::get('/karyawan/create', [EmployeeController::class, 'create'])->name('employees.create');
    Route::post('/karyawan', [EmployeeController::class, 'store'])->name('employees.store');
});

Route::middleware(['auth', 'verified', 'permission:view employee'])->group(function () {
    Route::get('/karyawan', [EmployeeController::class, 'index'])->name('employees.index');
    Route::get('/karyawan/{employee}', [EmployeeController::class, 'show'])->name('employees.show');
});

Route::middleware(['auth', 'verified', 'permission:edit employee'])->group(function () {
    Route::get('/karyawan/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
    Route::put('/karyawan/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
});

Route::middleware(['auth', 'verified', 'permission:delete employee'])->group(function () {
    Route::delete('/karyawan/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
});

// Gaji Karyawan
Route::middleware(['auth', 'verified', 'permission:create salary'])->group(function () {
    Route::get('/karyawan/{employee}/gaji/create', [EmployeeSalaryController::class, 'create'])->name('employee-salaries.create');
    Route::post('/karyawan/{employee}/gaji', [EmployeeSalaryController::class, 'store'])->name('employee-salaries.store');
});

Route::middleware(['auth', 'verified', 'permission:edit salary'])->group(function () {
    Route::get('/karyawan/{employee}/gaji/{salary}/edit', [EmployeeSalaryController::class, 'edit'])->name('employee-salaries.edit');
    Route::put('/karyawan/{employee}/gaji/{salary}', [EmployeeSalaryController::class, 'update'])->name('employee-salaries.update');
});

Route::middleware(['auth', 'verified', 'permission:delete salary'])->group(function () {
    Route::delete('/karyawan/{employee}/gaji/{salary}', [EmployeeSalaryController::class, 'destroy'])->name('employee-salaries.destroy');
});

Route::middleware(['auth', 'verified', 'permission:view salary'])->group(function () {
    Route::get('/karyawan/{employee}/gaji/{salary}/slip', [EmployeeSalaryController::class, 'slip'])->name('employee-salaries.slip');
});

Route::middleware(['auth', 'verified', 'permission:edit salary'])->group(function () {
    Route::post('/karyawan/{employee}/gaji/{salary}/bayar', [EmployeeSalaryController::class, 'markPaid'])->name('employee-salaries.mark-paid');
});

Route::middleware(['auth', 'verified', 'role:admin|owner'])->group(function () {
    Route::get('/pengaturan/tampilan', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/pengaturan/tampilan', [SettingController::class, 'update'])->name('settings.update');
    Route::get('/pengaturan/landing-page', [SettingController::class, 'landing'])->name('settings.landing');
    Route::put('/pengaturan/landing-page', [SettingController::class, 'landingUpdate'])->name('settings.landing.update');
});

// Cashbon — literal routes (create) HARUS sebelum route parameter ({cashbon})
Route::middleware(['auth', 'verified', 'permission:view cashbon'])->group(function () {
    Route::get('/cashbon', [CashbonController::class, 'index'])->name('cashbons.index');
});

Route::middleware(['auth', 'verified', 'permission:create cashbon'])->group(function () {
    Route::get('/cashbon/create', [CashbonController::class, 'create'])->name('cashbons.create');
    Route::post('/cashbon', [CashbonController::class, 'store'])->name('cashbons.store');
});

Route::middleware(['auth', 'verified', 'permission:edit cashbon'])->group(function () {
    Route::get('/cashbon/{cashbon}/edit', [CashbonController::class, 'edit'])->name('cashbons.edit');
    Route::put('/cashbon/{cashbon}', [CashbonController::class, 'update'])->name('cashbons.update');
    Route::post('/cashbon/{cashbon}/bayar', [CashbonController::class, 'markPaid'])->name('cashbons.mark-paid');
});

Route::middleware(['auth', 'verified', 'permission:delete cashbon'])->group(function () {
    Route::delete('/cashbon/{cashbon}', [CashbonController::class, 'destroy'])->name('cashbons.destroy');
});

// Laporan & Export
Route::middleware(['auth', 'verified'])->prefix('laporan')->name('laporan.')->group(function () {
    Route::get('/', [LaporanController::class, 'index'])->name('index');
    Route::get('/export/omset', [LaporanController::class, 'exportOmset'])->name('export-omset');
    Route::get('/export/transaksi', [LaporanController::class, 'exportTransaksi'])->name('export-transaksi');
    Route::get('/export/cashbon', [LaporanController::class, 'exportCashbon'])->name('export-cashbon');
    Route::get('/export/gaji', [LaporanController::class, 'exportGaji'])->name('export-gaji');
});

// Acct Reports
use App\Http\Controllers\AcctReportController;

Route::middleware(['auth', 'verified', 'role:admin|owner'])->prefix('acct-reports')->name('acct-reports.')->group(function () {
    Route::get('/trial-balance', [AcctReportController::class, 'trialBalance'])->name('trial-balance');
    Route::get('/income-statement', [AcctReportController::class, 'incomeStatement'])->name('income-statement');
    Route::get('/balance-sheet', [AcctReportController::class, 'balanceSheet'])->name('balance-sheet');
    Route::get('/general-ledger', [AcctReportController::class, 'generalLedger'])->name('general-ledger');
});

// Penjualan Harian
use App\Http\Controllers\DailySaleController;

Route::middleware(['auth', 'verified', 'role:kasir|admin|owner'])->prefix('penjualan-harian')->name('penjualan-harian.')->group(function () {
    Route::get('/', [DailySaleController::class, 'index'])->name('index');
    Route::get('/create', [DailySaleController::class, 'create'])->name('create');
    Route::get('/check', [DailySaleController::class, 'check'])->name('check');
    Route::post('/', [DailySaleController::class, 'store'])->name('store');
    Route::get('/import', [DailySaleController::class, 'importForm'])->name('import');
    Route::post('/import', [DailySaleController::class, 'importExcel'])->name('import.excel');
    Route::get('/{date}/{shift}', [DailySaleController::class, 'show'])->name('show');
    Route::get('/{date}/{shift}/edit', [DailySaleController::class, 'edit'])->name('edit');
    Route::delete('/{date}/{shift}', [DailySaleController::class, 'destroy'])->name('destroy');
});

// Telegram Bot Webhook
use App\Http\Controllers\TelegramController;

Route::post('/telegram/webhook', [TelegramController::class, 'webhook'])->name('telegram.webhook');

// Jurnal Umum
use App\Http\Controllers\JournalController;

Route::middleware(['auth', 'verified', 'role:admin|owner'])->prefix('journals')->name('journals.')->group(function () {
    Route::get('/', [JournalController::class, 'index'])->name('index');
    Route::get('/create', [JournalController::class, 'create'])->name('create');
    Route::post('/', [JournalController::class, 'store'])->name('store');
    Route::get('/{journal}', [JournalController::class, 'show'])->name('show');
    Route::post('/{journal}/post', [JournalController::class, 'post'])->name('post');
    Route::post('/{journal}/unpost', [JournalController::class, 'unpost'])->name('unpost');
});

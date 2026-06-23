<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Roles (3 tingkatan):
     *   kasir  -> hanya akses halaman kasir/POS
     *   admin  -> akses penuh operasional
     *   owner  -> akses penuh (tertinggi)
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view dashboard',
            'view kasir',
            'view transactions', 'create transactions', 'edit transactions',
            'delete transactions', 'approve transactions',
            'view daily revenues', 'create daily revenues', 'edit daily revenues', 'delete daily revenues',
            'view hpp', 'create hpp', 'edit hpp', 'delete hpp',
            'view raw-material', 'create raw-material', 'edit raw-material', 'delete raw-material',
            'view employee', 'create employee', 'edit employee', 'delete employee',
            'view salary', 'create salary', 'edit salary', 'delete salary',
            'view cashbon', 'create cashbon', 'edit cashbon', 'delete cashbon',
            'view reports', 'export reports',
            'view users', 'create users', 'edit users', 'delete users', 'assign roles',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // kasir: POS + view daily revenues, transactions
        $kasir = Role::firstOrCreate(['name' => 'kasir']);
        $kasir->syncPermissions([
            'view kasir',
            'view daily revenues',
            'create daily revenues',
            'view transactions',
            'create transactions',
        ]);

        // admin: semua kecuali view kasir
        $adminPerms = array_values(array_filter($permissions, fn ($p) => $p !== 'view kasir'));
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions($adminPerms);

        // owner: semua permission
        $owner = Role::firstOrCreate(['name' => 'owner']);
        $owner->syncPermissions($permissions);

        // Default users
        $ownerUser = User::firstOrCreate(
            ['email' => 'owner@sistemkeuangan.test'],
            ['name' => 'Owner', 'password' => bcrypt('password')]
        );
        $ownerUser->syncRoles(['owner']);

        $adminUser = User::firstOrCreate(
            ['email' => 'admin@sistemkeuangan.test'],
            ['name' => 'Administrator', 'password' => bcrypt('password')]
        );
        $adminUser->syncRoles(['admin']);

        $kasirUser = User::firstOrCreate(
            ['email' => 'kasir@sistemkeuangan.test'],
            ['name' => 'Kasir', 'password' => bcrypt('password')]
        );
        $kasirUser->syncRoles(['kasir']);
    }
}

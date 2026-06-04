<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run()
    {
        // 1. Create Permissions
        $p1 = Permission::firstOrCreate(['name' => 'add product']);
        $p2 = Permission::firstOrCreate(['name' => 'edit product']);
        $p3 = Permission::firstOrCreate(['name' => 'delete product']);
        $p4 = Permission::firstOrCreate(['name' => 'add category']);
        $p5 = Permission::firstOrCreate(['name' => 'edit category']);
        $p6 = Permission::firstOrCreate(['name' => 'delete category']);
        $p7 = Permission::firstOrCreate(['name' => 'manage coupons']);
        $p8 = Permission::firstOrCreate(['name' => 'manage orders']);

        $allPermissions = [$p1, $p2, $p3, $p4, $p5, $p6, $p7, $p8];
        $adminPermisions = [$p1, $p2, $p4, $p5, $p6, $p7, $p8];

        // 2. Create Roles and Assign Permissions
        $superAdmin = Role::firstOrCreate(['name' => 'super admin']);
        $superAdmin->syncPermissions($allPermissions);

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions($adminPermisions);
    }
}

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

        // 2. Create Roles and Assign Permissions
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo([$p1, $p2, $p3]);
    }
}

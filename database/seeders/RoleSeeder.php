<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Role::create(['name' => 'BOOK_USER', 'guard_name' => 'api']);
        Role::create(['name' => 'BOOK_ADMIN', 'guard_name' => 'api']);
    }
}

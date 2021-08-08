<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        $permissions = [
            ['name' => 'create-user'],
            ['name' => 'create-blog']
        ];
        

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission['name']]);
        }

        // create roles and assign created permissions

        Role::create(['name' => 'admin'])
            ->givePermissionTo(['create-user', 'create-blog']);

        Role::create(['name' => 'manager'])
            ->givePermissionTo(['create-blog']);

        Role::create(['name' => 'user'])
            ->givePermissionTo(['create-blog']);

    }
}

<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /** @var Role[] $allRoles */
        $allRoles = Role::all()->keyBy('id');

        $permissions = [
            'properties-manage' => [Role::ROLE_OWNER],
            'bookings-manage' => [Role::ROLE_USER],
        ];

        foreach ($permissions as $key => $roles) {
            /** @var Permission $permission */
            $permission = Permission::create(['name' => $key]);
            foreach ($roles as $role) {
                $allRoles[$role]->permissions()->attach($permission->id);
            }
        }
    }
}

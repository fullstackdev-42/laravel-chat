<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use App\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $permission =  Permission::create([
            'name'         => 'manage_centers',
            'display_name' => 'Manage Centers',
            'guard_name'   => 'web',
        ]);
        $permission1 =  Permission::create([
            'name'         => 'manage_groups',
            'display_name' => 'Manage Groups',
            'guard_name'   => 'web',
        ]);
        $role = Role::where('name', 'Admin')->first();
        $role->givePermissionTo($permission);
        $role->givePermissionTo($permission1);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $role = Role::where('name', 'Admin')->first();
        $role->revokePermissionTo('manage_centers');
        $role->revokePermissionTo('manage_groups');
        Permission::where('name', 'manage_centers')->delete();
        Permission::where('name', 'manage_groups')->delete();
    }
};

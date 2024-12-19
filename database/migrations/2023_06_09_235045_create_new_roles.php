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
        $center_admin_role = Role::create([
            'name' => 'Center Admin',
            'guard_name' => 'web',
            'is_default' => 1,
        ]);

        $group_leader_role = Role::create([
            'name' => 'Group Leader',
            'guard_name' => 'web',
            'is_default' => 1,
        ]);

        $center_admin_permissions =  ['manage_users', 'manage_conversations', 'manage_groups', 'manage_meetings'];
        $group_admin_permissions = ['manage_users', 'manage_conversations', 'manage_meetings'];
        
        $center_admin_role->syncPermissions($center_admin_permissions);
        $group_leader_role->syncPermissions($group_admin_permissions);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $center_admin_role = Center::where('name', 'Center Admin')->first();
        $center_admin_role->syncPermissions([]);

        $group_leader_role = Center::where('name', 'Group Leader')->first();
        $group_leader_role->syncPermissions([]);
    }
};

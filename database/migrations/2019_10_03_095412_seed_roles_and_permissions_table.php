<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SeedRolesAndPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 首先清楚缓存，否会报错
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // 创建权限
        \Spatie\Permission\Models\Permission::create([
            "name" => "manage_contents"
        ]);
        \Spatie\Permission\Models\Permission::create([
            "name" => "manage_users"
        ]);
        \Spatie\Permission\Models\Permission::create([
            "name" => "edit_settings"
        ]);

        // 站长
        $founder = \Spatie\Permission\Models\Role::create([
            "name" => "Founder"
        ]);
        $founder->givePermissionTo("manage_contents");
        $founder->givePermissionTo("manage_users");
        $founder->givePermissionTo("edit_settings");

        // 管理员
        $manager = \Spatie\Permission\Models\Role::create([
            "name" => "Maintainer"
        ]);
        $manager->givePermissionTo("manage_contents");


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $tables = config("permission.table_names");
        \App\Models\Model::unguard();
        \Illuminate\Support\Facades\DB::table($tables["role_has_permissions"])->delete();
        \Illuminate\Support\Facades\DB::table($tables["model_has_roles"])->delete();
        \Illuminate\Support\Facades\DB::table($tables["model_has_permissions"])->delete();
        \Illuminate\Support\Facades\DB::table($tables["roles"])->delete();
        \Illuminate\Support\Facades\DB::table($tables["permissions"])->delete();
        \App\Models\Model::reguard();
    }
}

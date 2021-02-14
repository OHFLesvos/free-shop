<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AddBasicPermissions extends Migration
{
    private $newPermissions = [
        'view orders',
        'update orders',
        'view customers',
        'update customers',
        'manage customers',
        'update products',
        'manage products',
        'export data',
        'import data',
        'manage users',
        'update settings',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->newPermissions as $name) {
            Permission::findOrCreate($name);
        }

        Role::findOrCreate('Translator')->syncPermissions([
            'update products',
        ]);

        Role::findOrCreate('Dispatcher')->syncPermissions([
            'view orders',
            'update orders',
            'view customers',
            'update customers',
            'manage products',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Role::findByName('Translator')->delete();
        Role::findByName('Dispatcher')->delete();

        foreach ($this->newPermissions as $name) {
            try {
                Permission::findByName($name)->delete();
            } catch (PermissionDoesNotExist $ignored) { }
        }
    }
}

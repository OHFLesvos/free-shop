<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    private $newPermissions = [
        'view reports',
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

        Role::findOrCreate('Reporter')->givePermissionTo($this->newPermissions);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Role::findByName('Reporter')->delete();

        foreach ($this->newPermissions as $name) {
            try {
                Permission::findByName($name)->delete();
            } catch (PermissionDoesNotExist $ignored) {
            }
        }
    }
};

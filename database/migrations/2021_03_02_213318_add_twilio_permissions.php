<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    private $newPermissions = [
        'view twilio balance',
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

        // Admin only
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach ($this->newPermissions as $name) {
            try {
                Permission::findByName($name)->delete();
            } catch (PermissionDoesNotExist $ignored) { }
        }
    }
};

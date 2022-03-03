<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class () extends Migration {
    private $newPermissions = [
        'manage blocked numbers',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blocked_phone_numbers', function (Blueprint $table) {
            $table->id();
            $table->string('phone')->unique();
            $table->text('reason');
            $table->timestamps();
        });

        foreach ($this->newPermissions as $name) {
            Permission::findOrCreate($name);
        }

        Role::findOrCreate('Dispatcher')->givePermissionTo($this->newPermissions);
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
            } catch (PermissionDoesNotExist $ignored) {
            }
        }

        Schema::dropIfExists('blocked_phone_numbers');
    }
};

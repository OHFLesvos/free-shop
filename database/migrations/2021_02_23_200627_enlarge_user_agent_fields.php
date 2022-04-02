<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('last_login_user_agent')->change();
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->text('user_agent')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('user_agent')->change();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->string('last_login_user_agent')->change();
        });
    }
};

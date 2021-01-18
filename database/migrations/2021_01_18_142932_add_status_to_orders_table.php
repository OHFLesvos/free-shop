<?php

use App\Models\Order;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', ['new', 'ready', 'completed', 'cancelled'])->default('new')->after('customer_id');
        });
        Order::whereNotNull('completed_at')
            ->update(['status' => 'completed']);
        Order::whereNotNull('cancelled_at')
            ->update(['status' => 'cancelled']);
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['completed_at', 'cancelled_at']);
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
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('completed_at')->nullable();
        });
        Order::where('status', 'completed')
            ->update(['completed_at' => now()]);
        Order::where('status', 'cancelled')
            ->update(['cancelled_at' => now()]);
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}

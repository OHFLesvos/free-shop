<?php

use App\Models\Customer;
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
        $duplicatedIdNumbers = Customer::select('id_number')
            ->selectRaw('COUNT(id_number) as cnt')
            ->groupBy('id_number')
            ->havingRaw('COUNT(id_number) > 1')
            ->get();
        foreach ($duplicatedIdNumbers->pluck('id_number') as $idNumber) {
            $customers = Customer::where('id_number', $idNumber)
                ->orderBy('created_at', 'asc')
                ->get();
            $master = $customers->pop();
            foreach ($customers as $customer) {
                $customer->orders()->update(['customer_id' => $master->id]);
                $customer->delete();
            }
        }

        Schema::table('customers', function (Blueprint $table) {
            $table->unique(['id_number']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropUnique(['id_number']);
        });
    }
};

<?php

use App\Models\Currency;
use App\Models\Customer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('name')
                ->unique();
            $table->unsignedInteger('top_up_amount')
                ->default(0);
            $table->timestamps();
        });

        $currency = Currency::create([
            'name' => 'Credit',
            'top_up_amount' => setting()->get('customer.credit_top_up.amount', setting()->get('customer.starting_credit', 10)),
        ]);

        setting()->forget('customer.starting_credit');
        setting()->forget('customer.credit_top_up.amount');
        setting()->forget('customer.credit_top_up.maximum');
        setting()->save();

        Schema::table('products', function (Blueprint $table) use ($currency) {
            $table->foreignIdFor(Currency::class)
                ->default($currency->id)
                ->after('price')
                ->constrained()
                ->restrictOnDelete();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('currency_id')
                ->default(null)
                ->change();
        });

        Schema::create('currency_customer', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Customer::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignIdFor(Currency::class)
                ->constrained()
                ->restrictOnDelete();
            $table->unique(['customer_id', 'currency_id']);
            $table->unsignedInteger('value');
        });

        Customer::with('currencies')->get()
            ->each(function (Customer $customer) use ($currency) {
                $customer->setBalance($currency->id, $customer->credit);
            });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('credit');
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
            $table->unsignedInteger('credit')->default(0)->after('phone');
        });

        Customer::with('currencies')->get()
            ->each(function (Customer $customer) {
                $customer->credit = $customer->currencies->sum(fn (Currency $currency) => $currency->pivot->value);
                $customer->save();
            });

        Schema::dropIfExists('currency_customer');

        Schema::table('products', function (Blueprint $table) {
            $table->dropForeignIdFor(Currency::class);
            $table->dropColumn('currency_id');
        });

        Schema::dropIfExists('currencies');
    }
};

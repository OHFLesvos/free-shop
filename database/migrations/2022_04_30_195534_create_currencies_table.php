<?php

use App\Models\Currency;
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
            $table->string('name')->unique();
            $table->timestamps();
        });

        $currency = Currency::create([
            'name' => 'Credit',
        ]);

        Schema::table('products', function (Blueprint $table) use($currency) {
            $table->foreignIdFor(Currency::class)
                ->default($currency->id)
                ->after('price')
                ->constrained()
                ->restrictOnDelete();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('currency_id')->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeignIdFor(Currency::class);
            $table->dropColumn('currency_id');
        });

        Schema::dropIfExists('currencies');
    }
};

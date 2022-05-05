<?php

namespace Tests\Unit;

use App\Actions\RegisterOrder;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\TestCase;

class TestRegisterOrder extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_that_true_is_true()
    {


        $currency1 = Currency::create([
            'name' => 'Credit',
        ]);
        $customer = Customer::create([
            'name' => 'John Doe',
        ]);
        $item1 = Product::create([
            'name' => 'Item A',
            'price' => 1,
            'currency_id' => $currency1->id,
        ]);
        $item2 = Product::create([
            'name' => 'Item B',
            'price' => 2,
            'currency_id' => $currency1->id,
        ]);
        $selection = collect([
            $item1->id => 1,
            $item2->id => 2,
        ]);
        $remarks = 'Lorem ipsum dolor sit amet consectetur adipisicing elit.';
        $logMessage = 'Tester registered order.';

        $order = RegisterOrder::run(
            customer: $customer,
            items: $selection,
            remarks: $remarks,
            logMessage: $logMessage,
        );

        $this->assertNotNull($order);
    }
}

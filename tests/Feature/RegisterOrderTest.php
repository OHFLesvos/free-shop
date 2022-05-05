<?php

namespace Tests\Feature;

use App\Actions\RegisterOrder;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Log;
use Tests\TestCase;

class RegisterOrderTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_register_order()
    {
        $currency = Currency::factory()->create();

        /** @var Customer $customer */
        $customer = Customer::factory()->create();

        $product1 = Product::factory()->create([
            'price' => 1,
            'currency_id' => $currency->id,
        ]);
        $product2 = Product::factory()->create([
            'price' => 2,
            'currency_id' => $currency->id,
        ]);
        $selection = collect([
            $product1->id => 1,
            $product2->id => 2,
        ]);

        $remarks = 'Lorem ipsum dolor sit amet consectetur adipisicing elit.';
        $logMessage = 'Tester registered order.';

        $customer->addBalance($currency->id, 5);

        Log::shouldReceive('info')
            ->once()
            ->withArgs(fn ($message) => strpos($message, $logMessage) !== false);
        Log::shouldReceive('warning')
            ->once();

        /** @var Order $order */
        $order = RegisterOrder::run(
            customer: $customer,
            items: $selection,
            remarks: $remarks,
            logMessage: $logMessage,
        );

        $this->assertNotNull($order);
        $this->assertModelExists($order);
        $this->assertEquals($customer->id, $order->customer_id);
        $this->assertEquals($remarks, $order->remarks);
        $this->assertSameSize($selection, $order->products);
        $this->assertEquals("5 $currency->name", $order->getCostsString());
        $this->assertEquals("0 $currency->name", $customer->totalBalance());
    }
}

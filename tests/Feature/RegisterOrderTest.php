<?php

namespace Tests\Feature;

use App\Actions\RegisterOrder;
use App\Exceptions\EmptyOrderException;
use App\Exceptions\InsufficientBalanceException;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class RegisterOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_order(): void
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
        $this->assertEquals($selection, $order->products->mapWithKeys(fn (Product $product) => [$product->id => $product->pivot->quantity]));
        $this->assertEquals("5 $currency->name", $order->getCostsString());
        $this->assertEquals("0 $currency->name", $customer->totalBalance());
    }

    public function test_register_order_without_items(): void
    {
        $customer = Customer::factory()->create();

        $selection = collect([]);

        $this->expectException(EmptyOrderException::class);

        RegisterOrder::run(
            customer: $customer,
            items: $selection,
        );
    }

    public function test_register_order_with_empty_items(): void
    {
        $currency = Currency::factory()->create();

        $customer = Customer::factory()->create();

        $product1 = Product::factory()->create([
            'price' => 1,
            'currency_id' => $currency->id,
        ]);

        $selection = collect([
            $product1->id => '',
        ]);

        $this->expectException(EmptyOrderException::class);

        RegisterOrder::run(
            customer: $customer,
            items: $selection,
        );
    }

    public function test_register_order_with_zero_items(): void
    {
        $currency = Currency::factory()->create();

        $customer = Customer::factory()->create();

        $product1 = Product::factory()->create([
            'price' => 1,
            'currency_id' => $currency->id,
        ]);

        $selection = collect([
            $product1->id => 0,
        ]);

        $this->expectException(EmptyOrderException::class);

        RegisterOrder::run(
            customer: $customer,
            items: $selection,
        );
    }

    public function test_register_order_with_some_zero_items(): void
    {
        $currency = Currency::factory()->create();

        $customer = Customer::factory()->create();

        $product1 = Product::factory()->create([
            'price' => 1,
            'currency_id' => $currency->id,
        ]);
        $product2 = Product::factory()->create([
            'price' => 1,
            'currency_id' => $currency->id,
        ]);

        $selection = collect([
            $product1->id => 1,
            $product2->id => 0,
        ]);

        $customer->addBalance($currency->id, 1);

        $order = RegisterOrder::run(
            customer: $customer,
            items: $selection,
        );

        $this->assertEquals(collect([
            $product1->id => 1,
        ]), $order->products->mapWithKeys(fn (Product $product) => [$product->id => $product->pivot->quantity]));
    }

    public function test_register_order_with_insufficient_balance(): void
    {
        $currency1 = Currency::factory()->create();
        $currency2 = Currency::factory()->create();

        $customer = Customer::factory()->create();

        $product1 = Product::factory()->create([
            'price' => 1,
            'currency_id' => $currency1->id,
        ]);
        $product2 = Product::factory()->create([
            'price' => 3,
            'currency_id' => $currency2->id,
        ]);

        $selection = collect([
            $product1->id => 1,
            $product2->id => 1,
        ]);

        $customer->addBalance($currency1->id, 1);
        $customer->addBalance($currency2->id, 1);

        $this->expectException(InsufficientBalanceException::class);

        RegisterOrder::run(
            customer: $customer,
            items: $selection,
        );
    }

    public function test_register_order_with_insufficient_balance_ignore_costs(): void
    {
        $currency1 = Currency::factory()->create();
        $currency2 = Currency::factory()->create();

        $customer = Customer::factory()->create();

        $product1 = Product::factory()->create([
            'price' => 1,
            'currency_id' => $currency1->id,
        ]);
        $product2 = Product::factory()->create([
            'price' => 3,
            'currency_id' => $currency2->id,
        ]);

        $selection = collect([
            $product1->id => 1,
            $product2->id => 1,
        ]);

        $customer->addBalance($currency1->id, 1);
        $customer->addBalance($currency2->id, 1);

        $order = RegisterOrder::run(
            customer: $customer,
            items: $selection,
            ignoreCosts: true,
        );

        $this->assertNotNull($order);
        $this->assertModelExists($order);
        $this->assertEquals($customer->id, $order->customer_id);
        $this->assertSameSize($selection, $order->products);
    }
}

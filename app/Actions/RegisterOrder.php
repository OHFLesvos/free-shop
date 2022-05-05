<?php

namespace App\Actions;

use App\Models\Customer;
use App\Dto\CostsDto;
use App\Exceptions\EmptyOrderException;
use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\PhoneNumberBlockedByAdminException;
use App\Models\Order;
use App\Models\Product;
use App\Notifications\OrderRegistered;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;

class RegisterOrder
{
    use AsAction;

    /**
     * @param Customer $customer
     * @param Collection<int,int> $items
     * @param ?string $remarks
     * @return Order
     * @throws EmptyOrderException
     * @throws InsufficientBalanceException
     */
    public function handle(
        Customer $customer,
        Collection $items,
        ?string $remarks = null,
        string $logMessage = 'An order has been registered.',
    ): Order {

        // Check if any items are defined
        $items = $items->filter(fn ($quantity) => is_numeric($quantity) && $quantity > 0);
        if ($items->isEmpty()) {
            throw new EmptyOrderException(__('No products have been selected.'));
        }

        // Check balance
        foreach ($customer->currencies as $currency) {
            $basketCosts = (int)$items
                ->map(function (int $quantity, int $productId) use ($currency) {
                    $product = Product::find($productId);
                    return ($product->currency_id == $currency->id) ? $product->price * $quantity : 0;
                })
                ->sum();
            $balance = $customer->getBalance($currency);
            if ($balance < $basketCosts) {
                throw new InsufficientBalanceException(__("Insufficient account balance. :required :currency required, but only :available available.", [
                    'currency' => $currency->name,
                    'required' => $basketCosts,
                    'available' => $balance,
                ]));
            }
        }

        // Register order
        $order = new Order();
        $order->fill([
            'remarks' => $remarks,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
        $order->customer()->associate($customer);
        $order->save();

        // Assign products to order
        $itemIds = $items->mapWithKeys(fn ($quantity, $id) => [$id => [
            'quantity' => $quantity,
        ]]);
        $order->products()->sync($itemIds);

        // Subtract costs from customer
        $order->getCosts()->each(fn (CostsDto $costs) => $customer->subtractBalance($costs->currency_id, $costs->value));

        // Logging
        Log::info($logMessage, [
            'event.kind' => 'event',
            'event.outcome' => 'success',
            'customer.name' => $customer->name,
            'customer.id_number' => $customer->id_number,
            'customer.phone' => $customer->phone,
            'customer.balance' => $customer->totalBalance(),
            'order.id' => $order->id,
            'order.costs' => $order->getCostsString(),
        ]);

        // Notify customer
        $notifyCustomer = !setting()->has('customer.skip_order_registered_notification');
        if ($notifyCustomer) {
            try {
                $customer->notify(new OrderRegistered($order));
            } catch (PhoneNumberBlockedByAdminException $ex) {
                Log::warning("The phone number {$ex->getPhone()} has been blocked by an administrator.");
            } catch (Exception $ex) {
                Log::warning('[' . get_class($ex) . '] Cannot send notification: ' . $ex->getMessage());
            }
        }

        return $order;
    }
}

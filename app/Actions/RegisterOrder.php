<?php

namespace App\Actions;

use App\Models\Customer;
use App\Models\Dto\CostsDto;
use App\Models\Order;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\Concerns\AsAction;

class RegisterOrder
{
    use AsAction;

    public function handle(Customer $customer, Collection $items, string $remarks)
    {
        $order = new Order();
        $order->fill([
            'remarks' => trim($remarks),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
        $order->customer()->associate($customer);
        $order->save();

        $itemIds = $items->mapWithKeys(fn ($quantity, $id) => [$id => [
            'quantity' => $quantity,
        ]]);
        $order->products()->sync($itemIds);

        $order->getCosts()->each(fn (CostsDto $costs) => $customer->subtractBalance($costs->currency_id, $costs->value));
    }
}

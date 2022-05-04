<?php

namespace App\Actions;

use App\Models\Customer;
use App\Dto\CostsDto;
use App\Exceptions\PhoneNumberBlockedByAdminException;
use App\Models\Order;
use App\Notifications\OrderRegistered;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;

class RegisterOrder
{
    use AsAction;

    public function handle(Customer $customer, Collection $items, string $remarks): Order
    {
        $order = new Order();
        $order->fill([
            'remarks' => $remarks,
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

        $notifyCustomer = !setting()->has('customer.skip_order_registered_notification');
        if ($notifyCustomer) {
            try {
                $this->customer->notify(new OrderRegistered($order));
            } catch (PhoneNumberBlockedByAdminException $ex) {
                session()->flash('error', __('The phone number :phone has been blocked by an administrator.', ['phone' => $ex->getPhone()]));
            } catch (\Exception $ex) {
                Log::warning('[' . get_class($ex) . '] Cannot send notification: ' . $ex->getMessage());
            }
        }

        return $order;
    }
}

<?php

namespace App\Http\Livewire\Backend;

use App\Exceptions\PhoneNumberBlockedByAdminException;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Notifications\OrderRegistered;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class OrderRegisterPage extends BackendPage
{
    use AuthorizesRequests;

    public Customer $customer;

    public Order $order;

    public array $selection = [];

    public Collection $products;

    protected function rules(): array
    {
        return [
            'order.remarks' => 'nullable',
            'selection' => ['array', 'min:1'],
            'selection.*' => ['integer', 'min:1'],
        ];
    }

    protected function title(): string
    {
        return 'Register new Order';
    }

    public function mount(): void
    {
        $this->authorize('create', Order::class);

        $this->order = new Order();
        $this->products = Product::query()
            ->orderBy('category->' . config('app.fallback_locale'))
            ->orderBy('sequence')
            ->orderBy('name->' . config('app.fallback_locale'))
            ->get()
            ->filter(fn (Product $product) => $product->getAvailableQuantityPerOrder() > 0);
    }

    public function render(): View
    {
        return parent::view('livewire.backend.order-register-page', []);
    }

    public function getTotalPriceProperty()
    {
        return collect($this->selection)
            ->filter(fn ($quantity) => $quantity > 0)
            ->map(fn ($quantity, $productId) => Product::find($productId)->price * $quantity)
            ->sum();
    }

    public function submit(Request $request)
    {
        $this->authorize('create', Order::class);

        $this->validate();

        $this->order->fill([
            'costs' => $this->getTotalPriceProperty(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $this->order->customer()->associate($this->customer);
        $this->order->save();

        $this->order->products()->sync(collect($this->selection)
            ->mapWithKeys(fn ($quantity, $id) => [$id => [
                'quantity' => $quantity,
            ]]));

        Log::info('Administrator registered order.', [
            'event.kind' => 'event',
            'event.outcome' => 'success',
            'customer.name' => $this->customer->name,
            'customer.id_number' => $this->customer->id_number,
            'customer.phone' => $this->customer->phone,
            'customer.credit' => $this->customer->credit,
            'order.id' => $this->order->id,
        ]);

        if (!setting()->has('customer.skip_order_registered_notification')) {
            try {
                $this->customer->notify(new OrderRegistered($this->order));
            } catch (PhoneNumberBlockedByAdminException $ex) {
                session()->flash('error', __('The phone number :phone has been blocked by an administrator.', ['phone' => $ex->getPhone()]));
            } catch (\Exception $ex) {
                Log::warning('[' . get_class($ex) . '] Cannot send notification: ' . $ex->getMessage());
            }
        }

        return redirect()->route('backend.orders.show', $this->order);
    }
}

<?php

namespace App\Http\Livewire\Backend;

use App\Actions\RegisterOrder;
use App\Http\Livewire\Traits\TrimAndNullEmptyStrings;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class OrderRegisterPage extends BackendPage
{
    use AuthorizesRequests;
    use TrimAndNullEmptyStrings;

    public Customer $customer;

    public string $remarks = '';

    public array $selection = [];

    public Collection $products;

    protected function rules(): array
    {
        return [
            'remarks' => [
                'nullable'
            ],
            'selection' => [
                'array',
                'min:1'
            ],
            'selection.*' => [
                'integer',
                'min:1'
            ],
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

    public function getTotalPriceProperty(OrderService $orderService)
    {
        return $orderService->calculateTotalCostsString($this->selection);
    }

    public function submit()
    {
        $this->authorize('create', Order::class);

        $this->validate();

        /** @var Order $order */
        $order = RegisterOrder::run(
            customer: $this->customer,
            items: collect($this->selection),
            remarks: $this->remarks,
        );

        Log::info('Administrator registered order.', [
            'event.kind' => 'event',
            'event.outcome' => 'success',
            'customer.name' => $this->customer->name,
            'customer.id_number' => $this->customer->id_number,
            'customer.phone' => $this->customer->phone,
            'customer.balance' => $this->customer->totalBalance(),
            'order.id' => $order->id,
            'order.costs' => $order->getCostsString(),
        ]);

        return redirect()->route('backend.orders.show', $order);
    }
}

<?php

namespace App\Http\Livewire\Backend;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class OrderEditPage extends BackendPage
{
    use AuthorizesRequests;

    public Order $order;

    public array $selection = [];

    public int $newProductId = 0;
    public int $newProductQuantity = 0;

    protected function rules(): array
    {
        return [
            'selection' => [
                'array',
                'min:1',
                function ($attribute, $value, $fail) {
                    foreach ($this->selection as $id => $quantity) {
                        if ($quantity > 0) {
                            return;
                        }
                    }
                    $fail('At least one product is required.');
                },
                function ($attribute, $value, $fail) {
                    $totalPrice = collect($this->selection)
                        ->map(fn ($quantity, $productId) => Product::find($productId)->price * $quantity)
                        ->sum();
                    $difference = $totalPrice - $this->order->costs;
                    if ($this->order->customer->credit < $difference) {
                        $fail('Customer has not enough credit. ' . $difference . ' required, ' . $this->order->customer->credit . ' available.');
                    }
                }
            ],
            'selection.*' => [
                'integer',
                'min:0',
            ],
        ];
    }

    protected function title(): string
    {
        return 'Edit Order #' . $this->order->id;
    }

    public function mount(): void
    {
        $this->authorize('update', $this->order);

        foreach ($this->order->products as $product) {
            $this->selection[$product->id] = $product->pivot->quantity;
        }
    }

    public function render(): View
    {
        return parent::view('livewire.backend.order-edit-page');
    }

    public function getOtherProductsProperty()
    {
        return Product::query()
            ->available()
            ->whereNotIn('id', array_keys($this->selection))
            ->orderBy('category->' . config('app.fallback_locale'))
            ->orderBy('sequence')
            ->orderBy('name->' . config('app.fallback_locale'))
            ->get();
    }

    public function submit()
    {
        $this->authorize('update', $this->order);

        $this->validate();

        foreach ($this->selection as $id => $quantity) {
            if ($quantity < 0) {
                continue;
            }

            if ($quantity == 0) {
                $this->order->products()->detach($id);
            } else {
                $this->order->products()->updateExistingPivot($id, [
                    'quantity' => $quantity,
                ]);
            }
        }

        $totalPrice = $this->order->calculateTotalPrice();
        $difference = $totalPrice - $this->order->costs;

        $this->order->costs = $totalPrice;
        $this->order->save();

        $this->order->customer->credit = max(0, $this->order->customer->credit - $difference);
        $this->order->customer->save();

        Log::info('Updated order.', [
            'event.kind' => 'event',
            'event.outcome' => 'success',
            'customer.name' => optional($this->order->customer)->name,
            'customer.id_number' => optional($this->order->customer)->id_number,
            'customer.phone' => optional($this->order->customer)->phone,
            'order.id' => $this->order->id,
            'order.costs' => $this->order->costs,
        ]);

        session()->flash('message', "Order updated.");

        return redirect()->route('backend.orders.show', $this->order);
    }
}

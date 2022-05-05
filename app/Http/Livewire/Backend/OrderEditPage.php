<?php

namespace App\Http\Livewire\Backend;

use App\Actions\ModifyOrder;
use App\Models\Order;
use App\Models\Product;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class OrderEditPage extends BackendPage
{
    use AuthorizesRequests;

    public Order $order;

    public array $selection = [];

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

    public function submit()
    {
        $this->authorize('update', $this->order);

        $this->validate();

        try {
            ModifyOrder::run(
                order: $this->order,
                items: collect($this->selection),
            );

            return redirect()
                ->route('backend.orders.show', $this->order)
                ->with('message', "Order updated.");
        } catch (Exception $ex) {
            session()->flash('error', $ex->getMessage());
            Log::error($ex);
        }
    }
}

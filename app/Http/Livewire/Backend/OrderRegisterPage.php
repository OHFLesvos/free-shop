<?php

namespace App\Http\Livewire\Backend;

use App\Actions\RegisterOrder;
use App\Exceptions\EmptyOrderException;
use App\Http\Livewire\Traits\TrimEmptyStrings;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class OrderRegisterPage extends BackendPage
{
    use AuthorizesRequests;
    use TrimEmptyStrings;

    public Customer $customer;

    public string $remarks = '';

    public array $selection = [];

    public Collection $products;

    public bool $success = false;

    protected function rules(): array
    {
        return [
            'remarks' => [
                'nullable',
            ],
            'selection' => [
                'array',
                'min:1',
                function ($attribute, $value, $fail) {
                    if (count($value) > 0 && collect($value)->filter(fn ($quantity) => is_numeric($quantity) && $quantity > 0)->isEmpty()) {
                        $fail(__('validation.min.array', ['attribute' => $attribute, 'min' => 1]));
                    }
                },
            ],
            'selection.*' => [
                'integer',
                'min:1',
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

        $this->products = Product::query()
            ->orderBy('category->'.config('app.fallback_locale'))
            ->orderBy('sequence')
            ->orderBy('name->'.config('app.fallback_locale'))
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

        try {
            /** @var Order $order */
            $order = RegisterOrder::run(
                customer: $this->customer,
                items: collect($this->selection),
                remarks: $this->remarks,
                logMessage: 'Administrator registered order.',
                ignoreCosts: true,
            );

            $this->success = true;

            return redirect()
                ->route('backend.orders.show', $order)
                ->with('message', 'Order registered.');
        } catch (EmptyOrderException $ex) {
            session()->flash('error', $ex->getMessage());
        } catch (Exception $ex) {
            session()->flash('error', $ex->getMessage());
            Log::error($ex);
        }
    }
}

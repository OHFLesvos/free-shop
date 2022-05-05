<?php

namespace App\Http\Livewire;

use App\Actions\RegisterOrder;
use App\Exceptions\EmptyOrderException;
use App\Exceptions\InsufficientBalanceException;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use App\Services\ShoppingBasket;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ShopFrontPage extends FrontendPage
{
    /**
     * @var Collection<Product>
     */
    public Collection $products;

    /**
     * @var Collection<string>
     */
    public Collection $categories;

    public ?Customer $customer = null;

    public bool $shopDisabled;

    public bool $dailyOrdersMaxedOut = false;

    public bool $useCategories = false;

    public ?Order $order = null;

    public string $remarks = '';

    protected array $rules = [
        'remarks' => 'nullable',
    ];

    protected function title(): string
    {
        return __('Choose your items');
    }

    public function mount(OrderService $orderService): void
    {
        $this->shopDisabled = setting()->has('shop.disabled');
        $this->useCategories = setting()->has('shop.group_products_by_categories');
        $this->dailyOrdersMaxedOut = $orderService->isDailyOrderMaximumReached();

        $this->customer = Auth::guard('customer')->user();

        $this->products = Product::query()
            ->available()
            ->orderBy('category->' . App::getLocale())
            ->orderBy('sequence')
            ->orderBy('name->' . App::getLocale())
            ->with('currency')
            ->get()
            ->filter(fn (Product $product) => $product->getAvailableQuantityPerOrder() > 0);

        if ($this->useCategories) {
            $this->categories = $this->products->values()
                ->sortBy('category')
                ->pluck('category')
                ->unique()
                ->values();
        }
    }

    public function render(ShoppingBasket $basket): View
    {
        return parent::view('livewire.shop-front-page', [
            'basket' => $basket,
            'nextOrderIn' => optional($this->customer)->getNextOrderIn(),
        ]);
    }

    public function add(ShoppingBasket $basket, int $productId, ?int $quantity = 1): void
    {
        $product = $this->products->firstWhere('id', $productId);

        $price = $product->price * $quantity;
        if ($price <= $this->getAvailableBalance($product->currency_id)) {
            $basket->add($productId, $quantity);
        }
    }

    public function remove(ShoppingBasket $basket, int $productId): void
    {
        $basket->remove($productId);
    }

    public function getAvailableBalance(int $currencyId): int
    {
        $basket = app()->make(ShoppingBasket::class);

        $basketCosts = (int)$basket->items()
            ->map(function (int $quantity, int $productId) use ($currencyId) {
                $product = $this->products->firstWhere('id', $productId);
                return ($product->currency_id == $currencyId) ? $product->price * $quantity : 0;
            })
            ->sum();

        return $this->customer->getBalance($currencyId) - $basketCosts;
    }

    public function getAvailableBalances(): Collection
    {
        return $this->customer->currencies->map(fn (Currency $currency) => [
            'name' => $currency->name,
            'total' => $this->customer->getBalance($currency),
            'available' => $this->getAvailableBalance($currency->id)
        ]);
    }

    public function submit(ShoppingBasket $basket)
    {
        $this->validate();

        try {
            /** @var Order $order */
            $order = RegisterOrder::run(
                customer: $this->customer,
                items: $basket->items(),
                remarks: $this->remarks,
                logMessage: 'Customer placed order.',
            );

            $this->order = $order;

            $basket->clear();
        } catch (InsufficientBalanceException $ex) {
            session()->flash('error', $ex->getMessage());
            Log::warning("Customer doesn't have sufficient balance to place order.", [
                'event.kind' => 'event',
                'event.outcome' => 'failure',
                'customer.name' => $this->customer->name,
                'customer.id_number' => $this->customer->id_number,
                'customer.phone' => $this->customer->phone,
                'customer.balance' => $this->customer->totalBalance(),
            ]);
        } catch (EmptyOrderException $ex) {
            session()->flash('error', $ex->getMessage());
            Log::warning('Customer tried to place empty order.', [
                'event.kind' => 'event',
                'event.outcome' => 'failure',
                'customer.name' => $this->customer->name,
                'customer.id_number' => $this->customer->id_number,
                'customer.phone' => $this->customer->phone,
                'customer.balance' => $this->customer->totalBalance(),
            ]);
        } catch (Exception $ex) {
            session()->flash('error', $ex->getMessage());
            Log::error($ex);
        }
    }
}

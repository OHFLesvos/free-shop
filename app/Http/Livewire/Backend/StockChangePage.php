<?php

namespace App\Http\Livewire\Backend;

use App\Http\Livewire\Traits\CurrentRouteName;
use App\Models\StockChange;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;
use Livewire\WithPagination;

class StockChangePage extends BackendPage
{
    use AuthorizesRequests;
    use WithPagination;
    use CurrentRouteName;

    protected string $paginationTheme = 'bootstrap';

    protected string $title = 'Stock changes';

    public bool $includeOrderChanges = false;

    public function mount(): void
    {
        $this->authorize('manage stock');

        $this->includeOrderChanges = boolval(session()->get('stock.changes.includeOrders'));
    }

    public function render(): View
    {
        return parent::view('livewire.backend.stock-changes-page', [
            'changes' => StockChange::query()
                ->when(! $this->includeOrderChanges, fn ($qry) => $qry->whereNull('order_id'))
                ->orderBy('created_at', 'desc')
                ->paginate(25),
        ]);
    }

    public function updatedIncludeOrderChanges(bool $value): void
    {
        session()->put('stock.changes.includeOrders', $value);
    }
}

<?php

namespace App\Http\Livewire\Backend;

use App\Models\Customer;
use App\Models\Tag;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;

class CustomerDetailPage extends BackendPage
{
    use AuthorizesRequests;

    public Customer $customer;
    public $newTag;

    protected function title(): string
    {
        return 'Customer ' . $this->customer->name;
    }

    public function render(): View
    {
        $this->authorize('view', $this->customer);

        return parent::view('livewire.backend.customer-detail-page', [
            'tags' => Tag::orderBy('name')
                ->has('customers')
                ->whereNotIn('slug', $this->customer->tags->pluck('slug'))
                ->get(),
        ]);
    }

    public function updatedNewTag($value): void
    {
        $this->authorize('update', $this->customer);

        if (filled($value)) {
            $this->customer->tags()->attach(Tag::findBySlug($value));
            $this->customer->refresh();
        }
    }
}

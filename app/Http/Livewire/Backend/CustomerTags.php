<?php

namespace App\Http\Livewire\Backend;

use App\Models\Customer;
use App\Models\Tag;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class CustomerTags extends Component
{
    use AuthorizesRequests;

    public Customer $customer;

    public $newTag;

    public function render()
    {
        return view('livewire.backend.customer-tags', [
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

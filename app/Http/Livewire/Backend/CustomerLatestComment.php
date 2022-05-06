<?php

namespace App\Http\Livewire\Backend;

use App\Models\Customer;
use Livewire\Component;

class CustomerLatestComment extends Component
{
    public Customer $customer;

    protected $listeners = ['commentAdded'];

    public function render()
    {
        return view('livewire.backend.customer-latest-comment', [
            'lastComment' => $this->customer->comments()
                ->orderBy('created_at', 'desc')
                ->limit(1)
                ->first(),
            'hasMoreComments' =>  $this->customer->comments()->count() > 1,
        ]);
    }

    public function commentAdded(string $content): void
    {
        $this->customer->addUserComment($content);
        $this->tab = 'comments';
    }
}

<?php

namespace App\Http\Livewire\Backend;

use App\Models\Comment;
use App\Models\Customer;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\WithPagination;

class CustomerDetailPage extends BackendPage
{
    use WithPagination;
    use AuthorizesRequests;

    protected string $paginationTheme = 'bootstrap';

    public Customer $customer;

    protected $listeners = ['commentAdded'];

    protected function title(): string
    {
        return 'Customer ' . $this->customer->name;
    }

    public function render(): View
    {
        $this->authorize('view', $this->customer);

        return parent::view('livewire.backend.customer-detail-page', [
            'orders' => $this->customer->orders()
                ->orderBy('created_at', 'desc')
                ->paginate(10),
            'comments' => $this->customer->comments()
                ->orderBy('created_at', 'asc')
                ->paginate(10),
        ]);
    }

    public function commentAdded(array $data): void
    {
        $comment = new Comment($data);
        $comment->user()->associate(Auth::user());
        $this->customer->comments()->save($comment);
    }

    public function deleteComment(int $id): void
    {
        $this->authorize('delete', $this->customer);

        Comment::where('id', $id)->delete();
    }
}

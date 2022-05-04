<?php

namespace App\Http\Livewire\Backend;

use App\Models\Comment;
use App\Models\Customer;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class CustomerComments extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public Customer $customer;

    protected $listeners = ['commentAdded'];

    public function render()
    {
        return view('livewire.backend.customer-comments', [
            'comments' => $this->customer->comments()
                ->orderBy('created_at', 'desc')
                ->paginate(10),
        ]);
    }

    public function commentAdded(array $data): void
    {
        $comment = new Comment($data);
        $comment->user()->associate(Auth::user());
        $this->customer->comments()->save($comment);
    }

    public function deleteComment(Comment $comment): void
    {
        $this->authorize('delete', $comment);

        $comment->delete();
    }
}

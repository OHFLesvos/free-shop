<?php

namespace App\Http\Livewire\Backend;

use App\Models\Comment;
use App\Models\Customer;
use App\Models\Tag;
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

    public $newTag;

    protected $listeners = ['commentAdded'];

    protected function title(): string
    {
        return 'Customer '.$this->customer->name;
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
            'tags' => Tag::orderBy('name')
                ->has('customers')
                ->whereNotIn('slug', $this->customer->tags->pluck('slug'))
                ->get(),
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

    public function updatedNewTag($value): void
    {
        $this->authorize('update', $this->customer);

        if (filled($value)) {
            $this->customer->tags()->attach(Tag::findBySlug($value));
            $this->customer->refresh();
        }
    }
}

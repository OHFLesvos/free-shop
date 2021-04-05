<?php

namespace App\Http\Livewire\Backend;

use App\Models\BlockedPhoneNumber;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\WithPagination;

class BlockedPhoneNumbersPage extends BackendPage
{
    use AuthorizesRequests;
    use WithPagination;
    use AuthorizesRequests;

    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        $this->authorize('viewAny', BlockedPhoneNumber::class);
    }

    protected $title = 'Blocked Phone Numbers';

    public function render()
    {
        return parent::view('livewire.backend.blocked-phone-numbers-page', [
            'entries' => BlockedPhoneNumber::query()
                ->orderBy('created_at', 'desc')
                ->paginate(10),
        ]);
    }
}

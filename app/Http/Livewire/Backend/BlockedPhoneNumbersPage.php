<?php

namespace App\Http\Livewire\Backend;

use App\Http\Livewire\Traits\CurrentRouteName;
use App\Http\Livewire\Traits\TrimEmptyStrings;
use App\Models\BlockedPhoneNumber;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;
use Livewire\WithPagination;

class BlockedPhoneNumbersPage extends BackendPage
{
    use AuthorizesRequests;
    use AuthorizesRequests;
    use CurrentRouteName;
    use TrimEmptyStrings;
    use WithPagination;

    protected string $title = 'Blocked Phone Numbers';

    protected string $paginationTheme = 'bootstrap';

    public ?BlockedPhoneNumber $shouldDelete = null;

    public string $phone = '';

    public string $reason = '';

    protected array $rules = [
        'phone' => [
            'required',
            'phone:INTERNATIONAL,mobile',
            'unique:blocked_phone_numbers,phone',
        ],
        'reason' => [
            'required',
        ],
    ];

    public function mount(): void
    {
        $this->authorize('viewAny', BlockedPhoneNumber::class);
    }

    public function render(): View
    {
        return parent::view('livewire.backend.blocked-phone-numbers-page', [
            'entries' => BlockedPhoneNumber::query()
                ->orderBy('created_at', 'desc')
                ->paginate(10),
        ]);
    }

    public function submit(): void
    {
        $this->authorize('create', BlockedPhoneNumber::class);

        $this->validate();

        $entry = BlockedPhoneNumber::create([
            'phone' => $this->phone,
            'reason' => $this->reason,
        ]);

        $this->reset(['phone', 'reason']);

        session()->flash('message', "Phone number $entry->phone added.");
    }

    public function markForDeletion(int $id): void
    {
        $this->shouldDelete = BlockedPhoneNumber::find($id);
    }

    public function cancelDeletion(): void
    {
        $this->shouldDelete = null;
    }

    public function delete(): void
    {
        if ($this->shouldDelete !== null) {
            $entry = BlockedPhoneNumber::find($this->shouldDelete['id']);
            if (isset($entry)) {
                $this->authorize('delete', $entry);

                $entry->delete();

                session()->flash('message', "Phone number $entry->phone removed.");
            }
            $this->shouldDelete = null;
        }
    }
}

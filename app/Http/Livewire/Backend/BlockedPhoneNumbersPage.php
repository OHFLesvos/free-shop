<?php

namespace App\Http\Livewire\Backend;

use App\Http\Livewire\CurrentRouteName;
use App\Http\Livewire\TrimEmptyStrings;
use App\Models\BlockedPhoneNumber;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\WithPagination;

class BlockedPhoneNumbersPage extends BackendPage
{
    use AuthorizesRequests;
    use WithPagination;
    use AuthorizesRequests;
    use CurrentRouteName;
    use TrimEmptyStrings;

    protected $paginationTheme = 'bootstrap';

    public $shouldDelete = null;

    public $phone = '';
    public $reason = '';

    protected $rules = [
        'phone' => [
            'required',
            'phone:AUTO,mobile',
            'unique:blocked_phone_numbers,phone',
        ],
        'reason' => [
            'required',
        ],
    ];

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

    public function submit()
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

    public function delete()
    {
        if (isset($this->shouldDelete)) {
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
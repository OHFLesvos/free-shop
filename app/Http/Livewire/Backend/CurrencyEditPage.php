<?php

namespace App\Http\Livewire\Backend;

use App\Models\Currency;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;

class CurrencyEditPage extends BackendPage
{
    use AuthorizesRequests;

    public Currency $currency;

    protected function rules(): array
    {
        return [
            'currency.name' => [
                'required',
            ],
            'currency.top_up_amount' => [
                'required',
                'integer',
                'min:0',
            ],
        ];
    }

    public function mount(): void
    {
        if (isset($this->currency)) {
            $this->authorize('update', $this->currency);
        } else {
            $this->authorize('create', Currency::class);
        }

        if (!isset($this->currency)) {
            $this->currency = new Currency();
        }
    }

    protected function title(): string
    {
        return $this->currency->exists
            ? 'Edit currency ' . $this->currency->name
            : 'Add new currency';
    }

    public function render(): View
    {
        return parent::view('livewire.backend.currency-edit-page', [
            'title' => $this->currency->exists ? 'Edit Currency' : 'Add new currency',
        ]);
    }

    public function submit()
    {
        $this->authorize('update', $this->currency);

        $this->validate();

        $this->currency->save();

        session()->flash('message', 'Currency updated.');

        return redirect()->route('backend.configuration.currencies');
    }

    public function delete()
    {
        $this->authorize('delete', $this->currency);

        $this->currency->delete();

        session()->flash('message', 'Currency deleted.');

        return redirect()->route('backend.configuration.currencies');
    }
}

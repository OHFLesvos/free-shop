<?php

namespace App\Http\Livewire\Backend;

trait WithSorting
{
    public function mountWithSorting(): void
    {
        if (session()->has(get_class().'.sortBy')) {
            $this->sortBy = session()->get(get_class().'.sortBy');
        }
        if (session()->has(get_class().'.sortDirection')) {
            $this->sortDirection = session()->get(get_class().'.sortDirection');
        }
    }

    public function sortBy(string $field): void
    {
        if (in_array($field, $this->sortableFields)) {
            $this->sortDirection = $this->sortBy === $field
                ? $this->reverseSort()
                : 'asc';
            $this->sortBy = $field;

            session()->put(get_class().'.sortBy', $this->sortBy);
            session()->put(get_class().'.sortDirection', $this->sortDirection);
        }
    }

    public function reverseSort(): string
    {
        return $this->sortDirection === 'asc'
            ? 'desc'
            : 'asc';
    }
}

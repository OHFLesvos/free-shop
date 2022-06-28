<div class="row g-2">
    @foreach ($customer->tags->sortBy('name', SORT_STRING | SORT_FLAG_CASE) as $tag)
        <div class="col-auto">
        <a href="{{ route('backend.customers', ['tags[]' => $tag->slug]) }}"
            class="btn btn-sm btn-primary">
            {{ $tag->name }}</a>
        </div>
    @endforeach
    @can('update', $customer)
        @if (count($tags) > 0)
            <div class="col-auto">
                <select class="form-select form-select-sm" style="max-width: 11em;"
                    wire:model="newTag" wire:loading.attr="disabled">
                    <option value="" selected>-- Add tag --</option>
                    @foreach ($tags as $tag)
                        <option value="{{ $tag->slug }}">
                            {{ $tag->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endif
    @endif
</div>

<div class="medium-container">

    @if (session()->has('message'))
        <x-alert type="success" dismissible>{{ session()->get('message') }}</x-alert>
    @endif
    <div class="table-responsive">
        <table class="table table-bordered bg-white shadow-sm @can('manage text blocks') table-hover @endcan">
            <thead>
                <th>Name</th>
                <th>Translations</th>
                <th>Last updated</th>
            </thead>
            <tbody>
                @forelse($textBlocks as $textBlock)
                    <tr
                        @can('update', $textBlock) onclick="window.location='{{ route('backend.text-blocks.edit', $textBlock) }}'" @endcan
                        class="@can('update', $textBlock) cursor-pointer @endcan"
                    >
                        <td>{{ $textBlock->name }}</td>
                        <td class="fit">
                            {{ collect(config('app.supported_languages'))
                                ->keys()
                                ->filter(fn ($key) => $textBlock->hasTranslation('content', $key))
                                ->map(fn ($key) => strtoupper($key))
                                ->join(', ') }}
                        </td>
                        <td class="fit">
                            <x-date-time-info :value="$textBlock->updated_at" />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">
                            <em>No text blocks registered.</em>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

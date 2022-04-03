<div class="medium-container">
    @include('livewire.backend.configuration-nav')

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
                    <tr @can('update', $textBlock)
                        onclick="window.location='{{ route('backend.configuration.text-blocks.edit', $textBlock) }}'" @endcan
                        class="@can('update', $textBlock) cursor-pointer @endcan">
                        <td>
                            <strong>{{ $textBlock->name }}</strong><br>
                            {{ config('text-blocks.' . $textBlock->name . '.purpose') }}
                        </td>
                        <td class="fit">
                            {{ collect(config('localization.languages'))
                                ->filter(fn(array $language) => $textBlock->hasTranslation('content', $language['code']))
                                ->map(fn(array $language) => $language['name'])
                                ->join(', ') }}
                        </td>
                        <td class="fit">
                            <x-date-time-info :value="$textBlock->updated_at" line-break />
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

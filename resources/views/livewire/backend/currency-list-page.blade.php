<div class="medium-container">
    @include('livewire.backend.configuration-nav')

    @if (session()->has('message'))
        <x-alert type="success" dismissible>{{ session()->get('message') }}</x-alert>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered bg-white shadow-sm @can('manage manage products') table-hover @endcan">
            <thead>
                <th>Name</th>
                <th class="fit text-end">Top-up amount</th>
                <th class="fit text-end">Assigned Products</th>
            </thead>
            <tbody>
                @forelse($currencies as $currency)
                    <tr @can('update', $currency)
                        onclick="window.location='{{ route('backend.configuration.currencies.edit', $currency) }}'" @endcan
                        class="@can('update', $currency) cursor-pointer @endcan">
                        <td>
                            {{ $currency->name }}
                        </td>
                        <td class="fit text-end">
                            {{ $currency->top_up_amount }}
                        </td>
                        <td class="fit text-end">
                            {{ $currency->products()->count() }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">
                            <em>No currencies registered.</em>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @can('create', App\Model\Currency::class)
        <p>
            <a
                href="{{ route('backend.configuration.currencies.create') }}"
                class="btn btn-primary">
                Add
            </a>
        </p>
    @endcan
</div>

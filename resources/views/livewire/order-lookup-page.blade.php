<div>
    <form wire:submit.prevent="submit" class="mb-4" autocomplete="off">
        <div class="card mb-4 shadow-sm">
            <div class="card-header">@lang('Find your order')</div>
            <div class="card-body">
                <div class="form-row">
                    <div class="col-md">
                        <div class="form-group">
                            <label for="inputIdNumber">@lang('Your ID number')</label>
                            <input
                                type="text"
                                class="form-control @error('id_number') is-invalid @enderror"
                                id="inputIdNumber"
                                wire:model.defer="id_number"
                                required
                                @if($results === null) autofocus @endif
                                autocomplete="off">
                            @error('id_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-md">
                        <div class="form-group">
                            <label for="inputPhone">@lang('Your phone number')</label>
                            <input
                                type="tel"
                                class="form-control @error('phone') is-invalid @enderror"
                                id="inputPhone"
                                wire:model.defer="phone"
                                required
                                autocomplete="off">
                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-between">
            <button
                type="submit"
                class="btn btn-primary">
                <x-icon-progress wire:loading wire:target="submit"/>
                @lang('Search')
            </button>
        </div>
    </form>
    @if($results !== null)
        @forelse($results as $order)
            <h4>{{ $order->created_at->toUserTimezone()->isoFormat('LLLL') }}</h4>
            <div class="card mb-4 shadow-sm">
                <div class="card-header">
                    @lang('Products')
                </div>
                <table class="table table-bordered m-0">
                    <tbody>
                        @php
                            $hasPictures = $order->products->whereNotNull('pictureUrl')->isNotEmpty();
                        @endphp
                        @foreach($order->products->sortBy('name') as $product)
                            <tr>
                                @if($hasPictures)
                                    <td class="fit">
                                        @isset($product->pictureUrl)
                                            <img
                                                src="{{ $product->pictureUrl }}"
                                                alt="Product Image"
                                                style="max-width: 100px; max-height: 75px"/>
                                        @endisset
                                    </td>
                                @endif
                                <td>
                                    {{ $product->name }}<br>
                                    <small>{{ $product->category }}</small>
                                </td>
                                <td class="fit text-right align-middle">
                                    <strong><big>{{ $product->pivot->quantity }}</big></strong>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="card-footer">
                    @if($order->cancelled_at !== null)
                        @lang('This order has been cancelled on :date.', ['date' => $order->cancelled_at->toUserTimezone()->isoFormat('LLLL')])
                    @elseif($order->completed_at !== null)
                        @lang('This order has been completed on :date.', ['date' => $order->completed_at->toUserTimezone()->isoFormat('LLLL')])
                    @else
                        @lang('This order is in progress.')
                    @endif
                </div>
            </div>
        @empty
            <x-alert type="info">@lang('No orders found.')</x-alert>
        @endforelse
    @endif
</div>

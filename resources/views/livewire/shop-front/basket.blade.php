<div class="sticky mb-4">
    @isset($customer)
        @php
            $balances = $this->getAvailableBalances();
        @endphp
    @endif
    @isset($customer)
        <x-card :title="__('Your account balance')">
            @forelse ($balances as $balance)
                <div class="row align-items-center">
                    <div class="col">
                        <strong>{{ $balance['available'] }}</strong>
                        @if($balance['available'] != $balance['total'] )
                            <span class="text-muted">/ {{ $balance['total'] }}</span>
                        @endif
                        {{ $balance['name'] }}
                    </div>
                    <div class="col">
                        @php
                            $percentage = round($balance['available'] / $balance['total']  * 100);
                        @endphp
                        <div class="progress">
                            <div class="progress-bar"
                                role="progressbar"
                                style="width: {{ $percentage }}%"
                                aria-valuenow="{{ $percentage }}"
                                aria-valuemin="0"
                                aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            @empty
                <p class="card-text">
                    <strong class="text-warning">{{ __("You currently don't have any balance available to spend.") }}</strong>
                </p>
            @endforelse
            @if($customer->nextTopUpDate !== null)
                <p class="card-text">
                    {!! __('Next top-up on <strong>:date</strong>.', ['date' => $customer->nextTopUpDate->isoFormat('LL') ]) !!}
                </p>
            @endif
        </x-card>
    @endisset

    <form wire:submit.prevent="submit" autocomplete="off">
        <x-card :title="__('Your order')" :noFooterPadding="$basket->isNotEmpty()">
            @isset($customer)
                @if($basket->isNotEmpty())
                    <x-slot name="addon">
                        <table class="table m-0">
                            <tbody>
                                @php
                                    $totals = [];
                                @endphp
                                @foreach($this->products->whereIn('id', $basket->items()->keys()) as $product)
                                    @php
                                        $price = $product->price > 0 && $product->currency_id !== null ? $basket->get($product->id) * $product->price : 0;
                                        if ($price > 0) {
                                            if (isset($totals[$product->currency->name])) {
                                                $totals[$product->currency->name] += $price;
                                            } else {
                                                $totals[$product->currency->name] = $price;
                                            }
                                        }
                                    @endphp
                                    <tr>
                                        <td class="align-middle ps-3 fit text-end">
                                            <strong>{{ $basket->get($product->id) }}</strong>
                                        </td>
                                        <td class="align-middle">
                                            {{ $product->name }}
                                            {{-- <small class="text-muted ms-1">{{ $product->category }}</small> --}}
                                        </td>
                                        <td class="align-middle fit">
                                            @if($price > 0)
                                                {{ $price }} {{ $product->currency->name }}
                                            @else
                                                <span class="text-success">{{ __('Free') }}</span>
                                            @endif
                                        </td>
                                        <td class="align-middle pe-3 fit">
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-danger"
                                                wire:click="remove({{ $product->id }})"
                                                wire:loading.attr="disabled"
                                                aria-label="{{ __('Remove') }}"
                                                title="{{ __('Remove') }}">
                                                <x-icon icon="times"/>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2" class="text-end align-top">
                                        <strong>{{ __('Total') }}</strong>
                                    </td>
                                    <td colspan="2">
                                        @php
                                            ksort($totals)
                                        @endphp
                                        @foreach($totals as $k => $v)
                                            <u>{{ $v }} {{ $k }}</u><br>
                                        @endforeach
                                    </td>
                                </tr>
                            </tfoot>
                        </table>

                        {{-- Remarks --}}
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="inputRemarks" class="form-label">{{ __('Remarks') }}</label>
                                <textarea
                                    class="form-control @error('remarks') is-invalid @enderror"
                                    id="inputRemarks"
                                    wire:model.defer="remarks"
                                    rows="3"
                                    autocomplete="off"
                                    aria-describedby="remarksHelp"></textarea>
                                @error('remarks') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <small id="remarksHelp" class="form-text text-muted">
                                    {{ __('Please write if we need to know anything more regarding your order.') }}
                                </small>
                            </div>
                            <p class="card-text d-flex align-items-center">
                                <x-icon icon="info-circle" class="fa-2x me-3"/>
                                <span>
                                    @isset($customer->phone)
                                        {!! __('We will send you updates about your order via SMS to <strong dir="ltr" class="text-nowrap">:phone</strong>.', ['phone' => $customer->phoneFormattedInternational]) !!}
                                    @else
                                        {!! __('We will send you updates about your order via email to <strong class="text-nowrap">:email</strong>.', ['email' => $customer->email]) !!}
                                    @endisset
                                    {!! __('You can update your contact information <a href=":url">here</a>.', ['url' => route('customer.account') ]) !!}</a>
                                </span>
                            </p>
                        </div>
                    </x-slot>

                    <x-slot name="footer">
                        <div class="d-flex justify-content-end">
                            @php
                                $customerBalances = $customer->balance();
                            @endphp
                            @if(collect($totals)->every(fn ($v, $k) => isset($customerBalances[$k]) && $customerBalances[$k] >= $v))
                                <button
                                    type="submit"
                                    class="btn btn-primary">
                                    <x-spinner wire:loading wire:target="submit"/>
                                    {{ __('Send order') }}
                                </button>
                            @else
                                <span class="text-danger">
                                    {{ __('Not enough credit.') }}
                                </span>
                            @endif
                        </div>
                    </x-slot>
                @else
                    @if($balances->sum('available') == 0)
                        <p class="card-text">{{ __("You can't add any products right now.") }}</p>
                    @else
                        <p class="card-text">{{ __('Please add some products.') }}</p>
                    @endif
                @endif
            @else
                <p class="card-text">{{ __('Please register or login to place an order.') }}</p>
                <a
                    href="{{ route('customer.login') }}"
                    class="btn btn-primary">
                    {{ __('Login') }}
                </a>
            @endisset
        </x-card>
    </form>
</div>

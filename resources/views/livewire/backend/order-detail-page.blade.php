<div class="medium-container">

    @if (session()->has('error'))
        <x-alert type="danger" dismissible>{{ session()->get('error') }}</x-alert>
    @endif
    @if (session()->has('message'))
        <x-alert type="success" dismissible>{{ session()->get('message') }}</x-alert>
    @endif

    @if($showChangeStatus)
        {{-- Change status --}}
        <x-card title="Change order #{{ $order->id }}">
            <p class="form-label">New status:</p>
            @foreach($this->statuses as $key)
                <div class="form-check">
                    <input
                        class="form-check-input"
                        type="radio"
                        id="newStatusInput_{{ $key }}"
                        @if($key == $order->status) autofocus @endif
                        value="{{ $key }}"
                        wire:model="newStatus">
                    <label class="form-check-label" for="newStatusInput_{{ $key }}">
                        <x-order-status-label :value="$key" />
                        @if($key == $order->status)
                            (current)
                        @endif
                    </label>
                </div>
            @endforeach
            @if($this->hasMessage && $order->customer !== null)
                <label for="messageInput" class="form-label">
                    Message to customer
                    @isset($order->customer->locale)
                        @inject('localization', 'App\Services\LocalizationService')
                        {{ $localization->getLanguageName($order->customer->locale) }}
                    @endisset
                </label>
                <textarea
                    id="messageInput"
                    wire:model.lazy="message"
                    rows="6"
                    class="form-control font-monospace @error('message') is-invalid @enderror"
                    wire:model.lazy="message"
                    placeholder="{{ $this->configuredMessage }}"
                    @inject('localization', 'App\Services\LocalizationService')
                    @if($localization->isRtl($order->customer->locale)) dir="rtl" @endif
                    aria-describedby="messageHelp"
                ></textarea>
                @error('message') <div class="invalid-feedback">{{ $message }}</div> @enderror
                <small id="contentHelp" class="form-text">
                    {!! config('text-blocks.' . $this->messageTextBlockName . '.help') !!}
                </small>
            @endif
            <x-slot name="footer">
                <button
                    type="submit"
                    class="btn btn-primary"
                    wire:target="submit"
                    wire:loading.attr="disabled"
                    wire:click="submit">
                    <x-spinner wire:loading wire:target="submit"/>
                    Apply
                </button>
                <button
                    wire:click="$set('showChangeStatus', false)"
                    class="btn btn-link">
                    Cancel
                </button>
            </x-slot>
        </x-card>
    @else
        {{-- Order details --}}
        <x-card title="Order #{{ $order->id }}" no-footer-padding>
            <dl class="row mb-2 mt-3">
                <dt class="col-sm-3">Status</dt>
                <dd class="col-sm-9"><x-order-status-label :order="$order" />
                    @can('update', $order)
                    <button
                        wire:click="$set('showChangeStatus', true)"
                        class="btn btn-outline-primary btn-sm ms-2">
                        Change status
                    </button>
                @endcan
                </dd>
                <dt class="col-sm-3">Customer</dt>
                <dd class="col-sm-9">
                    @isset($order->customer)
                        <strong>Name:</strong> <a href="{{ route('backend.customers.show', $order->customer) }}">{{ $order->customer->name }}</a><br>
                        <strong>ID Number:</strong> {{ $order->customer->id_number }}
                    @else
                        <em>Deleted</em>
                    @endisset
                </dd>
                <dt class="col-sm-3">IP Address</dt>
                <dd class="col-sm-9"><x-ip-info :value="$order->ip_address" /></dd>
                <dt class="col-sm-3">Geo Location</dt>
                <dd class="col-sm-9"><x-geo-location-info :value="$order->ip_address" /></dd>
                <dt class="col-sm-3">User Agent</dt>
                <dd class="col-sm-9"><x-user-agent-info :value="$order->user_agent" /></dd>
                <dt class="col-sm-3">Registered</dt>
                <dd class="col-sm-9"><x-date-time-info :value="$order->created_at" /></dd>
            </dl>
            <x-slot name="footer">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('backend.orders') }}" class="btn btn-link">Back to overview</a>
                    <span>
                        @can('update', $order)
                            <a href="{{ route('backend.orders.edit', $order) }}" class="btn btn-secondary">Edit</a>
                        @endcan
                    </span>
                </div>
            </x-slot>
        </x-card>
    @endif

    {{-- Remarks --}}
    @isset($order->remarks)
        <x-alert type="info mb-4">
            <strong>Remarks from customer:</strong><br>
            {!! nl2br(e($order->remarks)) !!}
        </x-alert>
    @endisset

    <ul class="nav nav-tabs mb-4">
        @foreach($tabs as $value => $label)
            <li class="nav-item">
                <a
                    class="nav-link @if($tab == $value)active @endif"
                    href="#"
                    wire:click.prevent="$set('tab', '{{ $value }}')"
                >
                {{ $label }}</a>
            </li>
        @endforeach
    </ul>
    @if($tab == 'products')
        @livewire('backend.order-products', ['order' => $order])
    @elseif($tab == 'history')
        @livewire('backend.order-history', ['order' => $order])
    @endif

</div>

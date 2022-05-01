<div class="medium-container">
    <x-card :title="$customer->name" no-footer-padding>
        <dl class="row mb-2 mt-3">
            <dt class="col-sm-3">ID number</dt>
            <dd class="col-sm-9">{{ $customer->id_number }}</dd>
            @isset($customer->locale)
                <dt class="col-sm-3">Language</dt>
                <dd class="col-sm-9">
                    @inject('localization', 'App\Services\LocalizationService')
                    {{ $localization->getLanguageName($customer->locale) }}
                </dd>
            @endisset
            @isset($customer->phone)
                <dt class="col-sm-3">Phone</dt>
                <dd class="col-sm-9">
                    <x-phone-info :value="$customer->phone" />
                    <div class="d-grid gap-2 d-md-block mt-1">
                        <x-phone-number-link :value="$customer->phone" class="btn btn-outline-primary btn-sm">
                            <x-icon icon="phone" /> Call
                        </x-phone-number-link>
                        <x-phone-number-link :value="$customer->phone" :body="'Hello '.$customer->name. '. '" type="sms"
                            class="btn btn-outline-primary btn-sm">
                            <x-icon icon="sms" /> SMS
                        </x-phone-number-link>
                        <x-phone-number-link :value="$customer->phone" :body="'Hello '.$customer->name.'. '" type="whatsapp"
                            class="btn btn-outline-primary btn-sm">
                            <x-icon icon="whatsapp" type="brands" /> WhatsApp
                        </x-phone-number-link>
                        <x-phone-number-link :value="$customer->phone" :body="'Hello '.$customer->name.'. '" type="viber"
                            class="btn btn-outline-primary btn-sm">
                            <x-icon icon="viber" type="brands" /> Viber
                        </x-phone-number-link>
                    </div>
                </dd>
            @endisset
            @isset($customer->email)
                <dt class="col-sm-3">Email address</dt>
                <dd class="col-sm-9"><a href="mailto:{{ $customer->email }}">{{ $customer->email }}</a></dd>
            @endisset
            <dt class="col-sm-3">Balance</dt>
            <dd class="col-sm-9">{!! nl2br(e($customer->balance()->map(fn ($v, $k) => "$v $k")->join("\n") )) !!}</dd>
            @isset($customer->remarks)
                <dt class="col-sm-3">Remarks</dt>
                <dd class="col-sm-9">{!! nl2br(e($customer->remarks)) !!}</dd>
            @endisset
            <dt class="col-sm-3">Tags</dt>
            <dd class="col-sm-9">
                @foreach ($customer->tags->sortBy('name', SORT_STRING | SORT_FLAG_CASE) as $tag)
                    <a href="{{ route('backend.customers', ['tags[]' => $tag->slug]) }}"
                        class="btn btn-sm btn-primary">
                        {{ $tag->name }}</a>
                @endforeach
                @can('update', $customer)
                    @if (count($tags) > 0)
                        <select class="form-select form-select-sm @if (count($customer->tags) > 0) mt-2 @endif" style="max-width: 11em;"
                            wire:model="newTag" wire:loading.attr="disabled">
                            <option value="" selected>-- Add tag --</option>
                            @foreach ($tags as $tag)
                                <option value="{{ $tag->slug }}">
                                    {{ $tag->name }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                    @endif
                </dd>
                @if ($customer->is_disabled)
                    <dt class="col-sm-3">Disabled</dt>
                    <dd class="col-sm-9">{{ $customer->disabled_reason ?? 'Yes' }}</dd>
                @endif
                <dt class="col-sm-3">Registered</dt>
                <dd class="col-sm-9">
                    <x-date-time-info :value="$customer->created_at" />
                </dd>
            </dl>
        </x-card>

        {{-- Comments --}}
        <h3>Comments</h3>
        @if ($comments->isNotEmpty())
            @foreach ($comments as $comment)
                <div class="card mb-3 shadow-sm" wire:key="comment-{{ $comment->id }}">
                    <div class="card-body">
                        {{ $comment->content }}
                        @can('delete', $comment)
                            <button class="btn btn-outline-danger btn-sm float-end"
                                wire:click="deleteComment({{ $comment->id }})"
                                onclick="confirm('Are you sure you want to remove this comment?') || event.stopImmediatePropagation()">
                                Delete
                            </button>
                        @endcan
                    </div>
                    <div class="card-footer d-sm-flex justify-content-between">
                        <span>
                            <x-date-time-info :value="$comment->created_at" />
                        </span>
                        @isset($comment->user)
                            <small class="text-muted">{{ $comment->user->name }}</small>
                        @endisset
                    </div>
                </div>
            @endforeach
            <div class="overflow-auto">{{ $comments->onEachSide(2)->links() }}</div>
        @endif

        @livewire('components.add-comment-input')

        {{-- Orders --}}
        <h3>Orders</h3>
        @if ($customer->orders->isNotEmpty())
            <div class="table-responsive">
                <table class="table table-bordered table-hover shadow-sm bg-white">
                    <thead>
                        <tr>
                            <th class="text-end">Order</th>
                            <th>Status</th>
                            <th>Registered</th>
                            <th class="text-end">Products</th>
                            <th class="text-end">Costs</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr onclick="window.location='{{ route('backend.orders.show', $order) }}'"
                                class="cursor-pointer">
                                <td class="fit text-end">#{{ $order->id }}</td>
                                <td class="fit">
                                    <x-order-status-label :order="$order" />
                                </td>
                                <td>
                                    <x-date-time-info :value="$order->created_at" />
                                </td>
                                <td class="fit text-end">
                                    {{ $order->products->map(fn($product) => $product->pivot->quantity)->sum() }}
                                </td>
                                <td class="fit text-end">
                                    {{ $order->costs }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="overflow-auto">{{ $orders->onEachSide(2)->links() }}</div>
        @endif
        @can('create', App\Models\Order::class)
            <p><a href="{{ route('backend.customers.registerOrder', $customer) }}" class="btn btn-primary">New order</a></p>
        @endcan

        {{-- Order history --}}
        @php
            $audits = $customer
                ->audits()
                ->with('user')
                ->get();
        @endphp
        @if ($audits->isNotEmpty())
            <h3 class="mt-2">Customer history</h3>
            <ul class="list-group shadow-sm mb-4">
                @foreach ($audits as $audit)
                    <li class="list-group-item">
                        On <strong>
                            <x-date-time-info :value="$audit->created_at" />
                        </strong>
                        <strong>{{ optional($audit->user)->name ?? 'Unknown' }}</strong>
                        @if ($audit->event == 'created')
                            registered the customer.
                        @elseif($audit->event == 'updated')
                            updated the customer and changed
                            @php
                                $modified = $audit->getModified();
                            @endphp
                            @foreach ($modified as $key => $val)
                                <em>{{ $key }}</em>
                                @isset($val['old']) from <code>{{ $val['old'] }}</code> @endisset
                                to <code>{{ $val['new'] }}</code>@if ($loop->last).@else,@endif
                            @endforeach
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif

        <hr>
        <div class="d-flex justify-content-between mb-3">
            <span>
                @can('update', $customer)
                    <a href="{{ route('backend.customers.edit', $customer) }}" class="btn btn-primary">Edit</a>
                @endcan
            </span>
            <a href="{{ route('backend.customers') }}" class="btn btn-link">Back to overview</a>
        </div>
    </div>

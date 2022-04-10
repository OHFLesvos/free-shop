@if($shouldDelete)
    <div class="small-container">
        <x-card title="Remove blocked phone number">
            <p class="card-text">Do you really want to unblock the phone number <strong>{{ $shouldDelete['phone'] }}</strong>?</p>
            <x-slot name="footer">
                <div class="d-flex justify-content-end">
                    <span>
                        <button
                            type="button"
                            class="btn btn-link"
                            wire:loading.attr="disabled"
                            wire:click="cancelDeletion()">
                            Cancel
                        </button>
                        <button
                            type="button"
                            class="btn btn-danger"
                            wire:target="delete"
                            wire:loading.attr="disabled"
                            wire:click="delete">
                            <x-spinner wire:loading wire:target="delete"/>
                            Delete
                        </button>
                    </span>
                </div>
            </x-slot>
        </x-card>
    </div>
@else
    <div>
        @include('livewire.backend.configuration-nav')

        @can('create', App\Models\BlockedPhoneNumber::class)
            <div class="row justify-content-center">
                <div class="col-12 col-md-9 col-lg-6">
                    <form wire:submit.prevent="submit" class="mb-4" autocomplete="off">
                        <x-card title="Add new number">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div>
                                        <label for="inputPhone" class="form-label">Phone</label>
                                        <input
                                            type="tel"
                                            class="form-control @error('phone') is-invalid @enderror"
                                            id="inputPhone"
                                            required
                                            autocomplete="off"
                                            wire:model.defer="phone">
                                        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div>
                                        <label for="inputReason" class="form-label">Reason</label>
                                        <input
                                            type="text"
                                            class="form-control @error('reason') is-invalid @enderror"
                                            id="inputReason"
                                            required
                                            autocomplete="off"
                                            wire:model.defer="reason">
                                        @error('reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
                            <x-slot name="footer">
                                <div class="text-end">
                                    <button
                                        type="submit"
                                        class="btn btn-primary"
                                        wire:target="submit"
                                        wire:loading.attr="disabled">
                                        <x-spinner wire:loading wire:target="submit"/>
                                        Register
                                    </button>
                                </div>
                            </x-slot>
                        </x-card>
                    </form>
                </div>
            </div>
        @endcan

        @if (session()->has('message'))
            <x-alert type="success" dismissible>{{ session()->get('message') }}</x-alert>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered bg-white shadow-sm">
                <thead>
                    <th>Number</th>
                    <th>Reason</th>
                    <th>Added</th>
                    <th></th>
                </thead>
                <tbody>
                    @forelse($entries as $entry)
                        <tr>
                            <td class="fit">
                                {{ $entry->phone }}
                            </td>
                            <td>
                                {{ $entry->reason }}
                            </td>
                            <td class="fit">
                                <x-date-time-info :value="$entry->created_at" line-break />
                            </td>
                            <td class="align-middle fit">
                                @can('delete', $entry)
                                    <button type="button" class="btn btn-outline-danger" wire:click="markForDeletion({{ $entry->id }})">
                                        <x-icon icon="trash" aria-label="Delete"/>
                                    </button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">
                                <em>No blocked phone numbers registered.</em>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($entries->hasPages())
            <div class="row">
                <div class="col overflow-auto">
                    {{ $entries->onEachSide(2)->links() }}
                </div>
                <div class="col-sm-auto">
                    <small>Showing {{ $entries->firstItem() }} to {{ $entries->lastItem() }} of {{ $entries->total() }} blocked phone numbers</small>
                </div>
            </div>
        @endif
    </div>
@endif

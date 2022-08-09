<div class="mx-auto" style="max-width: @if(count($oauth) > 0) 700px @else 400px @endif">
    <h1 class="display-4 text-center">{{ config('app.name') }}</h1>
    <h2 class="mb-4 display-6 text-center">Backend Login</h2>

    <div class="row my-5 gy-2 align-items-center justify-content-center">
        <div class="col-md">
            <form wire:submit.prevent="submit" autocomplete="off">
                <div class="card">
                    <div class="card-body">
                        @if(count($oauth) > 0)
                            <h5 class="card-title mb-4">Local account</h5>
                        @endif
                        <div class="mb-3">
                            <input
                                type="email"
                                class="form-control @error('email') is-invalid @enderror"
                                placeholder="Email address"
                                required
                                autofocus
                                autocomplete="off"
                                wire:loading.attr="disabled"
                                wire:model.defer="email">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <input
                                type="password"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="Password"
                                required
                                autocomplete="off"
                                wire:loading.attr="disabled"
                                wire:model.defer="password">
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        @if(count($oauth) > 0)
            <div class="col-md-auto text-center">or</div>
            <div class="col-md">
                    <div class="d-grid">
                        @foreach ($oauth as $service)
                            <a href="{{ $service['url'] }}" class="btn btn-info">
                                <x-icon type="brands" :icon="$service['icon']"/>
                                {{ $service['label'] }}
                                @if($service['domain'] != null)({{ $service['domain'] }})@endif
                            </a>
                        @endforeach
                    </div>
            </div>
        @endif
    </div>

    <p class="text-center">
        <a href="{{ route('home') }}">Return to shop</a>
        | <a href="{{ route('privacy-policy') }}">{{ __('Privacy Policy') }}</a>
    </p>
</div>

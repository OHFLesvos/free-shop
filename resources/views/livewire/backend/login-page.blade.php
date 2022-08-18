<div class="mx-auto" style="max-width: @if(count($oauth) > 0 && $hasLocalLogin) 700px @else 400px @endif">
    <h1 class="display-4 text-center">{{ setting()->get('brand.name', config('app.name')) }}</h1>
    <h2 class="mb-4 display-6 text-center">Backend Login</h2>

    @if(!$hasLocalLogin && count($oauth) == 0)
        <x-alert type="warning">No OAuth provider configured.</x-alert>
    @else
        <div class="row g-4 mb-4">
            @if($hasLocalLogin)
                <div class="col-md">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title mb-3 text-center">Login with local account</h5>
                            <form wire:submit.prevent="submit" autocomplete="off">
                                <div class="mb-3">
                                    <input
                                        type="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        placeholder="Email address"
                                        required
                                        autofocus
                                        autocomplete="username"
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
                                        autocomplete="current-password"
                                        wire:loading.attr="disabled"
                                        wire:model.defer="password">
                                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">Login</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
            @if(count($oauth) > 0)
                <div class="col-md">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title mb-3 text-center">Login with service provider</h5>
                            <div class="row row-cols-1 g-3">
                                @foreach ($oauth as $service)
                                    <div class="col d-grid">
                                        <a href="{{ $service['url'] }}" class="btn btn-outline-primary">
                                            <x-icon type="brands" :icon="$service['icon']"/>
                                            {{ $service['label'] }}
                                            @if($service['domain'] != null)({{ $service['domain'] }})@endif
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <p class="text-center">
        <a href="{{ route('home') }}">Return to shop</a>
        | <a href="{{ route('privacy-policy') }}">{{ __('Privacy Policy') }}</a>
    </p>
</div>

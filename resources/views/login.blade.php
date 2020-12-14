@extends('layouts.empty', ['title' => 'Login'])

@section('content')
    <div class="mx-auto" style="max-width: 600px">
        <h1 class="mb-4 display-4 text-center">Login</h1>
        @if ($errors->any())
            @if ($errors->has('email'))
                <x-alert type="danger">
                    {{ $errors->first('email') }}
                </x-alert>
            @endif
        @endif
        {{-- <form action="" method="POST">
            @csrf
            <div class="card shadow my-4 mx-auto" style="max-width: 26em">
                <div class="card-body">
                    <div class="form-group">
                        <input
                            type="email"
                            class="form-control @error('email') is-invalid @enderror"
                            required
                            autofocus
                            placeholder="E-Mail address"
                            autocomplete="off">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <input
                            type="password"
                            class="form-control @error('password') is-invalid @enderror"
                            required
                            autofocus
                            placeholder="Password"
                            autocomplete="off">
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <p class="text-right mb-0">
                        <button
                            type="submit"
                            class="btn btn-primary btn-block">
                            Login
                        </button>
                    </p>
                </div>
            </div>
        </form> --}}
        @if(filled(config('services.google.redirect')))
            {{-- <p class="text-center">
                or
            </p> --}}
            <p class="text-center">
                <a href="{{ route('login.google') }}" class="btn btn-primary">
                    <x-icon type="brands" icon="google"/>
                    Sign in with Google
                    @if(config('services.google.organization_domain') != null)
                    ({{ config('services.google.organization_domain') }})
                    @endif
                </a>
            </p>
        @endif
        <p class="text-center">
            <a href="{{ route('home') }}">Return to overview</a>
        </p>
    </div>
@endsection

@php
$navItems = [
    [
        'label' => 'Dashboard',
        'route' => 'backend.dashboard',
    ],
    [
        'label' => 'Orders',
        'route' => 'backend.orders',
        'authorized' => auth()
            ->user()
            ->can('viewAny', App\Models\Order::class),
    ],
    [
        'label' => 'Customers',
        'route' => 'backend.customers',
        'authorized' => auth()
            ->user()
            ->can('viewAny', App\Models\Customer::class),
    ],
    [
        'label' => 'Products',
        'route' => 'backend.products',
        'authorized' => auth()
            ->user()
            ->can('viewAny', App\Models\Product::class),
    ],
    [
        'label' => 'Stock',
        'route' => 'backend.stock',
        'authorized' => auth()
            ->user()
            ->can('manage stock'),
    ],
    [
        'label' => 'Export',
        'route' => 'backend.export',
        'authorized' => auth()
            ->user()
            ->can('export data'),
    ],
    [
        'label' => 'Reports',
        'route' => 'backend.reports',
        'authorized' => auth()
            ->user()
            ->can('view reports'),
    ],
    [
        'label' => 'Users',
        'route' => 'backend.users',
        'authorized' => auth()
            ->user()
            ->can('viewAny', App\Models\User::class),
    ],
    [
        'label' => 'Configuration',
        'route' => 'backend.configuration',
        'authorized' =>
            auth()
                ->user()
                ->can('update settings') ||
            auth()
                ->user()
                ->can('viewAny', App\Models\TextBlock::class) ||
            auth()
                ->user()
                ->can('viewAny', App\Models\BlockedPhoneNumber::class),
    ],
];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include('layouts.includes.head')

<body class="bg-light">
    <nav class="navbar navbar-expand-xl navbar-dark bg-primary shadow-sm mb-4">
        <div class="container">
            <span class="d-lg-none text-light">{{ $title }}</span>
            <a class="navbar-brand d-none d-lg-inline" href="{{ route('backend') }}">{{ setting()->get('brand.name', config('app.name')) }}</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    @foreach (collect($navItems)->filter(fn($item) => !isset($item['authorized']) || $item['authorized']) as $item)
                        <x-nav-item :item="$item"/>
                    @endforeach
                </ul>
                <ul class="navbar-nav mb-2 mb-lg-0">
                    <li class="nav-item dropdown">
                        @php
                            $active = Str::of(Request::route()->getName())->startsWith('backend.user-profile');
                        @endphp
                        <a class="nav-link @if ($active) active @endif
                            dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            @isset(Auth::user()->avatar)
                                <img
                                    src="{{ storage_url(Auth::user()->avatar) }}"
                                    alt="Avatar"
                                    class="align-top bg-white rounded-circle"
                                    height="24"
                                    width="24"/>
                            @endisset
                            <span class="align-top ms-2">{{ Str::of(Auth::user()->name)->words(1, '') }}</span>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li>
                                <a class="dropdown-item" href="{{ route('backend.user-profile') }}">User Profile</a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('backend.logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    Logout
                                </a>
                                <form id="logout-form" class="d-none" action="{{ route('backend.logout') }}"
                                    method="POST">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </li>
                    <x-nav-item :item="['label' => 'Frontend', 'route' => 'home']" target="_blank"/>
                </ul>
            </div>
        </div>
    </nav>
    <main>
        <div class="container">
            {{ $slot }}
        </div>
    </main>
    @include('layouts.includes.foot')
</body>

</html>

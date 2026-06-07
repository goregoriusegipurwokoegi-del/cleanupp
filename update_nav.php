<?php

$files = [
    'resources/views/dashboards/customer.blade.php',
    'resources/views/services/index.blade.php',
    'resources/views/orders/my-orders.blade.php',
    'resources/views/orders/history.blade.php',
    'resources/views/profile/edit.blade.php',
    'resources/views/profile/addresses/form.blade.php',
    'resources/views/orders/show.blade.php',
    'resources/views/orders/checkout.blade.php',
    'resources/views/orders/cart.blade.php',
    'resources/views/orders/receipt.blade.php'
];

$navItems = <<<'EOD'
@section('nav_items')
    <li class="nav-item"><a href="{{ route('customer.dashboard') }}" class="nav-link {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}">Dashboard</a></li>
    <li class="nav-item"><a href="{{ route('services.index') }}" class="nav-link {{ request()->routeIs('services.index') ? 'active' : '' }}">Layanan Kami</a></li>
    <li class="nav-item"><a href="{{ route('cart.index') }}" class="nav-link {{ request()->routeIs('cart.index') ? 'active' : '' }}">
        Keranjang 
        @if(Session::has('cart') && count(Session::get('cart')) > 0)
            <span style="background: var(--primary); color: #000; padding: 2px 6px; border-radius: 10px; font-size: 0.7rem; font-weight: 800; margin-left: 5px;">{{ count(Session::get('cart')) }}</span>
        @endif
    </a></li>
    <li class="nav-item"><a href="{{ route('orders.my-orders') }}" class="nav-link {{ request()->routeIs('orders.my-orders') ? 'active' : '' }}">Pesanan Saya</a></li>
    <li class="nav-item"><a href="{{ route('orders.history') }}" class="nav-link {{ request()->routeIs('orders.history') ? 'active' : '' }}">Riwayat</a></li>
    <li class="nav-item"><a href="{{ route('profile.edit') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">Pengaturan</a></li>
@endsection
EOD;

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $content = preg_replace('/@section\(\'nav_items\'\).*?@endsection/s', $navItems, $content);
        file_put_contents($file, $content);
        echo 'Updated ' . $file . PHP_EOL;
    }
}

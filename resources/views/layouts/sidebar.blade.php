<nav class="nav flex-column p-3">
    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
        <i class="fas fa-chart-line"></i> Nadzorna Plošča
    </a>
    <a class="nav-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}" href="{{ route('invoices.index') }}">
        <i class="fas fa-file-invoice"></i> Računi
    </a>
    <a class="nav-link {{ request()->routeIs('clients.*') ? 'active' : '' }}" href="{{ route('clients.index') }}">
        <i class="fas fa-users"></i> Klijenti
    </a>
    <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">
        <i class="fas fa-box"></i> Proizvodi
    </a>
    <hr>
    <a class="nav-link" href="{{ route('profile.edit') }}">
        <i class="fas fa-cog"></i> Nastavitve
    </a>
</nav>

<nav class="sidebar-nav">
    @php
        $grouped = [];
        foreach ($sidebarMenu as $item) {
            $g = $item['group'] ?? '__top__';
            $grouped[$g][] = $item;
        }
    @endphp

    @foreach ($grouped as $group => $items)
        @if ($group !== '__top__')
            <p class="sidebar-group-label">{{ $group }}</p>
        @endif
        @foreach ($items as $item)
            @php
                $routeName = $item['route'];
                $activePattern = preg_replace('/\.index$/', '.*', $routeName);
                $isActive = request()->routeIs($routeName) || request()->routeIs($activePattern);
            @endphp
            <a href="{{ route($item['route']) }}"
               class="sidebar-link {{ $isActive ? 'active' : '' }}">
                <i class="bi {{ $item['icon'] }}"></i>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    @endforeach
</nav>

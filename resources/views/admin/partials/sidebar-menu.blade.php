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
                $isExactOnly = in_array($routeName, ['admin.access.index', 'admin.rbac.index'], true);
                $isActive = $isExactOnly
                    ? request()->routeIs($routeName)
                    : (request()->routeIs($routeName) || request()->routeIs($activePattern));
                if ($routeName === 'admin.settings.index') {
                    $isActive = request()->routeIs('admin.settings.*', 'admin.rbac.*', 'admin.audit-logs.*');
                }
            @endphp
            <a href="{{ route($item['route']) }}"
               class="sidebar-link {{ $isActive ? 'active' : '' }}">
                <i class="bi {{ $item['icon'] }}"></i>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    @endforeach
</nav>

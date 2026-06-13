@php
    $placement = $placement ?? 'sidebar';
    $pickerClass = 'admin-theme-picker admin-theme-picker-' . $placement;
@endphp

<div class="{{ $pickerClass }}">
    @if ($placement === 'sidebar')
        <button class="sidebar-link sidebar-theme-trigger"
                type="button"
                data-bs-toggle="modal"
                data-bs-target="#adminThemeModal"
                aria-label="Doi mau giao dien"
                title="Doi mau giao dien">
            <i class="bi bi-palette"></i>
            <span>Mau giao dien</span>
        </button>
    @else
        <button class="btn btn-light d-inline-flex align-items-center gap-2"
                type="button"
                data-bs-toggle="modal"
                data-bs-target="#adminThemeModal"
                aria-label="Doi mau giao dien">
            <i class="bi bi-palette"></i>
        </button>
    @endif
</div>

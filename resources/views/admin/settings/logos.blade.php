@extends('layouts.admin')

@section('title', 'Cài đặt logo')
@section('page_title', 'Cài đặt logo')
@section('page_subtitle', 'Quản lý logo hiển thị trên admin, đăng nhập, kiosk, favicon và Desktop')

@push('styles')
<style>
.logo-settings{width:min(980px,100%);margin:0 auto;display:grid;gap:1rem}.logo-head{display:flex;align-items:center;justify-content:space-between;gap:1rem}.logo-head a{font-size:.8rem}.logo-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.9rem}.logo-item{display:grid;gap:.75rem;padding:1rem;border:1px solid #e1ebf6;border-radius:18px;background:#fff;box-shadow:0 10px 26px rgba(17,39,68,.04)}.logo-item-head{display:flex;align-items:center;gap:.65rem}.logo-item-icon{width:40px;height:40px;display:grid;place-items:center;border-radius:13px;color:#146bd7;background:#eaf4ff}.logo-item h2{margin:0;color:#10233d;font-size:.9rem;font-weight:600}.logo-item p{margin:.12rem 0 0;color:#7187a3;font-size:.72rem}.logo-preview{min-height:108px;display:grid;place-items:center;padding:.7rem;border:1px dashed #d3e2f1;border-radius:15px;background:#f9fcff}.logo-preview img{max-width:220px;max-height:82px;object-fit:contain}.logo-preview.favicon img{width:58px;height:58px}.logo-placeholder{color:#9aacc0;font-size:.75rem}.logo-file{width:100%;padding:.5rem;border:1px solid #dbe7f4;border-radius:12px;background:#fbfdff;color:#526b87;font-size:.78rem}.logo-actions{display:flex;align-items:center;justify-content:space-between;gap:.6rem}.logo-save{display:flex;justify-content:flex-end;padding-top:.2rem}.logo-save .btn{min-width:180px;font-size:.82rem;font-weight:600}@media(max-width:720px){.logo-grid{grid-template-columns:1fr}.logo-head{align-items:flex-start;flex-direction:column}.logo-save .btn{width:100%}}
</style>
@endpush

@section('content')
@php
    $items = [
        ['key' => 'admin_logo', 'setting' => 'admin.logo_url', 'title' => 'Logo admin', 'help' => 'Sidebar admin và ứng dụng mobile.', 'icon' => 'bi-layout-sidebar'],
        ['key' => 'login_logo', 'setting' => 'login.logo_url', 'title' => 'Logo trang đăng nhập', 'help' => 'Logo ngang trên màn hình đăng nhập.', 'icon' => 'bi-box-arrow-in-right'],
        ['key' => 'owner_logo', 'setting' => 'kiosk.owner_logo_url', 'title' => 'Logo hệ thống kiosk', 'help' => 'Logo hiển thị ngoài kiosk.', 'icon' => 'bi-display'],
        ['key' => 'favicon', 'setting' => 'app.favicon_url', 'title' => 'Favicon website', 'help' => 'Icon vuông trên tab trình duyệt.', 'icon' => 'bi-browser-chrome', 'favicon' => true],
        ['key' => 'desktop_icon', 'setting' => 'app.desktop_icon_url', 'fallback' => 'app.favicon_url', 'title' => 'Logo ngoài Desktop', 'help' => 'Icon .ico hiển thị cho shortcut Khach Moi VMS ngoài Desktop.', 'icon' => 'bi-windows', 'favicon' => true, 'desktop' => true],
    ];
@endphp

<form class="logo-settings" method="post" action="{{ route('admin.settings.logos.update') }}" enctype="multipart/form-data">
    @csrf
    @method('put')

    <div class="logo-head">
        <a class="btn btn-light" href="{{ route('admin.settings.index') }}"><i class="bi bi-grid me-1"></i>Tất cả cài đặt</a>
    </div>

    <div class="logo-grid">
        @foreach ($items as $item)
            @php
                $url = $settings[$item['setting']] ?? (($item['fallback'] ?? null) ? ($settings[$item['fallback']] ?? null) : null);
                $isIcon = in_array($item['key'], ['favicon', 'desktop_icon'], true);
                $isDesktopIcon = ! empty($item['desktop']);
            @endphp
            <section class="logo-item" data-logo-item="{{ $item['key'] }}">
                <div class="logo-item-head">
                    <span class="logo-item-icon"><i class="bi {{ $item['icon'] }}"></i></span>
                    <div><h2>{{ $item['title'] }}</h2><p>{{ $item['help'] }}</p></div>
                </div>
                <div class="logo-preview {{ ! empty($item['favicon']) ? 'favicon' : '' }}">
                    @if ($url)
                        <img src="{{ $url }}" alt="{{ $item['title'] }}">
                    @else
                        <span class="logo-placeholder">Chưa có logo</span>
                    @endif
                </div>
                <input class="logo-file" type="file" name="{{ $item['key'] }}_file" accept="{{ $isDesktopIcon ? '.ico,image/x-icon' : ($isIcon ? 'image/x-icon,image/png,image/jpeg,image/webp,image/svg+xml' : 'image/png,image/jpeg,image/webp,image/svg+xml') }}">
                <div class="logo-actions">
                    <span class="text-secondary small">Tối đa {{ $isIcon ? '1MB' : '2MB' }}</span>
                    <span class="text-secondary small">{{ $isDesktopIcon ? 'Dùng file .ico' : 'Tải file mới để thay thế' }}</span>
                </div>
            </section>
        @endforeach
    </div>

    <div class="logo-save">
        <button class="btn btn-brand" type="submit"><i class="bi bi-save2 me-1"></i>Lưu cài đặt logo</button>
    </div>
</form>
@endsection

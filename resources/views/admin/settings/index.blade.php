@extends('layouts.admin')

@section('title', 'Cài đặt')
@section('page_title', 'Cài đặt')
@section('page_subtitle', 'Quản lý cấu hình và các chức năng quản trị hệ thống')

@push('styles')
<style>
.settings-hub{width:min(920px,100%);margin:0 auto;padding:.25rem}.settings-menu{display:grid;gap:1.2rem;padding:1.35rem 1.5rem 1.5rem;border:1px solid #e2ebf5;border-radius:22px;background:#fff;box-shadow:0 12px 30px rgba(17,39,68,.045)}.settings-group{display:grid;gap:.75rem}.settings-group+.settings-group{padding-top:1.05rem;border-top:1px solid #edf2f7}.settings-group-title{margin:0;color:#71849c;font-size:.68rem;font-weight:600;letter-spacing:.055em;text-transform:uppercase}.settings-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:.65rem}.settings-tile{min-height:122px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:.65rem;padding:.7rem .45rem;border:1px solid transparent;border-radius:17px;color:#253b56;text-align:center;text-decoration:none;outline:0;transition:transform .16s ease,color .16s ease,background .16s ease,border-color .16s ease,box-shadow .16s ease}.settings-tile:hover{transform:translateY(-2px);border-color:#e3edf8;background:#f8fbff;color:var(--tile-color);box-shadow:0 9px 22px rgba(17,39,68,.055)}.settings-tile:focus-visible{border-color:var(--tile-color);box-shadow:0 0 0 4px color-mix(in srgb,var(--tile-color) 14%,transparent)}.settings-tile-icon{width:68px;height:68px;display:grid;place-items:center;border-radius:50%;color:var(--tile-color);background:var(--tile-soft);font-size:1.55rem;transition:transform .16s ease,box-shadow .16s ease}.settings-tile:hover .settings-tile-icon,.settings-tile:focus-visible .settings-tile-icon{transform:scale(1.04);box-shadow:0 9px 20px color-mix(in srgb,var(--tile-color) 15%,transparent)}.settings-tile strong{max-width:145px;color:inherit;font-size:.79rem;font-weight:500;line-height:1.35}.tile-blue{--tile-color:#2474d4;--tile-soft:#eaf3ff}.tile-cyan{--tile-color:#0891b2;--tile-soft:#e8f9fc}.tile-purple{--tile-color:#7555c7;--tile-soft:#f1edff}.tile-green{--tile-color:#16966b;--tile-soft:#eaf9f2}.tile-orange{--tile-color:#d07a16;--tile-soft:#fff4e5}@media(max-width:760px){.settings-hub{padding:0}.settings-menu{padding:1rem;border-radius:18px}.settings-grid{grid-template-columns:repeat(3,minmax(0,1fr))}}@media(max-width:480px){.settings-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:.45rem}.settings-tile{min-height:112px}.settings-tile-icon{width:62px;height:62px;font-size:1.4rem}}
</style>
@endpush

@section('content')
<div class="settings-hub">
    <div class="settings-menu">
        <section class="settings-group">
            <h3 class="settings-group-title">Thiết lập hệ thống</h3>
            <div class="settings-grid">
                <a class="settings-tile tile-blue" href="{{ route('admin.settings.kiosk') }}">
                    <span class="settings-tile-icon"><i class="bi bi-display"></i></span>
                    <strong>Kiosk & thương hiệu</strong>
                </a>
                <a class="settings-tile tile-cyan" href="{{ route('admin.settings.printer') }}">
                    <span class="settings-tile-icon"><i class="bi bi-printer"></i></span>
                    <strong>Máy in</strong>
                </a>
                <a class="settings-tile tile-purple" href="{{ route('admin.settings.logos') }}">
                    <span class="settings-tile-icon"><i class="bi bi-image"></i></span>
                    <strong>Cài đặt logo</strong>
                </a>
                <a class="settings-tile tile-green" href="{{ route('admin.settings.mail') }}">
                    <span class="settings-tile-icon"><i class="bi bi-envelope-at"></i></span>
                    <strong>Cấu hình Gmail</strong>
                </a>
                <a class="settings-tile tile-orange" href="{{ route('admin.settings.license') }}">
                    <span class="settings-tile-icon"><i class="bi bi-shield-lock"></i></span>
                    <strong>Bản quyền</strong>
                </a>
            </div>
        </section>

        <section class="settings-group">
            <h3 class="settings-group-title">Quản trị & bảo mật</h3>
            <div class="settings-grid">
                <a class="settings-tile tile-purple" href="{{ route('admin.rbac.index') }}">
                    <span class="settings-tile-icon"><i class="bi bi-shield-check"></i></span>
                    <strong>Phân quyền</strong>
                </a>
                <a class="settings-tile tile-green" href="{{ route('admin.rbac.accounts.index') }}">
                    <span class="settings-tile-icon"><i class="bi bi-person"></i></span>
                    <strong>Tài khoản nhân viên</strong>
                </a>
                <a class="settings-tile tile-orange" href="{{ route('admin.audit-logs.index') }}">
                    <span class="settings-tile-icon"><i class="bi bi-clock-history"></i></span>
                    <strong>Nhật ký hệ thống</strong>
                </a>
            </div>
        </section>
    </div>
</div>
@endsection

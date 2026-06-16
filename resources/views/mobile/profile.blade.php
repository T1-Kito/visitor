@extends('layouts.mobile')

@section('title', 'Hồ sơ')

@section('content')
    @php
        $employee = $profileEmployee;
        $roleNames = $profileUser->roles->pluck('name')->filter()->values();
        $displayName = trim((string) ($employee?->name ?: $profileUser->name));
        $initial = mb_strtoupper(mb_substr($displayName !== '' ? $displayName : 'U', 0, 1));
    @endphp

    <div class="m-page-head profile-page-head">
        <a href="{{ route('mobile.home') }}" aria-label="Quay lại"><i class="bi bi-chevron-left"></i></a>
        <div>
            <h1>Hồ sơ của tôi</h1>
            <p>Thông tin tài khoản và đơn vị công tác.</p>
        </div>
    </div>

    <section class="profile-identity">
        <div class="profile-avatar" aria-hidden="true">{{ $initial }}</div>
        <div class="profile-main">
            <h2>{{ $displayName !== '' ? $displayName : 'Người dùng' }}</h2>
            <p>{{ $profileUser->email }}</p>
            <div class="profile-badges">
                @forelse ($roleNames as $roleName)
                    <span>{{ $roleName }}</span>
                @empty
                    <span>Chưa gán vai trò</span>
                @endforelse
            </div>
        </div>
        <span class="profile-status {{ $profileUser->is_active ? 'active' : 'inactive' }}">
            <i class="bi {{ $profileUser->is_active ? 'bi-check-circle' : 'bi-lock' }}"></i>
            {{ $profileUser->is_active ? 'Đang hoạt động' : 'Đã khóa' }}
        </span>
    </section>

    <section class="m-section profile-section">
        <div class="m-section-head">
            <div>
                <h2>Thông tin tài khoản</h2>
                <span>Dữ liệu dùng để đăng nhập hệ thống</span>
            </div>
        </div>

        <div class="profile-info-list">
            <div class="profile-info-row">
                <span class="profile-info-icon"><i class="bi bi-envelope"></i></span>
                <div>
                    <small>Email đăng nhập</small>
                    <p>{{ $profileUser->email }}</p>
                </div>
            </div>
            <div class="profile-info-row">
                <span class="profile-info-icon"><i class="bi bi-person-badge"></i></span>
                <div>
                    <small>Nhân viên liên kết</small>
                    <p>{{ $employee?->name ?: 'Chưa liên kết hồ sơ nhân viên' }}</p>
                </div>
            </div>
            <div class="profile-info-row">
                <span class="profile-info-icon"><i class="bi bi-briefcase"></i></span>
                <div>
                    <small>Chức danh</small>
                    <p>{{ $employee?->job_title ?: 'Chưa cập nhật' }}</p>
                </div>
            </div>
            <div class="profile-info-row">
                <span class="profile-info-icon"><i class="bi bi-building"></i></span>
                <div>
                    <small>Phòng ban</small>
                    <p>{{ $employee?->department?->name ?: 'Chưa cập nhật' }}</p>
                </div>
            </div>
            <div class="profile-info-row">
                <span class="profile-info-icon"><i class="bi bi-telephone"></i></span>
                <div>
                    <small>Số điện thoại</small>
                    <p>{{ $employee?->phone ?: 'Chưa cập nhật' }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="m-section profile-section">
        <div class="m-section-head">
            <div>
                <h2>Ứng dụng</h2>
                <span>Thông tin hệ thống đang sử dụng</span>
            </div>
        </div>

        <div class="profile-app-row">
            @if (! empty($adminBrand['logo_url']))
                <img src="{{ $adminBrand['logo_url'] }}" alt="{{ $adminBrand['name'] }}">
            @else
                <span class="profile-app-mark">{{ $adminBrand['initials'] }}</span>
            @endif
            <div>
                <p>{{ $adminBrand['name'] }}</p>
                <small>{{ $adminBrand['subtitle'] }}</small>
            </div>
        </div>
    </section>

    <form class="profile-logout" action="{{ route('admin.logout') }}" method="post">
        @csrf
        <button type="submit">
            <i class="bi bi-box-arrow-right"></i>
            <span>Đăng xuất</span>
        </button>
    </form>
@endsection

@push('styles')
    <style>
        .profile-page-head {
            margin-bottom: 2px;
        }

        .profile-identity {
            position: relative;
            display: grid;
            grid-template-columns: 66px minmax(0, 1fr);
            align-items: center;
            gap: 14px;
            padding: 18px 16px 48px;
            border-radius: 24px;
            color: #111827;
            background: var(--m-secondary);
            box-shadow: 0 14px 30px rgba(212, 5, 17, 0.1);
        }

        .profile-avatar {
            width: 66px;
            height: 66px;
            display: grid;
            place-items: center;
            border: 1px solid rgba(212, 5, 17, 0.2);
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.72);
            color: var(--m-primary);
            font-size: 1.7rem;
            font-weight: 500;
        }

        .profile-main {
            min-width: 0;
        }

        .profile-main h2,
        .profile-main p {
            margin: 0;
        }

        .profile-main h2 {
            font-size: 1.12rem;
            font-weight: 600;
        }

        .profile-main p {
            margin-top: 4px;
            overflow: hidden;
            color: #5f5130;
            font-size: 0.76rem;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .profile-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-top: 10px;
        }

        .profile-badges span {
            padding: 4px 8px;
            border: 1px solid rgba(212, 5, 17, 0.16);
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.68);
            font-size: 0.66rem;
            font-weight: 500;
        }

        .profile-status {
            position: absolute;
            right: 14px;
            bottom: 13px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 0.68rem;
            font-weight: 500;
        }

        .profile-status.active {
            color: #dfffea;
            background: rgba(22, 163, 74, 0.3);
        }

        .profile-status.inactive {
            color: #ffe4e6;
            background: rgba(220, 38, 38, 0.28);
        }

        .profile-section {
            padding: 16px;
        }

        .profile-info-list {
            display: grid;
        }

        .profile-info-row {
            display: grid;
            grid-template-columns: 38px minmax(0, 1fr);
            align-items: center;
            gap: 11px;
            min-height: 62px;
            border-bottom: 1px solid var(--m-line);
        }

        .profile-info-row:last-child {
            border-bottom: 0;
        }

        .profile-info-icon {
            width: 36px;
            height: 36px;
            display: grid;
            place-items: center;
            border-radius: 12px;
            color: var(--m-blue);
            background: #edf6ff;
            font-size: 0.95rem;
        }

        .profile-info-row small,
        .profile-info-row p {
            margin: 0;
        }

        .profile-info-row small {
            color: var(--m-muted);
            font-size: 0.69rem;
        }

        .profile-info-row p {
            margin-top: 3px;
            color: var(--m-text);
            font-size: 0.82rem;
            font-weight: 500;
            overflow-wrap: anywhere;
        }

        .profile-app-row {
            display: grid;
            grid-template-columns: 58px minmax(0, 1fr);
            align-items: center;
            gap: 12px;
        }

        .profile-app-row img {
            width: 58px;
            height: 46px;
            object-fit: contain;
        }

        .profile-app-mark {
            width: 52px;
            height: 52px;
            display: grid;
            place-items: center;
            border-radius: 16px;
            color: #fff;
            background: var(--m-blue);
            font-size: 0.8rem;
            font-weight: 600;
        }

        .profile-app-row p,
        .profile-app-row small {
            margin: 0;
        }

        .profile-app-row p {
            color: var(--m-text);
            font-size: 0.86rem;
            font-weight: 500;
        }

        .profile-app-row small {
            display: block;
            margin-top: 3px;
            color: var(--m-muted);
            font-size: 0.7rem;
        }

        .profile-logout {
            margin: 0;
        }

        .profile-logout button {
            width: 100%;
            min-height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: 1px solid #fecaca;
            border-radius: 16px;
            color: #dc2626;
            background: #fff;
            font: inherit;
            font-size: 0.84rem;
            font-weight: 500;
        }
    </style>
@endpush

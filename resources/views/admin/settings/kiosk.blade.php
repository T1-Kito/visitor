@extends('layouts.admin')

@section('title', 'Cài đặt kiosk')
@section('page_title', 'Cài đặt kiosk')
@section('page_subtitle', 'Tùy chỉnh nội dung hiển thị trên màn hình tiếp đón khách')

@push('styles')
<style>
.ks-admin{display:grid;gap:1rem;color:#10233d}.ks-admin-hero{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:1rem;border:1px solid #e2edf8;border-radius:22px;background:linear-gradient(135deg,#ffffff,#f6fbff);box-shadow:0 14px 34px rgba(17,39,68,.05)}.ks-admin-title{display:flex;align-items:center;gap:.8rem}.ks-admin-mark{width:46px;height:46px;display:grid;place-items:center;border-radius:16px;background:#eaf4ff;color:#1976d2}.ks-admin-title h1{margin:0;color:#10233d;font-size:1.18rem;font-weight:600;letter-spacing:0}.ks-admin-title p{margin:.15rem 0 0;color:#6f839f;font-size:.82rem}.ks-admin-actions{display:flex;gap:.5rem;flex-wrap:wrap}.ks-soft-btn{min-height:40px;display:inline-flex;align-items:center;justify-content:center;gap:.42rem;padding:.55rem .85rem;border:1px solid #dbe7f4;border-radius:13px;background:#fff;color:#2c4967;font-size:.82rem;font-weight:500;text-decoration:none}.ks-soft-btn.primary{border:0;color:#fff;background:linear-gradient(135deg,#1976d2,#11a9c7);box-shadow:0 12px 24px rgba(20,107,215,.13)}.ks-settings-grid{display:grid;grid-template-columns:minmax(0,1fr) 390px;gap:1rem;align-items:start}.ks-form-stack{display:grid;gap:1rem}.ks-card{border:1px solid #e2edf8;border-radius:20px;background:#fff;box-shadow:0 12px 30px rgba(17,39,68,.045);overflow:hidden}.ks-card-head{display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;padding:.95rem 1rem;border-bottom:1px solid #eef4fb}.ks-card-head h2{margin:0;color:#10233d;font-size:.98rem;font-weight:600;letter-spacing:0}.ks-card-head p{margin:.16rem 0 0;color:#7187a3;font-size:.76rem}.ks-card-body{padding:1rem}.ks-grid-2{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.8rem}.ks-field{display:grid;gap:.35rem}.ks-field.full{grid-column:1/-1}.ks-field label{margin:0;color:#526b87;font-size:.76rem;font-weight:500}.ks-field .required{color:#ef4444}.ks-field input:not([type=file]),.ks-field textarea{width:100%;border:1px solid #dbe7f4;border-radius:13px;background:#fbfdff;color:#10233d;font-size:.86rem;font-weight:400}.ks-field input:not([type=file]){min-height:42px;padding:.62rem .75rem}.ks-field textarea{min-height:92px;padding:.7rem .75rem;resize:vertical}.ks-field input:focus,.ks-field textarea:focus{outline:0;border-color:#1976d2;box-shadow:0 0 0 4px rgba(25,118,210,.1)}.ks-help{color:#7c91aa;font-size:.72rem}.ks-upload{display:grid;gap:.55rem}.ks-current-media{display:inline-flex;align-items:center;gap:.55rem;width:max-content;max-width:100%;padding:.38rem .55rem;border:1px solid #edf3fb;border-radius:12px;background:#fbfdff;color:#7187a3;font-size:.76rem}.ks-current-media img{width:42px;height:32px;object-fit:contain;border-radius:8px;background:#fff}.ks-current-media.bg img{width:58px;object-fit:cover}.ks-file{min-height:42px;padding:.48rem .6rem;border:1px solid #dbe7f4;border-radius:13px;background:#fbfdff;color:#526b87;font-size:.82rem}.ks-color-row{display:grid;grid-template-columns:52px minmax(0,1fr);gap:.55rem;max-width:260px}.ks-color-row input[type=color]{width:52px;height:42px;padding:.25rem;border:1px solid #dbe7f4;border-radius:13px;background:#fff}.ks-preview-wrap{position:sticky;top:1rem;display:grid;gap:1rem}.ks-preview{position:relative;overflow:hidden;border:1px solid #dbe7f4;border-radius:24px;background:linear-gradient(135deg,#f7fdff,#eef8ff);box-shadow:0 18px 42px rgba(17,39,68,.07)}.ks-preview::before{content:"";position:absolute;inset:auto -80px -100px auto;width:220px;height:220px;border-radius:999px;background:rgba(20,107,215,.1)}.ks-preview-inner{position:relative;padding:1rem}.ks-preview-top{display:flex;align-items:center;justify-content:space-between;gap:.8rem}.ks-preview-badge{display:inline-flex;align-items:center;gap:.3rem;padding:.22rem .55rem;border-radius:999px;background:#fff;color:#1976d2;font-size:.68rem;font-weight:500;border:1px solid #dbe7f4}.ks-preview-logo{width:42px;height:42px;display:grid;place-items:center;border-radius:14px;background:#fff;border:1px solid #dbe7f4;overflow:hidden}.ks-preview-logo img{width:100%;height:100%;object-fit:contain}.ks-preview-logo span{font-size:.7rem;color:#1976d2;font-weight:600}.ks-preview-title{margin:1rem 0 0;color:#10233d;font-size:1.02rem;font-weight:600}.ks-preview-subtitle{margin:.2rem 0 0;color:#657d99;font-size:.8rem;line-height:1.45}.ks-preview-welcome{margin:.75rem 0 0;padding:.7rem;border:1px solid rgba(216,231,244,.9);border-radius:16px;background:rgba(255,255,255,.72)}.ks-preview-welcome strong{display:block;color:#10233d;font-size:.82rem;font-weight:600}.ks-preview-welcome span{display:block;margin-top:.2rem;color:#657d99;font-size:.74rem;line-height:1.4}.ks-preview-lines{display:grid;gap:.45rem;margin-top:.8rem}.ks-preview-lines div{display:flex;align-items:center;justify-content:space-between;gap:.8rem;padding:.52rem .6rem;border:1px solid rgba(216,231,244,.9);border-radius:13px;background:rgba(255,255,255,.82);font-size:.74rem}.ks-preview-lines span{color:#7187a3}.ks-preview-lines strong{color:#10233d;font-weight:500;text-align:right}.ks-save-card{padding:1rem;border:1px solid #e2edf8;border-radius:20px;background:#fff;box-shadow:0 12px 30px rgba(17,39,68,.045)}.ks-save-card p{margin:0 0 .75rem;color:#7187a3;font-size:.78rem;line-height:1.45}@media(max-width:1200px){.ks-settings-grid{grid-template-columns:1fr}.ks-preview-wrap{position:static}}@media(max-width:768px){.ks-admin-hero{align-items:flex-start;flex-direction:column}.ks-grid-2{grid-template-columns:1fr}.ks-admin-actions{width:100%}.ks-soft-btn{width:100%}}
.ks-media-row{display:flex;align-items:center;gap:.55rem;flex-wrap:wrap}.ks-remove-media{min-height:34px;display:inline-flex;align-items:center;gap:.35rem;padding:.42rem .62rem;border:1px solid #fecaca;border-radius:11px;background:#fff7f7;color:#dc2626;font-size:.76rem;font-weight:500}.ks-remove-media:hover{background:#fff1f2}.ks-remove-media.is-undone{border-color:#bfdbfe;background:#eff6ff;color:#146bd7}.ks-media-row.is-removed .ks-current-media{display:none}.ks-remove-note{display:none;color:#dc2626;font-size:.72rem}.ks-media-row.is-removed+.ks-remove-note{display:block}.ks-logo-grid{grid-column:1/-1;display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.85rem}.ks-logo-grid .ks-field{padding:.78rem;border:1px solid #edf3fb;border-radius:16px;background:#fbfdff}.ks-current-media{min-width:178px;min-height:64px;padding:.5rem .7rem;border-color:#dbe7f4;background:#fff}.ks-current-media img{width:112px;height:42px;object-fit:contain;border-radius:8px;background:#fff}.ks-current-media span{white-space:nowrap}.ks-current-media.bg img{width:72px;height:42px;object-fit:cover}.ks-current-media.favicon{min-width:118px}.ks-current-media.favicon img{width:38px;height:38px}.ks-preview-logos{display:flex;align-items:center;gap:.5rem}.ks-preview-logo-separator{width:1px;height:30px;background:#dbe7f4}@media(max-width:768px){.ks-logo-grid{grid-template-columns:1fr}}
.ks-logo-grid .ks-current-media{display:grid;place-items:center;width:100%;min-height:88px;padding:.35rem;overflow:hidden}.ks-logo-grid .ks-current-media img{width:190px;height:70px;max-width:100%;object-fit:contain;transform:scale(1.2);transform-origin:center}.ks-logo-grid .ks-current-media span{display:none}.ks-logo-grid .ks-current-media.favicon{width:max-content;min-height:64px}.ks-logo-grid .ks-current-media.favicon img{width:44px;height:44px;transform:none}.ks-color-palette{grid-column:1/-1;display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:.75rem}.ks-color-palette .ks-field{padding:.78rem;border:1px solid #edf3fb;border-radius:16px;background:#fbfdff}.ks-color-palette .ks-color-row{max-width:none;grid-template-columns:48px minmax(0,1fr)}.ks-color-palette .ks-color-row input[type=color]{width:48px}@media(max-width:992px){.ks-color-palette{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:560px){.ks-color-palette{grid-template-columns:1fr}}
</style>
@endpush

@section('content')
@php
    $value = static fn (string $key, ?string $fallback = '') => old(str_replace('kiosk.', '', $key), $settings[$key] ?? $fallback);
    $adminLogoUrl = $value('admin.logo_url');
    $loginLogoUrl = $settings['login.logo_url'] ?? null;
    $loginTitle = old('login_title', $settings['login.title'] ?? 'Visitor Management System');
    $loginSubtitle = old('login_subtitle', $settings['login.subtitle'] ?? 'Đăng nhập vào hệ thống vận hành');
    $ownerLogoUrl = $value('kiosk.owner_logo_url');
    $customerLogoUrl = $value('kiosk.customer_logo_url') ?: $value('kiosk.logo_url');
    $faviconUrl = $value('app.favicon_url');
    $backgroundUrl = $value('kiosk.background_url');
    $primaryColor = $value('kiosk.primary_color', '#146bd7');
    $secondaryColor = $value('kiosk.secondary_color', '#0cb4d8');
    $backgroundColor = $value('kiosk.background_color', '#f4f8fd');
    $surfaceColor = $value('kiosk.surface_color', '#ffffff');
    $welcomeTitle = $value('kiosk.welcome_title') ?: 'Chào mừng bạn đến '.$value('kiosk.company_name', 'Công ty ABC');
@endphp

<form class="ks-admin" method="post" action="{{ route('admin.settings.kiosk.update') }}" enctype="multipart/form-data">
    @csrf
    @method('put')

    <section class="ks-admin-hero">
        <div class="ks-admin-title">
            <div class="ks-admin-mark"><i class="bi bi-display"></i></div>
            <div>
                <h1>Thiết lập giao diện kiosk</h1>
                <p>Quản lý nội dung, nhận diện và thông tin hỗ trợ trên màn hình tiếp đón khách.</p>
            </div>
        </div>
        <div class="ks-admin-actions">
            <a class="ks-soft-btn" href="{{ route('admin.settings.index') }}">
                <i class="bi bi-grid"></i>Tất cả cài đặt
            </a>
            <a class="ks-soft-btn" href="{{ route('kiosk.index') }}" target="_blank">
                <i class="bi bi-box-arrow-up-right"></i>Xem kiosk
            </a>
            <button class="ks-soft-btn primary" type="submit">
                <i class="bi bi-save2"></i>Lưu cấu hình
            </button>
        </div>
    </section>

    <div class="ks-settings-grid">
        <div class="ks-form-stack">
            <section class="ks-card">
                <div class="ks-card-head">
                    <div>
                        <h2>Nội dung hiển thị</h2>
                        <p>Các dòng chữ chính trên màn hình kiosk public.</p>
                    </div>
                </div>
                <div class="ks-card-body ks-grid-2">
                    <div class="ks-field">
                        <label>Tên công ty <span class="required">*</span></label>
                        <input name="company_name" value="{{ $value('kiosk.company_name', 'Công ty ABC') }}" required>
                    </div>
                    <div class="ks-field">
                        <label>Tên hệ thống kiosk <span class="required">*</span></label>
                        <input name="system_name" value="{{ $value('kiosk.system_name', 'VMS Kiosk') }}" required>
                    </div>
                    <div class="ks-field full">
                        <label>Subtitle kiosk <span class="required">*</span></label>
                        <input name="subtitle" value="{{ $value('kiosk.subtitle', 'Giao diện tự động cho khách đến công ty') }}" required>
                    </div>
                    <div class="ks-field full">
                        <label>Câu chào mừng</label>
                        <input name="welcome_title" value="{{ $value('kiosk.welcome_title') }}" placeholder="Để trống để tự động: Chào mừng bạn đến [Tên công ty]">
                    </div>
                    <div class="ks-field full">
                        <label>Mô tả ngắn <span class="required">*</span></label>
                        <textarea name="welcome_description" required>{{ $value('kiosk.welcome_description', 'Vui lòng đăng ký thông tin hoặc check-in bằng QR để được hỗ trợ nhanh chóng.') }}</textarea>
                    </div>
                    <div class="ks-field">
                        <label>Tiêu đề trang đăng nhập <span class="required">*</span></label>
                        <input name="login_title" value="{{ $loginTitle }}" required>
                    </div>
                    <div class="ks-field">
                        <label>Mô tả trang đăng nhập <span class="required">*</span></label>
                        <input name="login_subtitle" value="{{ $loginSubtitle }}" required>
                    </div>
                </div>
            </section>

            <section class="ks-card">
                <div class="ks-card-head">
                    <div>
                        <h2>Hỗ trợ</h2>
                        <p>Thông tin lễ tân/bảo vệ và giờ làm việc hiển thị trên kiosk.</p>
                    </div>
                </div>
                <div class="ks-card-body ks-grid-2">
                    <div class="ks-field">
                        <label>Hotline lễ tân / bảo vệ <span class="required">*</span></label>
                        <input name="hotline" value="{{ $value('kiosk.hotline', '1900 0000') }}" required>
                    </div>
                    <div class="ks-field">
                        <label>Giờ làm việc <span class="required">*</span></label>
                        <input name="working_hours" value="{{ $value('kiosk.working_hours', '07:30 - 18:00') }}" required>
                    </div>
                </div>
            </section>

            <section class="ks-card">
                <div class="ks-card-head">
                    <div>
                        <h2>Cài đặt logo</h2>
                        <p>Tách riêng logo admin, logo kiosk, logo khách hàng và favicon website.</p>
                    </div>
                </div>
                <div class="ks-card-body ks-grid-2">
                    <div class="ks-logo-grid">
                        <div class="ks-field ks-upload">
                            <label>Logo admin</label>
                            <input type="hidden" name="remove_admin_logo" value="0" data-remove-input="admin_logo">
                            @if ($adminLogoUrl)
                                <div class="ks-media-row" data-media-row="admin_logo">
                                    <div class="ks-current-media">
                                        <img src="{{ $adminLogoUrl }}" alt="Logo admin hiện tại">
                                        <span>Logo admin</span>
                                    </div>
                                    <button class="ks-remove-media" type="button" data-remove-media="admin_logo" data-file-input="admin_logo_file">
                                        <i class="bi bi-trash3"></i><span>Xóa logo</span>
                                    </button>
                                </div>
                                <div class="ks-remove-note">Logo admin sẽ được xóa sau khi bạn bấm lưu cấu hình.</div>
                            @endif
                            <input class="ks-file" type="file" name="admin_logo_file" accept="image/png,image/jpeg,image/webp,image/svg+xml">
                            <div class="ks-help">Dùng cho sidebar admin và mobile app. Khuyến nghị 240x80px.</div>
                        </div>

                        <div class="ks-field ks-upload">
                            <label>Logo trang đăng nhập</label>
                            <input type="hidden" name="remove_login_logo" value="0" data-remove-input="login_logo">
                            @if ($loginLogoUrl)
                                <div class="ks-media-row" data-media-row="login_logo">
                                    <div class="ks-current-media">
                                        <img src="{{ $loginLogoUrl }}" alt="Logo trang đăng nhập hiện tại">
                                        <span>Logo login</span>
                                    </div>
                                    <button class="ks-remove-media" type="button" data-remove-media="login_logo" data-file-input="login_logo_file">
                                        <i class="bi bi-trash3"></i><span>Xóa logo</span>
                                    </button>
                                </div>
                                <div class="ks-remove-note">Logo trang đăng nhập sẽ được xóa sau khi bạn bấm lưu cấu hình.</div>
                            @endif
                            <input class="ks-file" type="file" name="login_logo_file" accept="image/png,image/jpeg,image/webp,image/svg+xml">
                            <div class="ks-help">Dùng riêng cho màn hình đăng nhập. Khuyến nghị logo ngang 240x80px.</div>
                        </div>

                        <div class="ks-field ks-upload">
                            <label>Logo hệ thống kiosk</label>
                            <input type="hidden" name="remove_owner_logo" value="0" data-remove-input="owner_logo">
                            @if ($ownerLogoUrl)
                                <div class="ks-media-row" data-media-row="owner_logo">
                                    <div class="ks-current-media">
                                        <img src="{{ $ownerLogoUrl }}" alt="Logo hệ thống hiện tại">
                                        <span>Logo hệ thống</span>
                                    </div>
                                    <button class="ks-remove-media" type="button" data-remove-media="owner_logo" data-file-input="owner_logo_file">
                                        <i class="bi bi-trash3"></i><span>Xóa logo</span>
                                    </button>
                                </div>
                                <div class="ks-remove-note">Logo hệ thống sẽ được xóa sau khi bạn bấm lưu cấu hình.</div>
                            @endif
                            <input class="ks-file" type="file" name="owner_logo_file" accept="image/png,image/jpeg,image/webp,image/svg+xml">
                            <div class="ks-help">Logo của mình/đơn vị triển khai hiển thị ngoài kiosk.</div>
                        </div>

                        <div class="ks-field ks-upload">
                            <label>Logo khách hàng</label>
                            <input type="hidden" name="remove_customer_logo" value="0" data-remove-input="customer_logo">
                            @if ($customerLogoUrl)
                                <div class="ks-media-row" data-media-row="customer_logo">
                                    <div class="ks-current-media">
                                        <img src="{{ $customerLogoUrl }}" alt="Logo khách hàng hiện tại">
                                        <span>Logo khách hàng</span>
                                    </div>
                                    <button class="ks-remove-media" type="button" data-remove-media="customer_logo" data-file-input="customer_logo_file">
                                        <i class="bi bi-trash3"></i><span>Xóa logo</span>
                                    </button>
                                </div>
                                <div class="ks-remove-note">Logo khách hàng sẽ được xóa sau khi bạn bấm lưu cấu hình.</div>
                            @endif
                            <input class="ks-file" type="file" name="customer_logo_file" accept="image/png,image/jpeg,image/webp,image/svg+xml">
                            <div class="ks-help">Logo công ty khách hàng hiển thị cạnh logo hệ thống ngoài kiosk.</div>
                        </div>

                        <div class="ks-field ks-upload">
                            <label>Favicon website</label>
                            <input type="hidden" name="remove_favicon" value="0" data-remove-input="favicon">
                            @if ($faviconUrl)
                                <div class="ks-media-row" data-media-row="favicon">
                                    <div class="ks-current-media favicon">
                                        <img src="{{ $faviconUrl }}" alt="Favicon hiện tại">
                                        <span>Favicon</span>
                                    </div>
                                    <button class="ks-remove-media" type="button" data-remove-media="favicon" data-file-input="favicon_file">
                                        <i class="bi bi-trash3"></i><span>Xóa favicon</span>
                                    </button>
                                </div>
                                <div class="ks-remove-note">Favicon sẽ được xóa sau khi bạn bấm lưu cấu hình.</div>
                            @endif
                            <input class="ks-file" type="file" name="favicon_file" accept="image/x-icon,image/png,image/jpeg,image/webp,image/svg+xml">
                            <div class="ks-help">Icon tab trình duyệt. Khuyến nghị vuông 64x64px hoặc SVG/ICO.</div>
                        </div>
                    </div>
                    <div class="ks-field full ks-upload">
                        <label>Ảnh nền kiosk</label>
                        <input type="hidden" name="remove_background" value="0" data-remove-input="background">
                        @if ($backgroundUrl)
                            <div class="ks-media-row" data-media-row="background">
                                <div class="ks-current-media bg">
                                    <img src="{{ $backgroundUrl }}" alt="Ảnh nền hiện tại">
                                    <span>Ảnh nền hiện tại</span>
                                </div>
                                <button class="ks-remove-media" type="button" data-remove-media="background" data-file-input="background_file">
                                    <i class="bi bi-trash3"></i><span>Xóa ảnh nền</span>
                                </button>
                            </div>
                            <div class="ks-remove-note">Ảnh nền sẽ được xóa sau khi bạn bấm lưu cấu hình.</div>
                        @endif
                        <input class="ks-file" type="file" name="background_file" accept="image/png,image/jpeg,image/webp">
                        <div class="ks-help">Hỗ trợ JPG, PNG, WEBP. Để trống nếu muốn giữ ảnh nền cũ.</div>
                    </div>
                    <div class="ks-color-palette">
                        <div class="ks-field">
                            <label>Màu chính <span class="required">*</span></label>
                            <div class="ks-color-row">
                                <input type="color" name="primary_color" value="{{ $primaryColor }}" title="Chọn màu chính" data-kiosk-color-input>
                                <input value="{{ $primaryColor }}" readonly>
                            </div>
                            <div class="ks-help">Nút chính, tab đang chọn, icon nổi bật.</div>
                        </div>
                        <div class="ks-field">
                            <label>Màu phụ <span class="required">*</span></label>
                            <div class="ks-color-row">
                                <input type="color" name="secondary_color" value="{{ $secondaryColor }}" title="Chọn màu phụ" data-kiosk-color-input>
                                <input value="{{ $secondaryColor }}" readonly>
                            </div>
                            <div class="ks-help">Dùng cho gradient và điểm nhấn nhẹ.</div>
                        </div>
                        <div class="ks-field">
                            <label>Nền kiosk <span class="required">*</span></label>
                            <div class="ks-color-row">
                                <input type="color" name="background_color" value="{{ $backgroundColor }}" title="Chọn màu nền kiosk" data-kiosk-color-input>
                                <input value="{{ $backgroundColor }}" readonly>
                            </div>
                            <div class="ks-help">Màu nền tổng thể ngoài kiosk.</div>
                        </div>
                        <div class="ks-field">
                            <label>Nền khung <span class="required">*</span></label>
                            <div class="ks-color-row">
                                <input type="color" name="surface_color" value="{{ $surfaceColor }}" title="Chọn màu nền khung" data-kiosk-color-input>
                                <input value="{{ $surfaceColor }}" readonly>
                            </div>
                            <div class="ks-help">Màu nền các card/form chính.</div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <aside class="ks-preview-wrap">
            <section class="ks-preview">
                <div class="ks-preview-inner">
                    <div class="ks-preview-top">
                        <span class="ks-preview-badge"><i class="bi bi-broadcast"></i>Public kiosk</span>
                        <div class="ks-preview-logos">
                            @if ($ownerLogoUrl)
                                <div class="ks-preview-logo">
                                    <img src="{{ $ownerLogoUrl }}" alt="Logo hệ thống">
                                </div>
                            @endif
                            @if ($ownerLogoUrl && $customerLogoUrl)
                                <span class="ks-preview-logo-separator" aria-hidden="true"></span>
                            @endif
                            <div class="ks-preview-logo">
                                @if ($customerLogoUrl)
                                    <img src="{{ $customerLogoUrl }}" alt="Logo khách hàng">
                                @else
                                    <span>VMS</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <h3 class="ks-preview-title">{{ $value('kiosk.system_name', 'VMS Kiosk') }}</h3>
                    <p class="ks-preview-subtitle">{{ $value('kiosk.subtitle', 'Giao diện tự động cho khách đến công ty') }}</p>
                    <div class="ks-preview-welcome">
                        <strong>{{ $welcomeTitle }}</strong>
                        <span>{{ $value('kiosk.welcome_description', 'Vui lòng đăng ký thông tin hoặc check-in bằng QR để được hỗ trợ nhanh chóng.') }}</span>
                    </div>
                    <div class="ks-preview-lines">
                        <div><span>Công ty</span><strong>{{ $value('kiosk.company_name', 'Công ty ABC') }}</strong></div>
                        <div><span>Hotline</span><strong>{{ $value('kiosk.hotline', '1900 0000') }}</strong></div>
                        <div><span>Giờ</span><strong>{{ $value('kiosk.working_hours', '07:30 - 18:00') }}</strong></div>
                    </div>
                </div>
            </section>

            <section class="ks-save-card">
                <p>Nhấn lưu để áp dụng nội dung mới cho màn hình kiosk public.</p>
                <button class="ks-soft-btn primary w-100" type="submit">
                    <i class="bi bi-save2"></i>Lưu cấu hình kiosk
                </button>
            </section>
        </aside>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.querySelectorAll('[data-remove-media]').forEach((button) => {
    const key = button.dataset.removeMedia;
    const input = document.querySelector(`[data-remove-input="${key}"]`);
    const row = document.querySelector(`[data-media-row="${key}"]`);
    const label = button.querySelector('span');
    const originalText = label?.textContent || '';
    const fileInput = document.querySelector(`input[name="${button.dataset.fileInput || `${key}_file`}"]`);

    button.addEventListener('click', () => {
        const willRemove = input.value !== '1';
        input.value = willRemove ? '1' : '0';
        row?.classList.toggle('is-removed', willRemove);
        button.classList.toggle('is-undone', willRemove);
        if (label) {
            label.textContent = willRemove ? 'Hoàn tác' : originalText;
        }
        if (willRemove && fileInput) {
            fileInput.value = '';
        }
    });

    fileInput?.addEventListener('change', () => {
        if (! fileInput.files.length) {
            return;
        }

        input.value = '0';
        row?.classList.remove('is-removed');
        button.classList.remove('is-undone');
        if (label) {
            label.textContent = originalText;
        }
    });
});

document.querySelectorAll('[data-kiosk-color-input]').forEach((input) => {
    const text = input.parentElement?.querySelector('input[readonly]');
    input.addEventListener('input', () => {
        if (text) {
            text.value = input.value.toUpperCase();
        }
    });
});
</script>
@endpush

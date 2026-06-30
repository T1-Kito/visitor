@extends('layouts.admin')

@section('title', 'Đăng ký online | Visitor Management')
@section('page_title', 'Đăng ký online')
@section('page_subtitle', 'Gửi link kiosk để khách tự đăng ký trước khi đến')


@push('styles')
<style>
    .online-share { display: grid; grid-template-columns: minmax(0, 1.45fr) minmax(300px, .75fr); align-items: start; gap: 22px; }
    .online-card { border: 1px solid #e2e8f0; border-radius: 22px; background: #fff; box-shadow: 0 12px 34px rgba(15, 23, 42, .06); }
    .online-main { padding: 20px; }
    .online-kicker { display: inline-flex; align-items: center; gap: 8px; padding: 7px 11px; border-radius: 999px; background: color-mix(in srgb, var(--admin-secondary-color) 20%, #fff); color: #7a4b00; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: .04em; }
    .online-url-wrap { display: flex; align-items: center; gap: 10px; margin-top: 14px; padding: 6px 6px 6px 12px; border: 1px solid #dbe4ef; border-radius: 15px; background: #f8fafc; }
    .online-url-wrap > i { color: var(--admin-primary-color); }
    .online-url { min-width: 0; flex: 1; overflow: hidden; color: #334155; font-size: 14px; text-overflow: ellipsis; white-space: nowrap; }
    .online-copy { display: inline-flex; align-items: center; gap: 7px; flex: 0 0 auto; padding: 8px 12px; border: 0; border-radius: 10px; background: var(--admin-primary-color); color: #fff; font-weight: 700; }
    .online-mail-form { display: grid; gap: 10px; margin-top: 12px; padding: 14px; border: 1px solid #e2e8f0; border-radius: 17px; background: #fff; }
    .online-mail-heading { display: flex; align-items: center; gap: 11px; }
    .online-mail-heading i { display: grid; width: 34px; height: 34px; place-items: center; flex: 0 0 auto; border-radius: 11px; background: #fef2f2; color: var(--admin-primary-color); font-size: 18px; }
    .online-mail-heading strong, .online-mail-heading span { display: block; }
    .online-mail-heading strong { color: #1e293b; font-size: 14px; }
    .online-mail-heading span { margin-top: 2px; color: #64748b; font-size: 12px; }
    .online-mail-row { display: grid; grid-template-columns: minmax(0, 1fr) auto; gap: 10px; }
    .online-mail-input { min-width: 0; min-height: 42px; padding: 0 12px; border: 1px solid #dbe4ef; border-radius: 12px; color: #1e293b; font: inherit; outline: none; }
    .online-mail-input:focus { border-color: var(--admin-primary-color); box-shadow: 0 0 0 3px color-mix(in srgb, var(--admin-primary-color) 10%, transparent); }
    .online-mail-send { display: inline-flex; align-items: center; justify-content: center; gap: 8px; min-height: 42px; padding: 0 14px; border: 0; border-radius: 12px; background: var(--admin-primary-color); color: #fff; font-weight: 800; }
    .online-mail-config { margin: 0; color: #64748b; font-size: 12px; }
    .online-mail-config a { color: var(--admin-primary-color); font-weight: 700; }
    .online-side { padding: 22px; text-align: center; }
    .online-qr { width: 220px; max-width: 100%; margin: 2px auto 17px; padding: 14px; border: 1px solid #e2e8f0; border-radius: 20px; background: #fff; }
    .online-qr svg { display: block; width: 100%; height: auto; }
    .online-side h4 { margin: 0; font-size: 18px; font-weight: 800; }
    .online-side p { margin: 7px auto 17px; color: #64748b; font-size: 13px; line-height: 1.5; }
    .online-open { display: inline-flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 9px 13px; border: 1px solid #dbe4ef; border-radius: 12px; color: #1e293b; font-weight: 700; text-decoration: none; }
    .online-disabled { display: grid; gap: 12px; padding: 22px; border: 1px solid #fee2e2; border-radius: 22px; background: #fff7f7; color: #7f1d1d; }
    .online-disabled h3 { margin: 0; color: #991b1b; font-size: 18px; font-weight: 800; }
    .online-disabled p { margin: 0; color: #7f1d1d; line-height: 1.55; }
    @media (max-width: 991.98px) { .online-share { grid-template-columns: 1fr; } }
    @media (max-width: 575.98px) { .online-main { padding: 18px; } .online-mail-row { grid-template-columns: 1fr; } .online-copy span { display: none; } }
</style>
@endpush

@section('content')
@if ($lobbyModeEnabled)
    <section class="online-disabled">
        <h3><i class="bi bi-door-open"></i> Chế độ kiosk tại sảnh đang bật</h3>
        <p>Gửi Gmail/link đăng ký và mã QR đăng ký online đã được ẩn. Khách nhập thông tin trực tiếp trên màn hình kiosk đặt tại sảnh, sau đó lễ tân kiểm tra và bấm “Duyệt & cho khách vào”.</p>
        <a class="online-open" href="{{ route('admin.settings.kiosk') }}"><i class="bi bi-gear"></i> Mở cài đặt kiosk</a>
    </section>
@else
<div class="online-share">
    <section class="online-card online-main">
        <span class="online-kicker"><i class="bi bi-broadcast-pin"></i> Link đăng ký công khai</span>

        <div class="online-url-wrap">
            <i class="bi bi-link-45deg"></i>
            <span class="online-url">{{ $registrationUrl }}</span>
            <button class="online-copy" type="button" data-copy-registration><i class="bi bi-copy"></i><span>Sao chép</span></button>
        </div>

        <form class="online-mail-form" method="post" action="{{ route('admin.online-registration.send-email') }}" data-disable-on-submit>
            @csrf
            <div class="online-mail-heading">
                <i class="bi bi-envelope-fill"></i>
                <span><strong>Gửi qua Gmail</strong><span>Gửi bằng tài khoản Gmail/SMTP đã cấu hình trong hệ thống</span></span>
            </div>
            <div class="online-mail-row">
                <input class="online-mail-input" name="recipient_email" type="email" value="{{ old('recipient_email') }}" maxlength="190" required autocomplete="email" placeholder="Nhập Gmail/email của khách cần gửi">
                <button class="online-mail-send" type="submit" data-loading-text="Đang gửi..."><i class="bi bi-send-fill"></i><span>Gửi link</span></button>
            </div>
            @if ($mailConfigured)
                <p class="online-mail-config"><i class="bi bi-check-circle-fill text-success"></i> Đang gửi từ {{ $mailFromAddress }}.</p>
            @else
                <p class="online-mail-config text-danger"><i class="bi bi-exclamation-triangle-fill"></i> Chưa cấu hình Gmail/SMTP. <a href="{{ route('admin.settings.mail') }}">Cấu hình Gmail ngay</a>.</p>
            @endif
            @error('recipient_email')<p class="online-mail-config text-danger">{{ $message }}</p>@enderror
        </form>
    </section>

    <aside class="online-card online-side">
        <div class="online-qr">{!! \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(220)->margin(1)->errorCorrection('M')->generate($registrationUrl) !!}</div>
        <h4>Mã QR đăng ký</h4>
        <p>Khách quét QR để mở form đăng ký trực tiếp trên điện thoại.</p>
        <a class="online-open" href="{{ $registrationUrl }}" target="_blank" rel="noopener"><i class="bi bi-box-arrow-up-right"></i> Mở thử trang đăng ký</a>
    </aside>
</div>
@endif
@endsection

@push('scripts')
<script>
(() => {
    const url = @json($registrationUrl);
    const copyButton = document.querySelector('[data-copy-registration]');

    copyButton?.addEventListener('click', async () => {
        try {
            await navigator.clipboard.writeText(url);
        } catch (error) {
            const input = document.createElement('textarea');
            input.value = url;
            input.style.position = 'fixed';
            input.style.opacity = '0';
            document.body.appendChild(input);
            input.select();
            document.execCommand('copy');
            input.remove();
        }
        copyButton.innerHTML = '<i class="bi bi-check-lg"></i><span>Đã sao chép</span>';
        window.setTimeout(() => copyButton.innerHTML = '<i class="bi bi-copy"></i><span>Sao chép</span>', 1800);
    });
})();
</script>
@endpush
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Trạng thái yêu cầu | VMS Kiosk</title>
    @php
        $headSettings = $kioskSettings ?? [];
        $headFaviconUrl = $headSettings['app.favicon_url'] ?? $headSettings['kiosk.customer_logo_url'] ?? $headSettings['kiosk.logo_url'] ?? $headSettings['admin.logo_url'] ?? null;
    @endphp
    @if ($headFaviconUrl)
        <link rel="icon" href="{{ $headFaviconUrl }}">
        <link rel="shortcut icon" href="{{ $headFaviconUrl }}">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --ink: #0b1f3a;
            --muted: #657895;
            --line: #d8e6f5;
            --blue: var(--kiosk-primary, #146bd7);
            --cyan: #0cb4d8;
            --bg: #f4f8fd;
            --green: #22c55e;
        }

        * { box-sizing: border-box; }

        body {
            min-height: 100vh;
            margin: 0;
            color: var(--ink);
            background:
                radial-gradient(circle at 12% -6%, rgba(12, 180, 216, .16), transparent 34%),
                radial-gradient(circle at 100% 8%, rgba(20, 107, 215, .1), transparent 30%),
                linear-gradient(180deg, #fff 0%, var(--bg) 100%);
            font-family: "Manrope", sans-serif;
            overflow: hidden;
        }

        .ks-shell {
            width: min(1420px, calc(100vw - 88px));
            height: 100vh;
            min-height: 0;
            margin: 0 auto;
            padding: .85rem 0;
            display: grid;
            grid-template-rows: auto minmax(0, 1fr) auto;
            gap: .75rem;
        }

        .ks-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .ks-brand,
        .ks-tools,
        .ks-help {
            display: flex;
            align-items: center;
        }

        .ks-brand { gap: .9rem; }

        .ks-logo-group {
            display: inline-flex;
            align-items: center;
            gap: .55rem;
        }

        .ks-logo {
            width: auto;
            min-width: 54px;
            max-width: 118px;
            height: 46px;
            display: grid;
            place-items: center;
            border-radius: 14px;
            color: #fff;
            background: linear-gradient(135deg, var(--blue), var(--cyan));
            box-shadow: 0 16px 34px rgba(20, 107, 215, .18);
            overflow: hidden;
        }

        .ks-logo.has-logo {
            min-width: 0;
            max-width: 128px;
            background: transparent;
            border: 0;
            box-shadow: none;
            overflow: visible;
        }

        .ks-logo img {
            width: auto;
            max-width: 128px;
            height: 42px;
            max-height: 42px;
            object-fit: contain;
            padding: 0;
        }

        .ks-logo-separator {
            width: 1px;
            height: 34px;
            background: var(--line);
        }

        .ks-brand strong {
            display: block;
            font-family: "Plus Jakarta Sans", sans-serif;
            font-size: 1.28rem;
            font-weight: 700;
            letter-spacing: -.04em;
            text-transform: uppercase;
        }

        .ks-brand span {
            color: var(--muted);
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .045em;
            text-transform: uppercase;
        }

        .ks-tools { gap: 1.05rem; }

        .ks-select {
            height: 42px;
            min-width: 124px;
            padding: 0 .85rem;
            border: 1px solid var(--line);
            border-radius: 13px;
            background: #fff;
            color: var(--ink);
            font-weight: 700;
            font-family: inherit;
        }

        .ks-clock { min-width: 92px; text-align: right; }
        .ks-clock strong { display: block; font-family: "Plus Jakarta Sans", sans-serif; font-size: 1.38rem; font-weight: 700; letter-spacing: -.05em; }
        .ks-clock span { color: var(--muted); font-size: .74rem; }

        .ks-help {
            gap: .45rem;
            padding-left: 1rem;
            border-left: 1px solid var(--line);
            color: #526b87;
            font-size: .78rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .ks-help i { color: var(--blue); font-size: 1.35rem; }
        .ks-help strong { display: block; color: var(--ink); font-size: .9rem; line-height: 1.1; }
        .ks-help small { display: block; color: var(--muted); font-size: .68rem; line-height: 1.1; }

        .ks-main {
            display: grid;
            grid-template-columns: minmax(0, 900px) minmax(340px, 390px);
            gap: 1rem;
            align-items: stretch;
            justify-content: center;
            min-height: 0;
        }

        .ks-card {
            border: 1px solid var(--line);
            border-radius: 18px;
            background: rgba(255, 255, 255, .96);
            box-shadow: 0 16px 40px rgba(17, 39, 68, .06);
        }

        .ks-success {
            min-height: 0;
            height: 100%;
            padding: clamp(1rem, 1.55vw, 1.45rem);
            display: grid;
            align-content: center;
            gap: .72rem;
            text-align: center;
            overflow: hidden;
        }

        .ks-mark-wrap {
            position: relative;
            height: 48px;
            display: grid;
            place-items: center;
        }

        .ks-mark {
            width: 42px;
            height: 42px;
            display: grid;
            place-items: center;
            border-radius: 999px;
            background: linear-gradient(135deg, #35d074, #20c767);
            color: #fff;
            font-size: 1.4rem;
            box-shadow: 0 10px 22px rgba(34, 197, 94, .14);
        }

        .ks-confetti {
            position: absolute;
            inset: 0;
            pointer-events: none;
        }

        .ks-confetti i {
            position: absolute;
            color: var(--blue);
            font-size: .8rem;
        }

        .ks-confetti i:nth-child(1) { left: 36%; top: 18%; }
        .ks-confetti i:nth-child(2) { left: 43%; top: 4%; color: #f59e0b; font-size: .55rem; }
        .ks-confetti i:nth-child(3) { right: 38%; top: 12%; color: #0f9f9a; font-size: .6rem; }
        .ks-confetti i:nth-child(4) { right: 33%; top: 30%; color: #f59e0b; }
        .ks-confetti i:nth-child(5) { left: 42%; bottom: 20%; color: #e86d4d; }

        .ks-success h1 {
            margin: 0;
            color: var(--ink);
            font-family: "Plus Jakarta Sans", sans-serif;
            font-size: clamp(1.65rem, 2.18vw, 2rem);
            font-weight: 600;
            letter-spacing: 0;
        }

        .ks-lead {
            margin: -.35rem auto 0;
            max-width: 500px;
            color: #526b87;
            font-size: 1rem;
            line-height: 1.45;
        }

        .ks-code-box {
            width: min(600px, 100%);
            margin: 0 auto;
            padding: .8rem .9rem;
            border: 1px solid var(--line);
            border-radius: 16px;
            background: linear-gradient(135deg, #f8fbff, #eef7ff);
        }

        .ks-code-content {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 124px;
            align-items: center;
            gap: .8rem;
            text-align: left;
        }

        .ks-code-content.is-plain {
            grid-template-columns: 1fr;
            text-align: center;
        }

        .ks-code-box span {
            display: block;
            margin-bottom: .28rem;
            color: #526b87;
            font-size: .78rem;
            font-weight: 600;
        }

        .ks-code-box strong {
            display: block;
            color: #0b6fe8;
            font-family: "Plus Jakarta Sans", sans-serif;
            font-size: clamp(3rem, 3.9vw, 3.45rem);
            font-weight: 700;
            letter-spacing: 0;
            overflow-wrap: anywhere;
        }

        .ks-code-box p {
            margin: .45rem 0 0;
            color: #526b87;
            font-size: 1rem;
            line-height: 1.35;
        }

        .ks-guest-qr {
            display: grid;
            place-items: center;
            padding: .45rem;
            border: 1px solid #d8e5f2;
            border-radius: 14px;
            background: #fff;
        }

        .ks-guest-qr svg {
            width: 106px;
            height: 106px;
            display: block;
        }

        .ks-info-strip {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: .65rem;
            padding: .65rem;
            border: 1px solid var(--line);
            border-radius: 16px;
            background: #fff;
        }

        .ks-info-item {
            display: grid;
            gap: .22rem;
            justify-items: center;
            min-width: 0;
        }

        .ks-info-item i {
            width: 28px;
            height: 28px;
            display: grid;
            place-items: center;
            border-radius: 999px;
            background: #eff6ff;
            color: var(--blue);
        }

        .ks-info-item span {
            color: #526b87;
            font-size: .73rem;
            font-weight: 600;
        }

        .ks-info-item strong {
            max-width: 100%;
            color: var(--ink);
            font-size: .82rem;
            font-weight: 600;
            overflow-wrap: anywhere;
        }

        .ks-note {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .7rem;
            padding: .68rem;
            border-radius: 14px;
            background: #f1f7ff;
            color: #4d6685;
            font-size: .82rem;
            line-height: 1.35;
        }

        .ks-note i { color: var(--blue); }

        .ks-actions {
            display: grid;
            grid-template-columns: minmax(180px, 1fr);
            gap: .7rem;
        }

        .ks-btn {
            min-height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            border: 1px solid #bfdbfe;
            border-radius: 14px;
            background: #fff;
            color: var(--blue);
            font-family: inherit;
            font-size: .86rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
        }

        .ks-btn-primary {
            border: 0;
            color: #fff;
            background: linear-gradient(135deg, var(--blue), var(--cyan));
            box-shadow: 0 16px 32px rgba(20, 107, 215, .18);
        }

        .ks-side {
            display: grid;
            gap: .75rem;
            min-height: 0;
        }

        .ks-side-card {
            padding: .9rem;
        }

        .ks-side-title {
            margin-bottom: .65rem;
            text-align: center;
        }

        .ks-side-title h2,
        .ks-last h3 {
            margin: 0;
            color: var(--ink);
            font-family: "Plus Jakarta Sans", sans-serif;
            font-weight: 700;
            letter-spacing: 0;
        }

        .ks-side-title h2 { font-size: 1.12rem; }
        .ks-side-title p { margin: .25rem 0 0; color: var(--muted); font-size: .8rem; line-height: 1.5; }

        .ks-qr-box {
            width: min(250px, 100%);
            height: 92px;
            margin: 0 auto;
            display: grid;
            place-items: center;
            border: 1px dashed #98c4f4;
            border-radius: 16px;
            background:
                linear-gradient(90deg, rgba(20, 107, 215, .055) 1px, transparent 1px),
                linear-gradient(rgba(20, 107, 215, .055) 1px, transparent 1px),
                #f2f8ff;
            background-size: 24px 24px;
            color: var(--blue);
        }

        .ks-qr-box div {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: .2rem;
        }

        .ks-qr-box i { font-size: 2.15rem; }
        .ks-qr-box span { color: #526b87; font-size: .68rem; font-weight: 650; }

        .ks-divider {
            display: flex;
            align-items: center;
            gap: .62rem;
            margin: .48rem 0 .45rem;
            color: #6e86a3;
            font-size: .68rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .ks-divider::before,
        .ks-divider::after {
            content: "";
            flex: 1;
            height: 1px;
            background: var(--line);
        }

        .ks-input-wrap {
            position: relative;
            margin-bottom: .55rem;
        }

        .ks-input-wrap i {
            position: absolute;
            left: .9rem;
            top: 50%;
            transform: translateY(-50%);
            color: #7088a4;
        }

        .ks-input {
            width: 100%;
            min-height: 40px;
            padding: .6rem .8rem .6rem 2.35rem;
            border: 1px solid var(--line);
            border-radius: 13px;
            color: var(--ink);
            font-family: inherit;
            font-size: .9rem;
        }

        .ks-last h3 {
            display: flex;
            align-items: center;
            gap: .45rem;
            margin-bottom: .48rem;
            font-size: .88rem;
        }

        .ks-last h3 i {
            width: 28px;
            height: 28px;
            display: grid;
            place-items: center;
            border-radius: 10px;
            color: var(--blue);
            background: #eaf4ff;
        }

        .ks-last-lines {
            display: grid;
            gap: .28rem;
        }

        .ks-last-lines div {
            display: flex;
            justify-content: space-between;
            gap: .8rem;
            padding: .36rem .55rem;
            border: 1px solid var(--line);
            border-radius: 12px;
            font-size: .78rem;
        }

        .ks-last-lines span { color: var(--muted); }
        .ks-last-lines strong { color: var(--ink); text-align: right; overflow-wrap: anywhere; font-weight: 600; }

        .ks-footer {
            text-align: center;
            color: #526b87;
            font-size: .76rem;
        }

        .ks-footer i { color: var(--blue); }

        @media print {
            .ks-header, .ks-side, .ks-footer, .ks-actions { display: none; }
            .ks-shell { width: 100%; min-height: auto; padding: 0; }
            .ks-main { display: block; }
            .ks-success { box-shadow: none; border: 0; min-height: auto; }
        }

        @media (max-width: 1180px) {
            body { overflow: auto; }
            .ks-shell { width: min(980px, calc(100vw - 24px)); height: auto; min-height: 100vh; padding: 1rem 0; }
            .ks-main { grid-template-columns: 1fr; }
            .ks-success { height: auto; overflow: visible; }
            .ks-side { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width: 760px) {
            .ks-shell { width: min(100%, calc(100vw - 18px)); }
            .ks-header, .ks-tools { align-items: flex-start; flex-direction: column; }
            .ks-main, .ks-side, .ks-info-strip, .ks-actions { grid-template-columns: 1fr; }
            .ks-success { min-height: 0; padding: 1.35rem; }
            .ks-code-box strong { font-size: 2.35rem; }
            .ks-code-content { grid-template-columns: 1fr; text-align: center; }
            .ks-guest-qr { justify-self: center; }
        }
    </style>
</head>
@php
    $formatDisplayName = static fn (?string $value, string $fallback): string => trim(preg_replace('/(?<=[a-z])(?=[A-Z])/', ' ', $value ?: $fallback));
    $settings = $kioskSettings ?? [];
    $systemName = $settings['kiosk.system_name'] ?? 'VMS Kiosk';
    $subtitle = $settings['kiosk.subtitle'] ?? 'Giao diện tự động cho khách đến công ty';
    $hotline = $settings['kiosk.hotline'] ?? '1900 0000';
    $ownerLogoUrl = $settings['kiosk.owner_logo_url'] ?? ($settings['admin.logo_url'] ?? null);
    $customerLogoUrl = $settings['kiosk.customer_logo_url'] ?? ($settings['kiosk.logo_url'] ?? null);
    $primaryColor = $settings['kiosk.primary_color'] ?? '#146bd7';
    $primaryColor = preg_match('/^#[0-9a-fA-F]{6}$/', (string) $primaryColor) ? $primaryColor : '#146bd7';

    $statusLabels = [
        'pending' => 'Đang chờ phê duyệt',
        'approved' => 'Đã được duyệt',
        'checked_in' => 'Đã check-in',
        'checked_out' => 'Đã rời công ty',
        'rejected' => 'Bị từ chối',
        'cancelled' => 'Đã hủy',
    ];
    $isJustSubmitted = $visit->status === 'pending';
    $title = $isJustSubmitted ? 'Gửi yêu cầu thành công!' : ($statusLabels[$visit->status] ?? 'Trạng thái yêu cầu');
    $lead = $isJustSubmitted
        ? 'Cảm ơn bạn đã cung cấp thông tin. Yêu cầu đã được gửi đến lễ tân và đang chờ duyệt.'
        : 'Vui lòng lưu lại mã lịch hẹn để tra cứu trạng thái khi cần.';
    $canShowQr = ! $isJustSubmitted && in_array($visit->status, ['approved', 'checked_in', 'checked_out'], true);
@endphp
<body style="--kiosk-primary: {{ $primaryColor }};">
    <main class="ks-shell">
        <header class="ks-header">
            <div class="ks-brand">
                <div class="ks-logo-group">
                    @if ($ownerLogoUrl)
                        <div class="ks-logo has-logo">
                            <img src="{{ $ownerLogoUrl }}" alt="{{ $systemName }}">
                        </div>
                    @endif
                    @if ($ownerLogoUrl && $customerLogoUrl)
                        <span class="ks-logo-separator" aria-hidden="true"></span>
                    @endif
                    @if ($customerLogoUrl)
                        <div class="ks-logo has-logo">
                            <img src="{{ $customerLogoUrl }}" alt="Logo khách hàng">
                        </div>
                    @elseif (! $ownerLogoUrl)
                        <div class="ks-logo">
                            <i class="bi bi-shield-check"></i>
                        </div>
                    @endif
                </div>
                <div>
                    <strong>{{ $systemName }}</strong>
                    <span>{{ $subtitle }}</span>
                </div>
            </div>

            <div class="ks-tools">
                <select class="ks-select" aria-label="Ngôn ngữ"><option>Tiếng Việt</option></select>
                <div class="ks-clock">
                    <strong id="ksClock">--:--</strong>
                    <span id="ksDate">--/--/----</span>
                </div>
                <div class="ks-help">
                    <i class="bi bi-telephone"></i>
                    <div><small>Hỗ trợ</small><strong>{{ $hotline }}</strong></div>
                </div>
            </div>
        </header>

        <section class="ks-main">
            <section class="ks-card ks-success">
                <div class="ks-mark-wrap">
                    <div class="ks-confetti" aria-hidden="true">
                        <i class="bi bi-record-circle"></i>
                        <i class="bi bi-diamond-fill"></i>
                        <i class="bi bi-diamond-fill"></i>
                        <i class="bi bi-stars"></i>
                        <i class="bi bi-activity"></i>
                    </div>
                    <div class="ks-mark"><i class="bi bi-check-lg"></i></div>
                </div>

                <div>
                    <h1>{{ $title }}</h1>
                    <p class="ks-lead">{{ $lead }}</p>
                </div>

                <div class="ks-code-box">
                    <div class="ks-code-content {{ $canShowQr && $visit->qr_token ? '' : 'is-plain' }}">
                        <div>
                            <span>Mã lịch hẹn của bạn</span>
                            <strong>{{ $visit->code }}</strong>
                            @if($isJustSubmitted)
                                <p>Sau khi lễ tân duyệt, mã QR/check-in sẽ được gửi qua Gmail của bạn. Vui lòng kiểm tra hộp thư trong vài phút tới.</p>
                            @else
                                <p>Vui lòng lưu lại mã này để tra cứu trạng thái khi cần.</p>
                            @endif
                            @if ($canShowQr && $visit->qr_token)
                                <p>Mã QR/check-in: {{ $visit->qr_token }}</p>
                            @endif
                        </div>
                        @if ($canShowQr && $visit->qr_token)
                            <div class="ks-guest-qr" aria-label="Mã QR check-in">
                                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(106)->margin(1)->errorCorrection('M')->generate($visit->qr_token) !!}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="ks-info-strip">
                    <div class="ks-info-item">
                        <i class="bi bi-clock"></i>
                        <span>Dự kiến tới</span>
                        <strong>{{ $visit->scheduled_at?->format('H:i - d/m/Y') ?? '-' }}</strong>
                    </div>
                    <div class="ks-info-item">
                        <i class="bi bi-person-circle"></i>
                        <span>Người tiếp</span>
                        <strong>{{ $visit->hostEmployee?->name ?? '-' }}</strong>
                    </div>
                    <div class="ks-info-item">
                        <i class="bi bi-building"></i>
                        <span>Phòng ban</span>
                        <strong>{{ $visit->hostEmployee?->department?->name ?? '-' }}</strong>
                    </div>
                </div>

                <div class="ks-note">
                    <i class="bi bi-bell-fill"></i>
                    <span>
                        @if($isJustSubmitted)
                            Lễ tân sẽ kiểm tra yêu cầu. Nếu lịch được duyệt, mã QR/check-in sẽ được gửi qua Gmail của bạn trong vài phút tới.
                        @else
                            Khi lễ tân xác nhận, bạn sẽ được hướng dẫn tiếp theo.<br>Vui lòng ngồi chờ tại khu vực tiếp khách.
                        @endif
                    </span>
                </div>

                <div class="ks-actions">
                    <a class="ks-btn" href="{{ route('kiosk.index') }}"><i class="bi bi-house-door"></i>Về trang chủ</a>
                    @if ($visit->status === 'approved' && $canConfirm)
                        <form method="post" action="{{ route('kiosk.checkin.confirm', $visit) }}">
                            @csrf
                            <button class="ks-btn ks-btn-primary" type="submit" style="width: 100%;">
                                <i class="bi bi-box-arrow-in-right"></i>Xác nhận check-in
                            </button>
                        </form>
                    @endif
                </div>
            </section>

            <aside class="ks-side">
                <section class="ks-card ks-side-card">
                    <div class="ks-side-title">
                        <h2>Tra cứu / Check-in</h2>
                        <p>Quét QR hoặc nhập mã lịch hẹn để check-in.</p>
                    </div>

                    <div class="ks-qr-box">
                        <div>
                            <i class="bi bi-qr-code"></i>
                            <span>Đưa mã QR vào khung</span>
                        </div>
                    </div>

                    <div class="ks-divider">Hoặc nhập mã lịch hẹn</div>

                    <form method="post" action="{{ route('kiosk.checkin.scan-qr') }}">
                        @csrf
                        <div class="ks-input-wrap">
                            <i class="bi bi-calendar2-check"></i>
                            <input class="ks-input" name="qr_token" placeholder="Nhập mã lịch hẹn hoặc mã QR">
                        </div>
                        <button class="ks-btn ks-btn-primary" type="submit" style="width: 100%;">
                            <i class="bi bi-search"></i>Kiểm tra mã
                        </button>
                    </form>
                </section>

                <section class="ks-card ks-side-card ks-last">
                    <h3><i class="bi bi-card-checklist"></i>Thông tin yêu cầu vừa gửi</h3>
                    <div class="ks-last-lines">
                        <div><span>Mã lịch hẹn</span><strong>{{ $visit->code }}</strong></div>
                        <div><span>Họ và tên</span><strong>{{ $visit->visitor?->full_name ?? '-' }}</strong></div>
                        <div><span>Số điện thoại</span><strong>{{ $visit->visitor?->phone ?? '-' }}</strong></div>
                        <div><span>Công ty / Tổ chức</span><strong>{{ $visit->visitor?->company ?? '-' }}</strong></div>
                        <div><span>Mục đích đến</span><strong>{{ $visit->purpose ?? '-' }}</strong></div>
                        <div><span>Thời gian gửi</span><strong>{{ $visit->created_at?->format('H:i - d/m/Y') ?? '-' }}</strong></div>
                    </div>
                    <a class="ks-btn" style="width: 100%; margin-top: .9rem;" href="{{ route('kiosk.checkin.status', $visit) }}">
                        Xem lịch sử yêu cầu <i class="bi bi-chevron-right"></i>
                    </a>
                </section>
            </aside>
        </section>

        <footer class="ks-footer">
            <i class="bi bi-info-circle"></i>
            Nếu cần hỗ trợ, vui lòng liên hệ lễ tân hoặc gọi {{ $hotline }}
        </footer>
    </main>

    <script>
        const clockNode = document.getElementById('ksClock');
        const dateNode = document.getElementById('ksDate');

        function updateClock() {
            const now = new Date();
            clockNode.textContent = new Intl.DateTimeFormat('vi-VN', { hour: '2-digit', minute: '2-digit' }).format(now);
            dateNode.textContent = new Intl.DateTimeFormat('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' }).format(now);
        }

        updateClock();
        setInterval(updateClock, 30000);
    </script>
</body>
</html>

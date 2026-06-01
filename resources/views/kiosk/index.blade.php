<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kiosk | Gatehouse Pro</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --kiosk-ink: #0b1f3a;
            --kiosk-muted: #637895;
            --kiosk-line: #d8e6f5;
            --kiosk-blue: var(--kiosk-primary, #146bd7);
            --kiosk-cyan: #0cb4d8;
            --kiosk-bg: #f4f8fd;
        }

        * { box-sizing: border-box; }

        body {
            min-height: 100vh;
            margin: 0;
            color: var(--kiosk-ink);
            background:
                radial-gradient(circle at 14% -6%, rgba(12, 180, 216, .16), transparent 34%),
                radial-gradient(circle at 100% 8%, rgba(20, 107, 215, .10), transparent 30%),
                linear-gradient(180deg, #ffffff 0%, var(--kiosk-bg) 100%);
            font-family: "Manrope", sans-serif;
        }

        .kiosk-shell {
            min-height: 100vh;
            display: grid;
            grid-template-rows: auto 1fr auto;
            gap: .85rem;
            width: min(1480px, calc(100vw - 28px));
            margin: 0 auto;
            padding: clamp(.7rem, 1vw, 1rem) 0;
        }

        .kiosk-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .kiosk-brand {
            display: flex;
            align-items: center;
            gap: .9rem;
        }

        .kiosk-logo {
            width: 46px;
            height: 46px;
            place-items: center;
            display: grid;
            border-radius: 15px;
            color: #fff;
            background: linear-gradient(135deg, var(--kiosk-blue), var(--kiosk-cyan));
            box-shadow: 0 16px 34px rgba(20, 107, 215, .18);
        }

        .kiosk-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: .45rem;
        }

        .kiosk-brand strong {
            display: block;
            font-family: "Plus Jakarta Sans", sans-serif;
            font-size: 1.28rem;
            font-weight: 800;
            letter-spacing: -.04em;
            text-transform: uppercase;
        }

        .kiosk-brand span {
            color: var(--kiosk-muted);
            font-size: .72rem;
            font-weight: 800;
            letter-spacing: .045em;
            text-transform: uppercase;
        }

        .kiosk-tools {
            display: flex;
            align-items: center;
            gap: 1.05rem;
        }

        .kiosk-tools .form-select {
            min-width: 124px;
            height: 42px;
            border-color: var(--kiosk-line);
            border-radius: 13px;
            color: var(--kiosk-ink);
            font-weight: 800;
        }

        .kiosk-clock {
            min-width: 92px;
            text-align: right;
        }

        .kiosk-clock strong {
            display: block;
            font-family: "Plus Jakarta Sans", sans-serif;
            font-size: 1.38rem;
            font-weight: 800;
            letter-spacing: -.05em;
        }

        .kiosk-clock span {
            color: var(--kiosk-muted);
            font-size: .74rem;
        }

        .kiosk-main {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 380px;
            gap: 1rem;
            align-items: start;
        }

        .kiosk-card {
            border: 1px solid var(--kiosk-line);
            border-radius: 24px;
            background: rgba(255, 255, 255, .96);
            box-shadow: 0 22px 58px rgba(17, 39, 68, .08);
        }

        .kiosk-form-card {
            min-height: 0;
            max-width: 1060px;
            padding: clamp(1.05rem, 1.35vw, 1.45rem);
        }

        .kiosk-title {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: .95rem;
        }

        .kiosk-title h1,
        .kiosk-side-title h2 {
            margin: 0;
            color: var(--kiosk-ink);
            font-family: "Plus Jakarta Sans", sans-serif;
            font-weight: 800;
            letter-spacing: -.05em;
        }

        .kiosk-title h1 {
            font-size: clamp(1.55rem, 1.9vw, 2rem);
        }

        .kiosk-title p,
        .kiosk-side-title p {
            margin: .25rem 0 0;
            color: var(--kiosk-muted);
            font-size: .88rem;
        }

        .kiosk-type-pill {
            margin-top: .15rem;
            padding: .42rem .75rem;
            border: 1px solid #bcd8f8;
            border-radius: 999px;
            color: var(--kiosk-blue);
            background: #eff6ff;
            font-size: .84rem;
            font-weight: 800;
        }

        .kiosk-flat-form {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .68rem 1rem;
        }

        .kiosk-section-title {
            grid-column: 1 / -1;
            display: flex;
            align-items: center;
            gap: .5rem;
            margin: .05rem 0 -.2rem;
            color: var(--kiosk-blue);
            font-size: .78rem;
            font-weight: 800;
            letter-spacing: .025em;
            text-transform: uppercase;
        }

        .kiosk-section-title i {
            width: 22px;
            height: 22px;
            display: grid;
            place-items: center;
            border-radius: 999px;
            background: #eaf4ff;
        }

        .kiosk-field-full,
        .kiosk-policy,
        .kiosk-submit {
            grid-column: 1 / -1;
        }

        .kiosk-flat-form .form-label {
            margin-bottom: .28rem;
            color: var(--kiosk-ink);
            font-size: .75rem;
            font-weight: 800;
        }

        .kiosk-input-wrap {
            position: relative;
        }

        .kiosk-input-wrap i {
            position: absolute;
            left: .9rem;
            top: 50%;
            transform: translateY(-50%);
            color: #7088a4;
            pointer-events: none;
        }

        .kiosk-input-wrap .form-control,
        .kiosk-input-wrap .form-select {
            padding-left: 2.55rem;
        }

        .kiosk-flat-form .form-control,
        .kiosk-flat-form .form-select,
        .kiosk-side-card .form-control {
            min-height: 44px;
            border-color: var(--kiosk-line);
            border-radius: 13px;
            color: var(--kiosk-ink);
            font-size: .9rem;
        }

        .kiosk-flat-form textarea.form-control {
            min-height: 62px;
        }

        .kiosk-flat-form .form-control:focus,
        .kiosk-flat-form .form-select:focus,
        .kiosk-side-card .form-control:focus {
            border-color: var(--kiosk-blue);
            box-shadow: 0 0 0 4px rgba(20, 107, 215, .10);
        }

        .kiosk-policy {
            display: flex;
            align-items: center;
            gap: .65rem;
            margin: 0;
            padding: .55rem .75rem;
            border: 1px solid var(--kiosk-line);
            border-radius: 15px;
            background: #f8fbff;
            color: #334963;
        }

        .kiosk-submit {
            min-height: 46px;
            border: 0;
            border-radius: 16px;
            color: #fff;
            background: linear-gradient(135deg, var(--kiosk-blue), var(--kiosk-cyan));
            font-weight: 800;
            box-shadow: 0 18px 36px rgba(20, 107, 215, .18);
        }

        .premium-result-list {
            position: absolute;
            z-index: 20;
            width: min(520px, 100%);
            box-shadow: 0 18px 36px rgba(17, 39, 68, .14);
        }

        .kiosk-side {
            display: grid;
            gap: .85rem;
        }

        .kiosk-side-card {
            padding: 1rem;
        }

        .kiosk-side-title {
            margin-bottom: .75rem;
            text-align: center;
        }

        .kiosk-side-title h2 {
            font-size: 1.15rem;
        }

        .kiosk-side-title p {
            font-size: .78rem;
            line-height: 1.5;
        }

        .kiosk-qr-box {
            position: relative;
            min-height: 128px;
            display: grid;
            place-items: center;
            border: 1px dashed #98c4f4;
            border-radius: 16px;
            background:
                linear-gradient(90deg, rgba(20, 107, 215, .055) 1px, transparent 1px),
                linear-gradient(rgba(20, 107, 215, .055) 1px, transparent 1px),
                #f2f8ff;
            background-size: 24px 24px;
            color: var(--kiosk-blue);
        }

        .kiosk-qr-box i {
            font-size: 3.2rem;
            filter: drop-shadow(0 10px 18px rgba(20, 107, 215, .12));
        }

        .kiosk-qr-box span {
            position: absolute;
            bottom: .72rem;
            color: #526b87;
            font-size: .72rem;
            font-weight: 800;
        }

        .kiosk-side-divider {
            display: flex;
            align-items: center;
            gap: .62rem;
            margin: .75rem 0 .62rem;
            color: #6e86a3;
            font-size: .68rem;
            font-weight: 900;
            text-transform: uppercase;
        }

        .kiosk-side-divider::before,
        .kiosk-side-divider::after {
            content: "";
            flex: 1;
            height: 1px;
            background: var(--kiosk-line);
        }

        .kiosk-last-card {
            padding: .95rem;
        }

        .kiosk-last-card h3 {
            display: flex;
            align-items: center;
            gap: .45rem;
            margin: 0 0 .7rem;
            color: var(--kiosk-ink);
            font-size: .92rem;
            font-weight: 900;
        }

        .kiosk-last-card h3 i {
            width: 28px;
            height: 28px;
            display: grid;
            place-items: center;
            border-radius: 10px;
            color: var(--kiosk-blue);
            background: #eaf4ff;
        }

        .kiosk-last-status {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .68rem;
            border-radius: 16px;
            background: #eff6ff;
        }

        .kiosk-last-status i {
            width: 42px;
            height: 42px;
            display: grid;
            place-items: center;
            border-radius: 16px;
            background: #fef3c7;
            color: #d97706;
            font-size: 1.25rem;
        }

        .kiosk-last-status strong,
        .kiosk-last-status span {
            display: block;
        }

        .kiosk-last-status strong {
            font-weight: 900;
        }

        .kiosk-last-status span {
            color: var(--kiosk-muted);
            font-size: .84rem;
        }

        .kiosk-last-lines {
            display: grid;
            gap: .45rem;
            margin-top: .65rem;
        }

        .kiosk-last-lines div {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: .55rem .7rem;
            border: 1px solid var(--kiosk-line);
            border-radius: 14px;
        }

        .kiosk-last-lines span {
            color: var(--kiosk-muted);
        }

        .kiosk-last-lines strong {
            color: var(--kiosk-ink);
            text-align: right;
        }

        .kiosk-footer {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1rem;
            padding: .72rem 1rem;
            border: 1px solid var(--kiosk-line);
            border-radius: 18px;
            background: rgba(255,255,255,.72);
            box-shadow: 0 18px 42px rgba(17, 39, 68, .05);
        }

        .kiosk-footer div {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .55rem;
            color: var(--kiosk-muted);
        }

        .kiosk-footer i {
            color: var(--kiosk-blue);
            font-size: 1.05rem;
        }

        .kiosk-footer strong {
            display: block;
            color: #526b87;
            font-size: .72rem;
        }

        .kiosk-footer span {
            color: var(--kiosk-ink);
            font-size: .85rem;
            font-weight: 700;
        }

        .kiosk-notice-layer {
            position: fixed;
            z-index: 50;
            top: 1rem;
            left: 50%;
            width: min(640px, calc(100vw - 2rem));
            transform: translateX(-50%);
        }

        .kiosk-notice {
            display: flex;
            gap: .8rem;
            align-items: flex-start;
            padding: 1rem;
            border-radius: 18px;
            background: #fff;
            box-shadow: 0 22px 60px rgba(11,31,58,.18);
        }

        .kiosk-notice-success { border: 1px solid #a7f3d0; color: #047857; }
        .kiosk-notice-danger { border: 1px solid #fecdd3; color: #be123c; }
        .kiosk-notice p { margin: .15rem 0 0; color: #334963; }
        .kiosk-notice-close { margin-left: auto; border: 0; background: transparent; color: inherit; }
        .kiosk-notice.is-hiding { opacity: 0; transform: translateY(-8px); transition: .25s ease; }

        .kiosk-modal-backdrop {
            position: fixed;
            inset: 0;
            z-index: 80;
            display: none;
            place-items: center;
            padding: 1.25rem;
            background: rgba(6, 22, 42, .46);
            backdrop-filter: blur(10px);
        }

        .kiosk-modal-backdrop.is-open { display: grid; }
        .kiosk-modal { width: min(560px, 100%); border: 1px solid #dbe8f6; border-radius: 28px; background: #fff; box-shadow: 0 28px 80px rgba(8,28,52,.24); overflow: hidden; }
        .kiosk-modal-head { display: flex; justify-content: space-between; gap: 1rem; padding: 1.25rem 1.35rem; background: linear-gradient(135deg, #eff6ff, #f8fdff); border-bottom: 1px solid #e4edf8; }
        .kiosk-modal-title { display: flex; gap: .85rem; align-items: center; }
        .kiosk-modal-mark { width: 54px; height: 54px; display: grid; place-items: center; border-radius: 18px; color: #0f6bdc; background: #dbeafe; font-size: 1.45rem; }
        .kiosk-modal-title h3 { margin: 0; color: var(--kiosk-ink); font-family: "Plus Jakarta Sans", sans-serif; font-size: 1.35rem; font-weight: 800; }
        .kiosk-modal-title p { margin: .25rem 0 0; color: var(--kiosk-muted); }
        .kiosk-modal-close { width: 38px; height: 38px; border: 1px solid #dbe8f6; border-radius: 14px; background: #fff; color: #526b87; }
        .kiosk-modal-body { padding: 1.25rem 1.35rem 1.35rem; }
        .kiosk-modal-status { display: inline-flex; align-items: center; gap: .45rem; margin-bottom: 1rem; padding: .5rem .75rem; border-radius: 999px; color: #b45309; background: #fff3cd; font-weight: 800; }
        .kiosk-modal-status.is-approved,.kiosk-modal-status.is-checked_in { color: #047857; background: #dcfce7; }
        .kiosk-modal-status.is-rejected,.kiosk-modal-status.is-cancelled { color: #be123c; background: #ffe4e6; }
        .kiosk-modal-grid { display: grid; gap: .65rem; }
        .kiosk-modal-row { display: flex; justify-content: space-between; gap: 1rem; padding: .72rem .85rem; border: 1px solid #dbe8f6; border-radius: 15px; background: #fbfdff; color: var(--kiosk-muted); }
        .kiosk-modal-row strong { color: var(--kiosk-ink); text-align: right; }
        .kiosk-modal-qr { display: none; place-items: center; margin-bottom: 1rem; padding: 1rem; border: 1px solid #dbe8f6; border-radius: 20px; background: #f8fbff; }
        .kiosk-modal-qr.has-qr { display: grid; }
        .kiosk-modal-qr svg { width: 150px; height: 150px; }
        .kiosk-modal-actions { display: grid; grid-template-columns: repeat(2, 1fr); gap: .75rem; margin-top: 1rem; }
        .kiosk-modal-actions .btn { min-height: 46px; border-radius: 15px; font-weight: 800; }

        @media (max-width: 1180px) {
            .kiosk-main { grid-template-columns: 1fr; }
            .kiosk-side { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width: 760px) {
            .kiosk-header, .kiosk-tools { align-items: flex-start; flex-direction: column; }
            .kiosk-flat-form, .kiosk-side, .kiosk-footer { grid-template-columns: 1fr; }
        }

        @media (max-width: 1700px) {
            .kiosk-shell {
                width: min(1360px, calc(100vw - 22px));
                gap: .65rem;
                padding-top: .65rem;
                padding-bottom: .65rem;
            }

            .kiosk-main {
                grid-template-columns: minmax(0, 1fr) 330px;
                gap: .8rem;
            }

            .kiosk-form-card {
                max-width: none;
                padding: .95rem 1rem;
                border-radius: 21px;
            }

            .kiosk-side-card,
            .kiosk-last-card {
                padding: .82rem;
                border-radius: 20px;
            }

            .kiosk-title {
                margin-bottom: .65rem;
            }

            .kiosk-title h1 {
                font-size: clamp(1.35rem, 1.55vw, 1.7rem);
                letter-spacing: -.055em;
            }

            .kiosk-title p,
            .kiosk-side-title p {
                font-size: .78rem;
            }

            .kiosk-flat-form {
                gap: .52rem .78rem;
            }

            .kiosk-section-title {
                margin-top: 0;
                font-size: .72rem;
            }

            .kiosk-section-title i {
                width: 20px;
                height: 20px;
            }

            .kiosk-flat-form .form-label {
                margin-bottom: .22rem;
                font-size: .68rem;
            }

            .kiosk-flat-form .form-control,
            .kiosk-flat-form .form-select,
            .kiosk-side-card .form-control {
                min-height: 39px;
                border-radius: 11px;
                font-size: .82rem;
            }

            .kiosk-input-wrap .form-control,
            .kiosk-input-wrap .form-select {
                padding-left: 2.25rem;
            }

            .kiosk-input-wrap i {
                left: .78rem;
                font-size: .86rem;
            }

            .kiosk-flat-form textarea.form-control {
                min-height: 54px;
            }

            .kiosk-policy {
                padding: .42rem .62rem;
                border-radius: 12px;
                font-size: .78rem;
            }

            .kiosk-submit {
                min-height: 42px;
                border-radius: 13px;
                font-size: .88rem;
            }

            .kiosk-side-title {
                margin-bottom: .55rem;
            }

            .kiosk-side-title h2 {
                font-size: 1rem;
            }

            .kiosk-qr-box {
                min-height: 108px;
                border-radius: 14px;
                background-size: 20px 20px;
            }

            .kiosk-qr-box i {
                font-size: 2.55rem;
            }

            .kiosk-side-divider {
                margin: .56rem 0 .5rem;
                font-size: .62rem;
            }

            .kiosk-last-status {
                padding: .55rem;
            }

            .kiosk-last-status i {
                width: 36px;
                height: 36px;
                border-radius: 13px;
                font-size: 1rem;
            }

            .kiosk-last-lines {
                gap: .35rem;
                margin-top: .5rem;
            }

            .kiosk-last-lines div {
                padding: .45rem .58rem;
                border-radius: 12px;
                font-size: .8rem;
            }

            .kiosk-footer {
                padding: .55rem .8rem;
                border-radius: 15px;
            }
        }

        @media (max-width: 1400px) {
            .kiosk-shell {
                width: min(1200px, calc(100vw - 18px));
            }

            .kiosk-main {
                grid-template-columns: minmax(0, 1fr) 300px;
            }

            .kiosk-brand strong {
                font-size: 1.08rem;
            }

            .kiosk-brand span {
                font-size: .64rem;
            }

            .kiosk-logo {
                width: 40px;
                height: 40px;
                border-radius: 13px;
            }

            .kiosk-clock strong {
                font-size: 1.18rem;
            }
        }

        @media (max-width: 1100px) {
            .kiosk-main {
                grid-template-columns: 1fr;
            }

            .kiosk-side {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
    </style>
</head>
@php
    $formatDisplayName = static fn (?string $value, string $fallback): string => trim(preg_replace('/(?<=[a-z])(?=[A-Z])/', ' ', $value ?: $fallback));
    $settings = $kioskSettings ?? [];
    $companyName = $formatDisplayName($settings['kiosk.company_name'] ?? null, 'Công ty ABC');
    $systemName = $settings['kiosk.system_name'] ?? 'VMS Kiosk';
    $subtitle = $settings['kiosk.subtitle'] ?? 'Giao diện tự động cho khách đến công ty';
    $hotline = $settings['kiosk.hotline'] ?? '1900 0000';
    $workingHours = $settings['kiosk.working_hours'] ?? '07:30 - 18:00';
    $logoUrl = $settings['kiosk.logo_url'] ?? null;
    $primaryColor = $settings['kiosk.primary_color'] ?? '#146bd7';
    $primaryColor = preg_match('/^#[0-9a-fA-F]{6}$/', (string) $primaryColor) ? $primaryColor : '#146bd7';
    $lastVisit = $lastKioskVisit ?? null;
    $lastStatusLabels = [
        'pending' => 'Đang chờ phê duyệt',
        'approved' => 'Đã được duyệt',
        'checked_in' => 'Đã check-in',
        'checked_out' => 'Đã rời công ty',
        'rejected' => 'Bị từ chối',
        'cancelled' => 'Đã hủy',
    ];
@endphp
<body style="--kiosk-primary: {{ $primaryColor }};">
    <main class="kiosk-shell">
        @php
            $noticeType = session('error') || $errors->any() ? 'danger' : (session('status') ? 'success' : null);
            $noticeMessage = session('error') ?? session('status') ?? ($errors->any() ? $errors->first() : null);
        @endphp
        @if ($noticeMessage)
            <div class="kiosk-notice-layer" id="kioskNotice">
                <div class="kiosk-notice kiosk-notice-{{ $noticeType }}">
                    <i class="bi {{ $noticeType === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' }}"></i>
                    <div>
                        <strong>{{ $noticeType === 'success' ? 'Thao tác thành công' : 'Cần kiểm tra lại' }}</strong>
                        <p>{{ $noticeMessage }}</p>
                    </div>
                    <button type="button" class="kiosk-notice-close" onclick="document.getElementById('kioskNotice')?.remove()">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>
        @endif

        <header class="kiosk-header">
            <div class="kiosk-brand">
                <div class="kiosk-logo">
                    @if ($logoUrl)
                        <img src="{{ $logoUrl }}" alt="Logo">
                    @else
                        <i class="bi bi-shield-check"></i>
                    @endif
                </div>
                <div>
                    <strong>{{ $systemName }}</strong>
                    <span>{{ $subtitle }}</span>
                </div>
            </div>
            <div class="kiosk-tools">
                <select class="form-select" aria-label="Chọn ngôn ngữ">
                    <option>Tiếng Việt</option>
                    <option>English</option>
                </select>
                <div class="kiosk-clock">
                    <strong id="kioskClock">--:--</strong>
                    <span id="kioskDate">--</span>
                </div>
            </div>
        </header>

        <section class="kiosk-main">
            <section class="kiosk-card kiosk-form-card">
                <div class="kiosk-title">
                    <div>
                        <h1>Đăng ký khách</h1>
                        <p>Vui lòng nhập thông tin để được hỗ trợ nhanh chóng.</p>
                    </div>
                    <span class="kiosk-type-pill">Walk-in</span>
                </div>

                <form class="kiosk-flat-form" method="post" action="{{ route('kiosk.checkin.manual') }}">
                    @csrf
                    <div class="kiosk-section-title"><i class="bi bi-person-fill"></i>Thông tin khách</div>

                    <div>
                        <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                        <div class="kiosk-input-wrap">
                            <i class="bi bi-person"></i>
                            <input class="form-control" name="visitor_name" value="{{ old('visitor_name') }}" placeholder="Nhập họ và tên" required>
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                        <div class="kiosk-input-wrap">
                            <i class="bi bi-telephone"></i>
                            <input class="form-control" name="visitor_phone" value="{{ old('visitor_phone') }}" placeholder="Nhập số điện thoại" required>
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Email nếu có</label>
                        <div class="kiosk-input-wrap">
                            <i class="bi bi-envelope"></i>
                            <input class="form-control" type="email" name="visitor_email" value="{{ old('visitor_email') }}" placeholder="example@email.com">
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Công ty / Tổ chức</label>
                        <div class="kiosk-input-wrap">
                            <i class="bi bi-building"></i>
                            <input class="form-control" name="visitor_company" value="{{ old('visitor_company') }}" placeholder="Nhập tên công ty">
                        </div>
                    </div>

                    <div class="kiosk-section-title"><i class="bi bi-diagram-3-fill"></i>Thông tin gặp</div>

                    <div>
                        <label class="form-label">Người cần gặp <span class="text-danger">*</span></label>
                        <div class="kiosk-input-wrap">
                            <i class="bi bi-search"></i>
                            <input class="form-control" id="employeeSearch" autocomplete="off" placeholder="Tìm tên nhân viên" data-search-url="{{ route('kiosk.employees.search') }}">
                        </div>
                        <input id="hostEmployeeId" name="host_employee_id" type="hidden" value="{{ old('host_employee_id') }}" required>
                        <div class="small text-secondary mt-2" id="selectedHost">Chưa chọn nhân viên.</div>
                        <div class="list-group premium-result-list mt-2" id="employeeResults"></div>
                    </div>
                    <div>
                        <label class="form-label">Phòng ban</label>
                        <div class="kiosk-input-wrap">
                            <i class="bi bi-hospital"></i>
                            <input class="form-control" id="selectedDepartment" placeholder="Tự động sau khi chọn" readonly>
                        </div>
                    </div>

                    <div class="kiosk-section-title"><i class="bi bi-briefcase-fill"></i>Thông tin chuyến thăm</div>

                    <div>
                        <label class="form-label">Mục đích đến <span class="text-danger">*</span></label>
                        <select class="form-select" name="purpose" required>
                            <option value="" disabled {{ old('purpose') ? '' : 'selected' }}>Chọn mục đích</option>
                            @foreach (['Họp', 'Giao hàng', 'Phỏng vấn', 'Tham quan', 'Khác'] as $purpose)
                                <option value="{{ $purpose }}" @selected(old('purpose') === $purpose)>{{ $purpose }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Dự kiến rời đi</label>
                        <div class="kiosk-input-wrap">
                            <i class="bi bi-clock"></i>
                            <input class="form-control" type="time" name="expected_checkout_time" value="{{ old('expected_checkout_time', now()->addHours(2)->format('H:i')) }}">
                        </div>
                    </div>
                    <div class="kiosk-field-full">
                        <label class="form-label">Ghi chú thêm nếu có</label>
                        <textarea class="form-control" name="visitor_note" rows="2" placeholder="Ghi chú thêm nếu có">{{ old('visitor_note') }}</textarea>
                    </div>

                    <label class="form-check kiosk-policy">
                        <input class="form-check-input" type="checkbox" name="policy_accepted" value="1" required>
                        <span class="form-check-label">Tôi đồng ý tuân thủ quy định ra/vào và hướng dẫn của lễ tân/bảo vệ.</span>
                    </label>

                    <button class="btn kiosk-submit" type="submit">
                        <i class="bi bi-send-check me-2"></i>
                        Gửi yêu cầu tiếp khách
                    </button>
                </form>
            </section>

            <aside class="kiosk-side">
                <section class="kiosk-card kiosk-side-card">
                    <div class="kiosk-side-title">
                        <h2>Check-in nhanh</h2>
                        <p>Quét QR hoặc nhập mã lịch hẹn để check-in</p>
                    </div>

                    <div class="kiosk-qr-box">
                        <i class="bi bi-qr-code"></i>
                        <span>Đưa mã QR vào khung</span>
                    </div>

                    <div class="kiosk-side-divider">Hoặc nhập mã lịch hẹn</div>

                    <form id="kioskLookupForm" method="post" action="{{ route('kiosk.checkin.scan-qr') }}">
                        @csrf
                        <div class="kiosk-input-wrap mb-3">
                            <i class="bi bi-calendar2-check"></i>
                            <input class="form-control" name="qr_token" placeholder="Nhập mã lịch hẹn hoặc mã QR">
                        </div>
                        <button class="btn kiosk-submit w-100" type="submit">
                            <i class="bi bi-search me-1"></i>
                            Kiểm tra mã
                        </button>
                    </form>
                </section>

                @if ($lastVisit)
                    <section class="kiosk-card kiosk-last-card">
                        <h3><i class="bi bi-card-checklist"></i>Trạng thái yêu cầu gần nhất</h3>
                        <div class="kiosk-last-status">
                            <i class="bi bi-hourglass-split"></i>
                            <div>
                                <strong>{{ $lastStatusLabels[$lastVisit->status] ?? 'Chưa có dữ liệu' }}</strong>
                                <span>{{ $lastVisit->code }}</span>
                            </div>
                        </div>
                        <div class="kiosk-last-lines">
                            <div><span>Mã lịch</span><strong>{{ $lastVisit->code }}</strong></div>
                            <div><span>Người tiếp</span><strong>{{ $lastVisit->hostEmployee?->name ?? '-' }}</strong></div>
                            <div><span>Cập nhật lúc</span><strong>{{ $lastVisit->updated_at?->format('H:i - d/m/Y') ?? '-' }}</strong></div>
                        </div>
                        <a class="btn btn-outline-primary w-100 mt-3" href="{{ route('kiosk.checkin.status', $lastVisit) }}">
                            Xem lịch sử yêu cầu
                            <i class="bi bi-chevron-right ms-1"></i>
                        </a>
                    </section>
                @endif
            </aside>
        </section>

        <footer class="kiosk-footer">
            <div><i class="bi bi-telephone"></i><p class="m-0"><strong>Hỗ trợ</strong><span>{{ $hotline }}</span></p></div>
            <div><i class="bi bi-shield-check"></i><p class="m-0"><strong>Quy định</strong><span>Xem hướng dẫn</span></p></div>
            <div><i class="bi bi-clock"></i><p class="m-0"><strong>Thời gian làm việc</strong><span>{{ $workingHours }}</span></p></div>
            <div><i class="bi bi-emoji-smile"></i><p class="m-0"><strong>Xin cảm ơn!</strong><span>Chúc bạn một ngày tốt lành</span></p></div>
        </footer>
    </main>

    <div class="kiosk-modal-backdrop" id="kioskLookupModal" aria-hidden="true">
        <section class="kiosk-modal" role="dialog" aria-modal="true" aria-labelledby="kioskLookupTitle">
            <div class="kiosk-modal-head">
                <div class="kiosk-modal-title">
                    <div class="kiosk-modal-mark" id="kioskModalIcon"><i class="bi bi-search"></i></div>
                    <div>
                        <h3 id="kioskLookupTitle">Thông tin lịch hẹn</h3>
                        <p id="kioskModalMessage">Kiểm tra trạng thái yêu cầu của bạn.</p>
                    </div>
                </div>
                <button class="kiosk-modal-close" type="button" data-kiosk-modal-close aria-label="Đóng">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="kiosk-modal-body">
                <div class="kiosk-modal-status" id="kioskModalStatus">
                    <i class="bi bi-hourglass-split"></i>
                    <span>Đang kiểm tra</span>
                </div>
                <div class="kiosk-modal-qr" id="kioskModalQr"></div>
                <div class="kiosk-modal-grid" id="kioskModalDetails"></div>
                <div class="kiosk-modal-actions">
                    <button class="btn btn-outline-primary" type="button" data-kiosk-modal-close>Đóng</button>
                    <button class="btn kiosk-submit" type="button" id="kioskModalConfirm" hidden>
                        <i class="bi bi-box-arrow-in-right me-1"></i>
                        Xác nhận check-in
                    </button>
                </div>
            </div>
        </section>
    </div>

    <script>
        const clockNode = document.getElementById('kioskClock');
        const dateNode = document.getElementById('kioskDate');

        function updateClock() {
            const now = new Date();
            clockNode.textContent = new Intl.DateTimeFormat('vi-VN', { hour: '2-digit', minute: '2-digit' }).format(now);
            dateNode.textContent = new Intl.DateTimeFormat('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' }).format(now);
        }

        updateClock();
        setInterval(updateClock, 30000);

        const searchInput = document.getElementById('employeeSearch');
        const resultsBox = document.getElementById('employeeResults');
        const selectedHost = document.getElementById('selectedHost');
        const selectedDepartment = document.getElementById('selectedDepartment');
        const hostEmployeeId = document.getElementById('hostEmployeeId');
        let searchTimer = null;

        function renderEmployees(items) {
            resultsBox.innerHTML = '';

            if (items.length === 0) {
                resultsBox.innerHTML = '<div class="list-group-item text-secondary">Không tìm thấy nhân viên phù hợp.</div>';
                return;
            }

            items.forEach((employee) => {
                const item = document.createElement('button');
                item.type = 'button';
                item.className = 'list-group-item list-group-item-action';

                const name = document.createElement('strong');
                name.textContent = employee.name;
                const detail = document.createElement('span');
                detail.className = 'text-secondary';
                detail.textContent = `${employee.position ?? '-'} - ${employee.department ?? '-'}`;

                item.appendChild(name);
                item.appendChild(document.createElement('br'));
                item.appendChild(detail);
                item.addEventListener('click', () => {
                    hostEmployeeId.value = employee.id;
                    selectedHost.textContent = `Đã chọn: ${employee.name}`;
                    selectedDepartment.value = employee.department ?? '';
                    resultsBox.innerHTML = '';
                    searchInput.value = employee.name;
                });
                resultsBox.appendChild(item);
            });
        }

        searchInput.addEventListener('input', () => {
            clearTimeout(searchTimer);
            hostEmployeeId.value = '';
            selectedDepartment.value = '';
            selectedHost.textContent = 'Chưa chọn nhân viên.';

            const keyword = searchInput.value.trim();
            if (keyword.length < 2) {
                resultsBox.innerHTML = '';
                return;
            }

            searchTimer = setTimeout(async () => {
                const url = `${searchInput.dataset.searchUrl}?q=${encodeURIComponent(keyword)}`;
                const response = await fetch(url, { headers: { Accept: 'application/json' } });
                const payload = await response.json();
                renderEmployees(payload.data ?? []);
            }, 250);
        });

        const lookupForm = document.getElementById('kioskLookupForm');
        const lookupModal = document.getElementById('kioskLookupModal');
        const modalTitle = document.getElementById('kioskLookupTitle');
        const modalMessage = document.getElementById('kioskModalMessage');
        const modalStatus = document.getElementById('kioskModalStatus');
        const modalIcon = document.getElementById('kioskModalIcon');
        const modalDetails = document.getElementById('kioskModalDetails');
        const modalQr = document.getElementById('kioskModalQr');
        const modalConfirm = document.getElementById('kioskModalConfirm');

        function openLookupModal() {
            lookupModal.classList.add('is-open');
            lookupModal.setAttribute('aria-hidden', 'false');
        }

        function closeLookupModal() {
            lookupModal.classList.remove('is-open');
            lookupModal.setAttribute('aria-hidden', 'true');
        }

        function renderLookupModal(payload, isError = false) {
            const visit = payload.visit ?? null;
            const status = visit?.status ?? 'error';
            const statusLabel = visit?.status_label ?? 'Không tìm thấy';
            const rows = visit ? [
                ['Mã lịch hẹn', visit.code],
                ['Khách', visit.visitor_name],
                ['Công ty', visit.visitor_company],
                ['Người tiếp khách', visit.host_name],
                ['Phòng ban', visit.department],
                ['Giờ hẹn', visit.scheduled_at],
            ] : [
                ['Kết quả', payload.message ?? 'Không tìm thấy thông tin phù hợp.'],
            ];

            modalTitle.textContent = isError && !visit ? 'Không tìm thấy lịch hẹn' : 'Thông tin lịch hẹn';
            modalMessage.textContent = visit?.status_hint ?? payload.message ?? 'Vui lòng kiểm tra lại mã vừa nhập.';
            modalStatus.className = `kiosk-modal-status is-${status}`;
            modalStatus.querySelector('span').textContent = statusLabel;
            modalIcon.innerHTML = status === 'approved'
                ? '<i class="bi bi-check2-circle"></i>'
                : status === 'checked_in'
                    ? '<i class="bi bi-person-check"></i>'
                    : isError
                        ? '<i class="bi bi-exclamation-triangle"></i>'
                        : '<i class="bi bi-hourglass-split"></i>';

            modalDetails.innerHTML = rows.map(([label, value]) => `
                <div class="kiosk-modal-row">
                    <span>${label}</span>
                    <strong>${value ?? '-'}</strong>
                </div>
            `).join('');

            modalQr.classList.toggle('has-qr', Boolean(visit?.qr_svg));
            modalQr.innerHTML = visit?.qr_svg ?? '';

            modalConfirm.hidden = !(visit?.can_confirm && visit?.confirm_url);
            modalConfirm.dataset.confirmUrl = visit?.confirm_url ?? '';
            modalConfirm.disabled = false;
            modalConfirm.innerHTML = '<i class="bi bi-box-arrow-in-right me-1"></i>Xác nhận check-in';
            openLookupModal();
        }

        lookupForm?.addEventListener('submit', async (event) => {
            event.preventDefault();
            const formData = new FormData(lookupForm);
            const submitButton = lookupForm.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang kiểm tra';

            try {
                const response = await fetch(lookupForm.action, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: formData,
                });
                const payload = await response.json();
                renderLookupModal(payload, !response.ok);
            } catch (error) {
                renderLookupModal({ message: 'Không thể kiểm tra mã lúc này. Vui lòng thử lại.' }, true);
            } finally {
                submitButton.disabled = false;
                submitButton.innerHTML = '<i class="bi bi-search me-1"></i>Kiểm tra mã';
            }
        });

        modalConfirm?.addEventListener('click', async () => {
            const confirmUrl = modalConfirm.dataset.confirmUrl;
            if (!confirmUrl) return;

            modalConfirm.disabled = true;
            modalConfirm.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang check-in';

            try {
                const response = await fetch(confirmUrl, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': lookupForm.querySelector('input[name="_token"]').value,
                    },
                });
                const payload = await response.json();
                renderLookupModal(payload, !response.ok);
            } catch (error) {
                renderLookupModal({ message: 'Không thể xác nhận check-in lúc này. Vui lòng thử lại.' }, true);
            }
        });

        document.querySelectorAll('[data-kiosk-modal-close]').forEach((button) => {
            button.addEventListener('click', closeLookupModal);
        });

        lookupModal?.addEventListener('click', (event) => {
            if (event.target === lookupModal) closeLookupModal();
        });

        const kioskNotice = document.getElementById('kioskNotice');
        if (kioskNotice) {
            setTimeout(() => {
                kioskNotice.classList.add('is-hiding');
                setTimeout(() => kioskNotice.remove(), 260);
            }, 4200);
        }
    </script>
</body>
</html>

<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kiosk | Gatehouse Pro</title>
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

        html, body {
            height: 100vh;
            overflow: hidden;
        }

        body {
            margin: 0;
            color: var(--kiosk-ink);
            background:
                radial-gradient(circle at 14% -6%, rgba(12, 180, 216, .16), transparent 34%),
                radial-gradient(circle at 100% 8%, rgba(20, 107, 215, .10), transparent 30%),
                linear-gradient(180deg, #ffffff 0%, var(--kiosk-bg) 100%);
            font-family: "Manrope", sans-serif;
        }

        .kiosk-shell {
            height: 100vh;
            display: grid;
            grid-template-rows: auto minmax(0, 1fr);
            gap: .75rem;
            width: min(1420px, calc(100vw - 48px));
            margin: 0 auto;
            padding: .65rem 0;
        }

        .kiosk-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .kiosk-brand {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: .15rem;
        }

        .kiosk-logo-group {
            display: inline-flex;
            align-items: center;
            gap: .55rem;
        }

        .kiosk-logo {
            width: auto;
            min-width: 64px;
            max-width: 170px;
            height: 62px;
            place-items: center;
            display: grid;
            border-radius: 14px;
            color: #fff;
            background: linear-gradient(135deg, var(--kiosk-blue), var(--kiosk-cyan));
            box-shadow: 0 16px 34px rgba(20, 107, 215, .18);
            overflow: hidden;
        }

        .kiosk-logo.has-logo {
            width: 170px;
            min-width: 170px;
            max-width: 170px;
            background: transparent;
            border: 0;
            box-shadow: none;
            overflow: visible;
        }

        .kiosk-logo img {
            width: 100%;
            max-width: 100%;
            height: 100%;
            max-height: 100%;
            object-fit: contain;
            object-position: center;
            padding: 0;
            transform: scale(1.06);
            transform-origin: center;
        }

        .kiosk-logo-separator {
            display: block;
            width: 1px;
            height: 38px;
            flex: 0 0 1px;
            margin: 0 .25rem;
            border-radius: 999px;
            background: #cbd8e6;
        }

        .kiosk-brand-caption {
            margin: 0;
            padding-left: .1rem;
            color: var(--kiosk-muted);
            font-size: .72rem;
            font-weight: 500;
            line-height: 1.25;
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

        .kiosk-header-help {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            padding: .15rem 0 .15rem 1rem;
            border-left: 1px solid var(--kiosk-line);
            color: #526b87;
            font-size: .78rem;
            font-weight: 800;
            white-space: nowrap;
        }

        .kiosk-header-help i {
            color: var(--kiosk-blue);
            font-size: 1.35rem;
        }

        .kiosk-header-help strong {
            display: block;
            color: var(--kiosk-ink);
            font-size: .9rem;
            line-height: 1.1;
        }

        .kiosk-header-help small {
            display: block;
            color: var(--kiosk-muted);
            font-size: .68rem;
            font-weight: 800;
            line-height: 1.1;
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
            grid-template-columns: minmax(0, 920px) minmax(360px, 410px);
            gap: 1.25rem;
            align-items: stretch;
            justify-content: center;
            min-height: 0;
        }

        .kiosk-card {
            border: 1px solid var(--kiosk-line);
            border-radius: 20px;
            background: rgba(255, 255, 255, .96);
            box-shadow: 0 22px 58px rgba(17, 39, 68, .08);
        }

        .kiosk-form-card {
            min-height: 0;
            width: 100%;
            padding: 1rem 1.2rem;
            overflow-y: auto;
            max-height: 100%;
        }

        .kiosk-side {
            min-height: 0;
            max-height: 100%;
            overflow-y: auto;
        }

        .kiosk-title {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: .5rem;
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

        .kiosk-mode-actions {
            display: inline-grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: .45rem;
            min-width: 390px;
            padding: .28rem;
            border: 1px solid var(--kiosk-line);
            border-radius: 16px;
            background: #f8fbff;
        }

        .kiosk-mode-button {
            min-height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .38rem;
            border: 0;
            border-radius: 12px;
            background: #fff;
            color: #315b89;
            font-family: inherit;
            font-size: .82rem;
            font-weight: 900;
            box-shadow: 0 8px 18px rgba(15, 64, 110, .06);
            white-space: nowrap;
        }

        .kiosk-mode-button.is-active {
            color: #fff;
            background: linear-gradient(135deg, var(--kiosk-blue), var(--kiosk-cyan));
            box-shadow: 0 12px 24px rgba(20, 107, 215, .16);
        }

        .kiosk-mode-button:focus-visible {
            outline: 3px solid rgba(20, 107, 215, .18);
            outline-offset: 2px;
        }

        .kiosk-flat-form {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .55rem 1.25rem;
        }

        .kiosk-form-section {
            grid-column: 1 / -1;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .45rem 1.25rem;
            padding: .2rem 0 .1rem;
            border: 0;
            border-radius: 0;
            background: transparent;
            box-shadow: none;
        }

        .kiosk-section-title {
            grid-column: 1 / -1;
            display: flex;
            align-items: center;
            gap: .5rem;
            margin: .05rem 0 .02rem;
            color: var(--kiosk-blue);
            font-size: .72rem;
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
            min-height: 40px;
            border-color: var(--kiosk-line);
            border-radius: 12px;
            color: var(--kiosk-ink);
            font-size: .88rem;
        }

        .kiosk-flat-form textarea.form-control {
            min-height: 46px;
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
            padding: .35rem 0;
            border: 0;
            border-radius: 0;
            background: transparent;
            color: #334963;
        }

        .kiosk-extra-panel {
            grid-column: 1 / -1;
            display: grid;
            gap: .62rem;
            margin-top: .1rem;
            padding: .55rem;
            border: 1px solid #e3edf8;
            border-radius: 16px;
            background: #f8fbff;
        }

        .kiosk-extra-toggle {
            width: 100%;
            min-height: 38px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .8rem;
            border: 0;
            border-radius: 12px;
            color: #315b89;
            background: #fff;
            font: inherit;
            font-size: .78rem;
            font-weight: 800;
            padding: .45rem .7rem;
            text-align: left;
        }

        .kiosk-extra-toggle span {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
        }

        .kiosk-extra-toggle > span {
            flex: 1;
        }

        .kiosk-extra-toggle small {
            display: none;
        }

        .kiosk-extra-toggle i:last-child {
            transition: transform .18s ease;
        }

        .kiosk-extra-panel.is-open .kiosk-extra-toggle i:last-child {
            transform: rotate(180deg);
        }

        .kiosk-extra-content {
            display: none;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .55rem 1rem;
        }

        .kiosk-extra-panel.is-open .kiosk-extra-content {
            display: grid;
        }

        .kiosk-submit {
            min-height: 44px;
            border: 0;
            border-radius: 13px;
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

        .kiosk-flat-form > .kiosk-submit {
            width: 100%;
            justify-self: center;
        }

        .kiosk-side-card .kiosk-submit {
            width: 100%;
        }

        .kiosk-side {
            display: grid;
            gap: .75rem;
            width: 100%;
        }

        .kiosk-side-card,
        .kiosk-last-card {
            padding: .9rem 1rem;
        }

        .kiosk-side-title {
            margin-bottom: .85rem;
            text-align: center;
        }

        .kiosk-side-title h2 {
            font-size: 1.28rem;
        }

        .kiosk-side-title p {
            font-size: .86rem;
            line-height: 1.5;
        }

        .kiosk-qr-box {
            position: relative;
            width: min(250px, 100%);
            height: 118px;
            min-height: 0;
            margin: 0 auto;
            display: grid;
            place-items: center;
            overflow: hidden;
            border: 1px dashed #98c4f4;
            border-radius: 16px;
            background:
                linear-gradient(90deg, rgba(20, 107, 215, .055) 1px, transparent 1px),
                linear-gradient(rgba(20, 107, 215, .055) 1px, transparent 1px),
                #f2f8ff;
            background-size: 24px 24px;
            color: var(--kiosk-blue);
        }

        .kiosk-qr-box .qr-camera-video {
            position: absolute;
            inset: 0;
            z-index: 1;
            display: none;
            width: 100%;
            height: 100%;
            object-fit: cover;
            background: #06172b;
        }

        .kiosk-qr-box .qr-camera-placeholder {
            position: absolute;
            inset: 0;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: .22rem;
            padding: .55rem;
        }

        .kiosk-qr-box i {
            font-size: 2.7rem;
            filter: drop-shadow(0 10px 18px rgba(20, 107, 215, .12));
        }

        .kiosk-qr-box span {
            position: static;
            display: block;
            margin-top: .35rem;
            color: #526b87;
            font-size: .72rem;
            font-weight: 800;
        }

        .kiosk-qr-box small {
            display: block;
            margin-top: .18rem;
            color: #7890aa;
            font-size: .68rem;
            font-weight: 700;
        }

        .kiosk-scan-mode {
            display: grid;
            grid-template-columns: 1fr;
            gap: .55rem;
            margin: .68rem 0 .58rem;
        }

        .kiosk-scan-chip {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .38rem;
            min-height: 36px;
            border: 1px solid #d8e6f5;
            border-radius: 13px;
            background: #f8fbff;
            color: #466585;
            font-size: .74rem;
            font-weight: 900;
        }

        .kiosk-camera-toolbar {
            display: grid;
            grid-template-columns: 1fr;
            gap: .48rem;
            margin-top: .6rem;
        }

        .kiosk-camera-toolbar .qr-camera-button {
            width: 100%;
            min-height: 42px;
            border: 1px solid #cfe0f5;
            border-radius: 14px;
            background: #fff;
            color: var(--kiosk-blue);
            font-size: .82rem;
            font-family: inherit;
            line-height: 1;
            font-weight: 900;
            box-shadow: 0 8px 18px rgba(15, 64, 110, .06);
        }

        .kiosk-camera-toolbar .qr-camera-button.primary {
            border-color: transparent;
            background: linear-gradient(135deg, var(--kiosk-blue), var(--kiosk-cyan));
            color: #fff;
            box-shadow: 0 12px 24px rgba(20, 107, 215, .18);
        }

        .kiosk-camera-toolbar .qr-camera-button:hover {
            transform: translateY(-1px);
        }

        .kiosk-camera-note {
            display: flex;
            gap: .45rem;
            align-items: flex-start;
            padding: .58rem .68rem;
            border: 1px solid #dbeafe;
            border-radius: 14px;
            background: #f5faff;
            color: #5a7390;
            font-size: .72rem;
            line-height: 1.4;
        }

        .kiosk-camera-note i {
            color: var(--kiosk-blue);
        }

        .kiosk-side-divider {
            display: flex;
            align-items: center;
            gap: .62rem;
            margin: .68rem 0 .58rem;
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

        .kiosk-last-card h3 {
            display: flex;
            align-items: center;
            gap: .45rem;
            margin: 0 0 .65rem;
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
            padding: .6rem;
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
            gap: .4rem;
            margin-top: .58rem;
        }

        .kiosk-last-lines div {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: .5rem .65rem;
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

        .kiosk-last-card .btn {
            min-height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 13px;
            font-weight: 800;
        }

        .kiosk-footer {
            display: none;
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
        .kiosk-modal { width: min(760px, 100%); border: 1px solid #dbe8f6; border-radius: 30px; background: #fff; box-shadow: 0 34px 90px rgba(8,28,52,.28); overflow: hidden; }
        .kiosk-modal-head { display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: 1.65rem 1.9rem 1rem; background: #fff; }
        .kiosk-modal-title { display: block; }
        .kiosk-modal-mark { display: none; }
        .kiosk-modal-title h3 { margin: 0; color: var(--kiosk-ink); font-family: "Plus Jakarta Sans", sans-serif; font-size: clamp(1.55rem, 2vw, 2rem); font-weight: 650; letter-spacing: -.02em; }
        .kiosk-modal-title p { display: none; }
        .kiosk-modal-close { width: 48px; height: 48px; border: 1px solid #dbe8f6; border-radius: 999px; background: #fff; color: #526b87; font-size: 1.2rem; }
        .kiosk-modal-body { padding: .75rem 1.9rem 1.9rem; }
        .kiosk-modal-status { display: none; }
        .kiosk-modal-status.is-approved,.kiosk-modal-status.is-checked_in { color: #047857; background: #dcfce7; }
        .kiosk-modal-status.is-checked_out { color: #0f6bdc; background: #dbeafe; }
        .kiosk-modal-status.is-rejected,.kiosk-modal-status.is-cancelled { color: #be123c; background: #ffe4e6; }
        .kiosk-modal-alert { display: grid; grid-template-columns: 86px 1fr; gap: 1rem; align-items: center; margin-bottom: 1.3rem; padding: 1.08rem 1.15rem; border-radius: 22px; border: 1px solid #dbeafe; background: #eff6ff; color: #1d4ed8; }
        .kiosk-modal-alert i { width: 72px; height: 72px; display: grid; place-items: center; border-radius: 999px; background: rgba(255,255,255,.68); font-size: 2rem; }
        .kiosk-modal-alert strong { display: block; color: inherit; font-size: 1.1rem; font-weight: 650; }
        .kiosk-modal-alert span { display: block; margin-top: .38rem; color: #142945; font-size: .96rem; line-height: 1.5; }
        .kiosk-modal-alert.is-success { border-color: #bbf7d0; background: #ecfdf5; color: #047857; }
        .kiosk-modal-alert.is-warning { border-color: #fde68a; background: #fffbeb; color: #b45309; }
        .kiosk-modal-alert.is-danger { border-color: #fecaca; background: #fff1f2; color: #be123c; }
        .kiosk-modal-grid { display: grid; gap: 0; border: 1px solid #dbe8f6; border-radius: 20px; overflow: hidden; background: #fff; }
        .kiosk-modal-row { display: grid; grid-template-columns: 34px minmax(130px, 1fr) minmax(180px, 1.4fr); align-items: center; gap: .85rem; padding: .9rem 1rem; border-bottom: 1px solid #e4edf8; background: #fbfdff; color: var(--kiosk-muted); }
        .kiosk-modal-row:last-child { border-bottom: 0; }
        .kiosk-modal-row i { color: #617895; font-size: 1.1rem; text-align: center; }
        .kiosk-modal-row strong { color: var(--kiosk-ink); text-align: right; font-size: .98rem; font-weight: 600; }
        .kiosk-modal-qr { display: none; place-items: center; margin-bottom: 1rem; padding: 1rem; border: 1px solid #dbe8f6; border-radius: 20px; background: #f8fbff; }
        .kiosk-modal-qr.has-qr { display: grid; }
        .kiosk-modal-qr svg { width: 150px; height: 150px; }
        .kiosk-modal-actions { display: grid; grid-template-columns: 1fr; gap: .75rem; margin-top: 1.25rem; }
        .kiosk-modal-actions .btn { min-height: 54px; border-radius: 16px; font-weight: 650; }

        /* Keep the kiosk calm and readable on large touch screens. */
        .kiosk-shell :is(h1, h2, h3, strong, label, button, .btn, .kiosk-section-title, .kiosk-mode-button) {
            font-weight: 500 !important;
        }

        .kiosk-title h1 {
            font-weight: 600 !important;
        }

        .kiosk-submit {
            font-weight: 600 !important;
        }
        .kiosk-modal-actions .btn-outline-primary { border-color: #071f3d; color: #fff; background: #071f3d; }
        .kiosk-modal-actions .btn-outline-primary:hover { border-color: #0b2a50; color: #fff; background: #0b2a50; }

        @media (max-width: 1180px) {
            .kiosk-main { grid-template-columns: 1fr; }
            .kiosk-side { width: 100%; grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width: 760px) {
            .kiosk-header, .kiosk-tools { align-items: flex-start; flex-direction: column; }
            .kiosk-flat-form, .kiosk-form-section, .kiosk-extra-content, .kiosk-side, .kiosk-footer { grid-template-columns: 1fr; }
            .kiosk-title { flex-direction: column; }
            .kiosk-mode-actions { width: 100%; min-width: 0; }
        }

        @media (max-width: 1700px) {
            .kiosk-shell {
                width: min(1420px, calc(100vw - 72px));
                gap: 1.15rem;
                padding-top: 1.15rem;
                padding-bottom: 1.15rem;
            }

            .kiosk-main {
                grid-template-columns: minmax(0, 920px) minmax(360px, 410px);
                gap: 1.55rem;
            }

            .kiosk-form-card {
                padding: 1.25rem 1.45rem;
                border-radius: 20px;
            }

            .kiosk-side {
                width: 100%;
            }

            .kiosk-side-card,
            .kiosk-last-card {
                padding: 1.1rem;
                border-radius: 20px;
            }

            .kiosk-title {
                margin-bottom: .65rem;
            }

            .kiosk-mode-actions {
                min-width: 360px;
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
                gap: .62rem 1.05rem;
            }

            .kiosk-form-section {
                gap: .55rem 1.05rem;
                padding: .28rem 0 .18rem;
                border-radius: 0;
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
                min-height: 42px;
                border-radius: 13px;
                font-size: .9rem;
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
                min-height: 56px;
            }

            .kiosk-policy {
                padding: .42rem .62rem;
                border-radius: 12px;
                font-size: .78rem;
            }

            .kiosk-submit {
                min-height: 48px;
                border-radius: 15px;
                font-size: .95rem;
            }

            .kiosk-side-title {
                margin-bottom: .75rem;
            }

            .kiosk-side-title h2 {
                font-size: 1.22rem;
            }

            .kiosk-qr-box {
                width: min(240px, 100%);
                height: 112px;
                min-height: 0;
                border-radius: 15px;
                background-size: 20px 20px;
            }

            .kiosk-qr-box i {
                font-size: 2.6rem;
            }

            .kiosk-side-divider {
                margin: .62rem 0 .5rem;
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
                width: min(1240px, calc(100vw - 42px));
            }

            .kiosk-main {
                grid-template-columns: minmax(0, 1fr) minmax(340px, 370px);
                gap: 1.35rem;
            }

            .kiosk-side {
                width: 100%;
            }

            .kiosk-qr-box {
                width: min(230px, 100%);
                height: 108px;
                min-height: 0;
            }

            .kiosk-brand strong {
                font-size: 1.08rem;
            }

            .kiosk-brand span {
                font-size: .64rem;
            }

            .kiosk-logo {
                min-width: 54px;
                max-width: 126px;
                height: 48px;
                border-radius: 13px;
            }

            .kiosk-logo.has-logo {
                width: 126px;
                min-width: 126px;
                max-width: 126px;
            }

            .kiosk-logo img {
                max-width: 100%;
                height: 100%;
                max-height: 100%;
                padding: 0;
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
                width: 100%;
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 1180px) {
            .kiosk-shell {
                width: min(980px, calc(100vw - 24px));
            }

            .kiosk-main {
                grid-template-columns: 1fr;
            }

            .kiosk-form-card {
                max-width: none;
            }

            .kiosk-side {
                width: 100%;
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 760px) {
            .kiosk-shell {
                width: min(100%, calc(100vw - 18px));
            }

            .kiosk-side {
                grid-template-columns: 1fr;
            }

            .kiosk-flat-form > .kiosk-submit {
                width: 100%;
            }

            .kiosk-header-help {
                border-left: 0;
                padding-left: 0;
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
    $ownerLogoUrl = $settings['kiosk.owner_logo_url'] ?? ($settings['admin.logo_url'] ?? null);
    $customerLogoUrl = $settings['kiosk.customer_logo_url'] ?? ($settings['kiosk.logo_url'] ?? null);
    $logoUrls = array_values(array_filter([$ownerLogoUrl, $customerLogoUrl]));
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
                <div class="kiosk-logo-group">
                    @if ($ownerLogoUrl)
                        <div class="kiosk-logo has-logo">
                            <img src="{{ $ownerLogoUrl }}" alt="Logo hệ thống">
                        </div>
                    @endif
                    @if ($ownerLogoUrl && $customerLogoUrl)
                        <span class="kiosk-logo-separator" aria-hidden="true"></span>
                    @endif
                    @if ($customerLogoUrl)
                        <div class="kiosk-logo has-logo">
                            <img src="{{ $customerLogoUrl }}" alt="{{ $companyName }}">
                        </div>
                    @elseif (! $ownerLogoUrl)
                        <div class="kiosk-logo">
                            <i class="bi bi-shield-check"></i>
                        </div>
                    @endif
                </div>
                <p class="kiosk-brand-caption">Hệ thống quản lý khách đến</p>
            </div>
            <div class="kiosk-tools">
                <select class="form-select" id="kioskLanguage" aria-label="Chọn ngôn ngữ">
                    <option value="vi">Tiếng Việt</option>
                    <option value="en">English</option>
                </select>
                <div class="kiosk-clock">
                    <strong id="kioskClock">--:--</strong>
                    <span id="kioskDate">--</span>
                </div>
                <div class="kiosk-header-help">
                    <i class="bi bi-telephone"></i>
                    <span>
                        <small>Hỗ trợ</small>
                        <strong>{{ $hotline }}</strong>
                    </span>
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
                    <div class="kiosk-mode-actions" aria-label="Tác vụ kiosk">
                        <button class="kiosk-mode-button is-active" type="button" data-kiosk-mode-action="register">
                            <i class="bi bi-pencil-square"></i>
                            Đăng ký
                        </button>
                        <button class="kiosk-mode-button" type="button" data-kiosk-mode-action="checkin">
                            <i class="bi bi-box-arrow-in-right"></i>
                            Check-in
                        </button>
                        <button class="kiosk-mode-button" type="button" data-kiosk-mode-action="checkout">
                            <i class="bi bi-box-arrow-left"></i>
                            Check-out
                        </button>
                    </div>
                </div>

                <form class="kiosk-flat-form" id="kioskRegisterForm" method="post" action="{{ route('kiosk.checkin.manual') }}">
                    @csrf
                    <div class="kiosk-form-section">
                    <div class="kiosk-section-title"><i class="bi bi-person-fill"></i>1. Thông tin khách</div>

                    <div>
                        <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                        <div class="kiosk-input-wrap">
                            <i class="bi bi-person"></i>
                            <input class="form-control" id="kioskVisitorName" name="visitor_name" value="{{ old('visitor_name') }}" placeholder="Nhập họ và tên" required>
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
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <div class="kiosk-input-wrap">
                            <i class="bi bi-envelope"></i>
                            <input class="form-control" type="email" name="visitor_email" value="{{ old('visitor_email') }}" placeholder="example@email.com" required>
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Công ty / Tổ chức <span class="text-danger">*</span></label>
                        <div class="kiosk-input-wrap">
                            <i class="bi bi-building"></i>
                            <input class="form-control" name="visitor_company" value="{{ old('visitor_company') }}" placeholder="Nhập tên công ty" required>
                        </div>
                    </div>

                    </div>

                    <div class="kiosk-extra-panel {{ old('visitor_identity_no') || old('visitor_identity_issued_place') || old('visitor_identity_issued_date') || old('expected_checkout_time') ? 'is-open' : '' }}" data-kiosk-extra-panel>
                        <button class="kiosk-extra-toggle" type="button" data-kiosk-extra-toggle aria-expanded="{{ old('visitor_identity_no') || old('visitor_identity_issued_place') || old('visitor_identity_issued_date') || old('expected_checkout_time') ? 'true' : 'false' }}">
                            <span><i class="bi bi-plus-circle"></i>Thông tin xác thực</span>
                            <i class="bi bi-chevron-down"></i>
                        </button>
                        <div class="kiosk-extra-content">
                            <div>
                                <label class="form-label">Căn cước công dân</label>
                                <div class="kiosk-input-wrap">
                                    <i class="bi bi-card-text"></i>
                                    <input class="form-control" name="visitor_identity_no" value="{{ old('visitor_identity_no') }}" placeholder="Nhập số CCCD">
                                </div>
                            </div>
                            <div>
                                <label class="form-label">Nơi cấp</label>
                                <div class="kiosk-input-wrap">
                                    <i class="bi bi-geo-alt"></i>
                                    <input class="form-control" name="visitor_identity_issued_place" value="{{ old('visitor_identity_issued_place') }}" placeholder="Nhập nơi cấp">
                                </div>
                            </div>
                            <div>
                                <label class="form-label">Ngày cấp</label>
                                <div class="kiosk-input-wrap">
                                    <i class="bi bi-calendar3"></i>
                                    <input class="form-control" type="date" name="visitor_identity_issued_date" value="{{ old('visitor_identity_issued_date') }}">
                                </div>
                            </div>
                            <div>
                                <label class="form-label">Dự kiến rời đi</label>
                                <div class="kiosk-input-wrap">
                                    <i class="bi bi-clock"></i>
                                    <input class="form-control" type="time" name="expected_checkout_time" value="{{ old('expected_checkout_time', now()->addHours(2)->format('H:i')) }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="kiosk-form-section">
                    <div class="kiosk-section-title"><i class="bi bi-diagram-3-fill"></i>2. Thông tin gặp</div>

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

                    </div>
                    <div class="kiosk-form-section">
                    <div class="kiosk-section-title"><i class="bi bi-briefcase-fill"></i>3. Thông tin chuyến thăm</div>

                    <div>
                        <label class="form-label">Mục đích đến <span class="text-danger">*</span></label>
                        <select class="form-select" name="purpose" required>
                            <option value="" disabled {{ old('purpose') ? '' : 'selected' }}>Chọn mục đích</option>
                            @foreach (['Họp', 'Giao hàng', 'Phỏng vấn', 'Tham quan', 'Khác'] as $purpose)
                                <option value="{{ $purpose }}" @selected(old('purpose') === $purpose)>{{ $purpose }}</option>
                            @endforeach
                        </select>
                    </div>
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
                        <h2 id="kioskLookupHeading">Check-in trực tiếp</h2>
                        <p id="kioskLookupHelp">Quét QR hoặc nhập mã lịch hẹn đã duyệt để hệ thống check-in ngay.</p>
                    </div>

                    <div class="kiosk-qr-box qr-camera-frame" id="kioskQrFrame">
                        <video class="qr-camera-video" id="kioskQrVideo" playsinline muted></video>
                        <div class="qr-camera-placeholder">
                            <i class="bi bi-qr-code"></i>
                            <span>Đưa mã QR vào khung</span>
                        </div>
                    </div>

                    <div class="kiosk-camera-note">
                        <i class="bi bi-info-circle"></i>
                        <span id="kioskQrStatus">Nếu không có mã QR, khách có thể nhập mã lịch hẹn bên dưới.</span>
                    </div>

                    <div class="kiosk-side-divider" id="kioskLookupDivider">Hoặc nhập mã check-in</div>

                    <form
                        id="kioskLookupForm"
                        method="post"
                        action="{{ route('kiosk.checkin.scan-qr') }}"
                        data-checkin-url="{{ route('kiosk.checkin.scan-qr') }}"
                        data-checkout-url="{{ route('kiosk.checkout.scan-qr') }}"
                    >
                        @csrf
                        <input type="hidden" id="kioskLookupMode" name="mode" value="checkin">
                        <div class="kiosk-input-wrap mb-3">
                            <i class="bi bi-calendar2-check"></i>
                            <input class="form-control" id="kioskQrInput" name="qr_token" placeholder="Nhập mã lịch hẹn hoặc mã QR">
                        </div>
                        <button class="btn kiosk-submit w-100" type="submit" id="kioskLookupSubmit">
                            <i class="bi bi-search me-1"></i>
                            Check-in ngay
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
                <div class="kiosk-modal-alert" id="kioskModalAlert">
                    <i class="bi bi-info-circle"></i>
                    <div>
                        <strong>Thông báo</strong>
                        <span>Đang kiểm tra mã lịch hẹn.</span>
                    </div>
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

    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script src="{{ \App\Support\AssetVersion::url('js/gatehouse-qr-scanner.js') }}"></script>
    <script>
        const kioskTranslations = {
            'Hệ thống quản lý khách đến': 'Visitor management system',
            'Hỗ trợ': 'Support',
            'Đăng ký khách': 'Visitor registration',
            'Vui lòng nhập thông tin để được hỗ trợ nhanh chóng.': 'Please enter your information for faster assistance.',
            'Đăng ký': 'Register',
            '1. Thông tin khách': '1. Visitor information',
            'Họ và tên': 'Full name',
            'Số điện thoại': 'Phone number',
            'Công ty / Tổ chức': 'Company / Organization',
            'Thông tin xác thực': 'Identity information',
            'Căn cước công dân': 'Identity document',
            'Nơi cấp': 'Place of issue',
            'Ngày cấp': 'Date of issue',
            'Dự kiến rời đi': 'Expected departure',
            '2. Thông tin gặp': '2. Meeting information',
            'Người cần gặp': 'Person to meet',
            'Phòng ban': 'Department',
            'Chưa chọn nhân viên.': 'No employee selected.',
            '3. Thông tin chuyến thăm': '3. Visit information',
            'Mục đích đến': 'Purpose of visit',
            'Chọn mục đích': 'Select a purpose',
            'Họp': 'Meeting',
            'Giao hàng': 'Delivery',
            'Phỏng vấn': 'Interview',
            'Tham quan': 'Site visit',
            'Khác': 'Other',
            'Tôi đồng ý tuân thủ quy định ra/vào và hướng dẫn của lễ tân/bảo vệ.': 'I agree to follow the access rules and instructions from reception/security.',
            'Gửi yêu cầu tiếp khách': 'Submit visit request',
            'Check-in trực tiếp': 'Direct check-in',
            'Check-out trực tiếp': 'Direct check-out',
            'Quét QR hoặc nhập mã lịch hẹn đã duyệt để hệ thống check-in ngay.': 'Scan the QR code or enter an approved visit code to check in.',
            'Quét QR hoặc nhập mã lịch hẹn của khách đang trong công ty để check-out ngay.': 'Scan the QR code or enter the visit code of a visitor currently inside to check out.',
            'Đưa mã QR vào khung': 'Place the QR code inside the frame',
            'Nếu không có mã QR, khách có thể nhập mã lịch hẹn bên dưới.': 'If no QR code is available, enter the visit code below.',
            'Hoặc nhập mã check-in': 'Or enter a check-in code',
            'Hoặc nhập mã check-out': 'Or enter a check-out code',
            'Check-in ngay': 'Check in now',
            'Check-out ngay': 'Check out now',
            'Trạng thái yêu cầu gần nhất': 'Latest request status',
            'Mã lịch': 'Visit code',
            'Người tiếp': 'Host',
            'Cập nhật lúc': 'Last updated',
            'Xem lịch sử yêu cầu': 'View request history',
            'Quy định': 'Guidelines',
            'Xem hướng dẫn': 'View instructions',
            'Thời gian làm việc': 'Working hours',
            'Xin cảm ơn!': 'Thank you!',
            'Chúc bạn một ngày tốt lành': 'Have a great day',
            'Không tìm thấy nhân viên phù hợp.': 'No matching employee found.',
        };
        const kioskPlaceholders = {
            'Nhập họ và tên': 'Enter full name',
            'Nhập số điện thoại': 'Enter phone number',
            'Nhập tên công ty': 'Enter company name',
            'Nhập số CCCD': 'Enter identity number',
            'Nhập nơi cấp': 'Enter place of issue',
            'Tìm tên nhân viên': 'Search employee name',
            'Tự động sau khi chọn': 'Filled automatically after selection',
            'Nhập mã lịch hẹn hoặc mã QR': 'Enter visit code or QR code',
            'Nhập mã lịch hẹn để check-out': 'Enter visit code to check out',
        };
        const reverseKioskTranslations = Object.fromEntries(Object.entries(kioskTranslations).map(([vi, en]) => [en, vi]));
        const reverseKioskPlaceholders = Object.fromEntries(Object.entries(kioskPlaceholders).map(([vi, en]) => [en, vi]));
        let kioskLanguage = localStorage.getItem('kiosk-language') === 'en' ? 'en' : 'vi';

        function kioskText(viText) {
            return kioskLanguage === 'en' ? (kioskTranslations[viText] ?? viText) : viText;
        }

        function translateKiosk(root = document.body) {
            const textMap = kioskLanguage === 'en' ? kioskTranslations : reverseKioskTranslations;
            const placeholderMap = kioskLanguage === 'en' ? kioskPlaceholders : reverseKioskPlaceholders;
            const walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT);

            while (walker.nextNode()) {
                const node = walker.currentNode;
                if (['SCRIPT', 'STYLE'].includes(node.parentElement?.tagName)) continue;
                const value = node.nodeValue;
                const trimmed = value.trim();
                if (trimmed && textMap[trimmed]) node.nodeValue = value.replace(trimmed, textMap[trimmed]);
            }

            root.querySelectorAll('[placeholder]').forEach((element) => {
                if (placeholderMap[element.placeholder]) element.placeholder = placeholderMap[element.placeholder];
            });
            document.documentElement.lang = kioskLanguage;
        }

        const kioskLanguageSelect = document.getElementById('kioskLanguage');
        if (kioskLanguageSelect) {
            kioskLanguageSelect.value = kioskLanguage;
            kioskLanguageSelect.addEventListener('change', () => {
                kioskLanguage = kioskLanguageSelect.value === 'en' ? 'en' : 'vi';
                localStorage.setItem('kiosk-language', kioskLanguage);
                translateKiosk();
                setLookupMode(document.getElementById('kioskLookupMode')?.value);
                updateClock();
            });
        }

        const clockNode = document.getElementById('kioskClock');
        const dateNode = document.getElementById('kioskDate');

        function updateClock() {
            const now = new Date();
            const locale = kioskLanguage === 'en' ? 'en-GB' : 'vi-VN';
            clockNode.textContent = new Intl.DateTimeFormat(locale, { hour: '2-digit', minute: '2-digit' }).format(now);
            dateNode.textContent = new Intl.DateTimeFormat(locale, { day: '2-digit', month: '2-digit', year: 'numeric' }).format(now);
        }

        updateClock();
        setInterval(updateClock, 30000);

        GatehouseQrScanner.create({
            frame: '#kioskQrFrame',
            video: '#kioskQrVideo',
            input: '#kioskQrInput',
            form: '#kioskLookupForm',
            status: '#kioskQrStatus'
        });

        const extraPanel = document.querySelector('[data-kiosk-extra-panel]');
        const extraToggle = document.querySelector('[data-kiosk-extra-toggle]');
        extraToggle?.addEventListener('click', () => {
            const isOpen = extraPanel?.classList.toggle('is-open') ?? false;
            extraToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });

        const searchInput = document.getElementById('employeeSearch');
        const resultsBox = document.getElementById('employeeResults');
        const selectedHost = document.getElementById('selectedHost');
        const selectedDepartment = document.getElementById('selectedDepartment');
        const hostEmployeeId = document.getElementById('hostEmployeeId');
        let searchTimer = null;

        function renderEmployees(items) {
            resultsBox.innerHTML = '';

            if (items.length === 0) {
                resultsBox.innerHTML = `<div class="list-group-item text-secondary">${kioskText('Không tìm thấy nhân viên phù hợp.')}</div>`;
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
                    selectedHost.textContent = `${kioskLanguage === 'en' ? 'Selected' : 'Đã chọn'}: ${employee.name}`;
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
            selectedHost.textContent = kioskText('Chưa chọn nhân viên.');

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
        const lookupInput = document.getElementById('kioskQrInput');
        const lookupSubmitButton = document.getElementById('kioskLookupSubmit');
        const lookupModeInput = document.getElementById('kioskLookupMode');
        const lookupHeading = document.getElementById('kioskLookupHeading');
        const lookupHelp = document.getElementById('kioskLookupHelp');
        const lookupDivider = document.getElementById('kioskLookupDivider');
        const registerForm = document.getElementById('kioskRegisterForm');
        const visitorNameInput = document.getElementById('kioskVisitorName');
        const lookupModal = document.getElementById('kioskLookupModal');
        const modalTitle = document.getElementById('kioskLookupTitle');
        const modalMessage = document.getElementById('kioskModalMessage');
        const modalStatus = document.getElementById('kioskModalStatus');
        const modalAlert = document.getElementById('kioskModalAlert');
        const modalIcon = document.getElementById('kioskModalIcon');
        const modalDetails = document.getElementById('kioskModalDetails');
        const modalQr = document.getElementById('kioskModalQr');
        const modalConfirm = document.getElementById('kioskModalConfirm');
        let lookupSubmitTimer = null;
        let lookupInProgress = false;
        let lookupModalAutoCloseTimer = null;

        function currentLookupMode() {
            return lookupModeInput?.value === 'checkout' ? 'checkout' : 'checkin';
        }

        function lookupButtonHtml() {
            return currentLookupMode() === 'checkout'
                ? `<i class="bi bi-box-arrow-left me-1"></i>${kioskText('Check-out ngay')}`
                : `<i class="bi bi-box-arrow-in-right me-1"></i>${kioskText('Check-in ngay')}`;
        }

        function setLookupMode(mode) {
            const normalizedMode = mode === 'checkout' ? 'checkout' : 'checkin';

            if (lookupModeInput) {
                lookupModeInput.value = normalizedMode;
            }

            if (lookupForm) {
                lookupForm.action = normalizedMode === 'checkout'
                    ? lookupForm.dataset.checkoutUrl
                    : lookupForm.dataset.checkinUrl;
            }

            if (lookupInput) {
                lookupInput.placeholder = normalizedMode === 'checkout'
                    ? kioskLanguage === 'en' ? 'Enter visit code to check out' : 'Nhập mã lịch hẹn để check-out'
                    : kioskLanguage === 'en' ? 'Enter visit code or QR code' : 'Nhập mã lịch hẹn hoặc mã QR';
            }

            if (lookupSubmitButton) {
                lookupSubmitButton.innerHTML = lookupButtonHtml();
            }

            if (lookupHeading) {
                lookupHeading.textContent = normalizedMode === 'checkout'
                    ? kioskText('Check-out trực tiếp')
                    : kioskText('Check-in trực tiếp');
            }

            if (lookupHelp) {
                lookupHelp.textContent = normalizedMode === 'checkout'
                    ? kioskText('Quét QR hoặc nhập mã lịch hẹn của khách đang trong công ty để check-out ngay.')
                    : kioskText('Quét QR hoặc nhập mã lịch hẹn đã duyệt để hệ thống check-in ngay.');
            }

            if (lookupDivider) {
                lookupDivider.textContent = normalizedMode === 'checkout'
                    ? kioskText('Hoặc nhập mã check-out')
                    : kioskText('Hoặc nhập mã check-in');
            }
        }

        function submitKioskLookup() {
            if (!lookupForm || !lookupInput || lookupInProgress) return;

            const value = lookupInput.value.trim();
            if (!value) return;

            lookupForm.requestSubmit();
        }

        function looksLikeCompleteLookupCode(value) {
            const normalized = value.trim();

            return /^\d{8}$/.test(normalized)
                || /^(WK|VO|RP)-[A-Z0-9-]{6,}$/i.test(normalized);
        }

        function scheduleKioskLookup() {
            clearTimeout(lookupSubmitTimer);

            const value = lookupInput?.value.trim() || '';
            if (!looksLikeCompleteLookupCode(value)) return;

            lookupSubmitTimer = setTimeout(submitKioskLookup, 280);
        }

        if (lookupInput) {
            lookupInput.addEventListener('keydown', (event) => {
                if (event.key === 'Enter' && lookupInput.value.trim() !== '') {
                    event.preventDefault();
                    submitKioskLookup();
                }
            });
            lookupInput.addEventListener('input', scheduleKioskLookup);
            lookupInput.addEventListener('paste', () => setTimeout(scheduleKioskLookup, 0));
        }

        document.querySelectorAll('[data-kiosk-mode-action]').forEach((button) => {
            button.addEventListener('click', () => {
                document.querySelectorAll('[data-kiosk-mode-action]').forEach((item) => {
                    item.classList.toggle('is-active', item === button);
                });

                const mode = button.dataset.kioskModeAction;
                if (mode === 'register') {
                    registerForm?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    setTimeout(() => visitorNameInput?.focus(), 250);
                    return;
                }

                if (lookupInput) {
                    setLookupMode(mode);
                    document.querySelector('.kiosk-side-card')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    setTimeout(() => lookupInput.focus(), 250);
                }
            });
        });

        function openLookupModal() {
            clearTimeout(lookupModalAutoCloseTimer);
            lookupModal.classList.add('is-open');
            lookupModal.setAttribute('aria-hidden', 'false');
        }

        function closeLookupModal() {
            clearTimeout(lookupModalAutoCloseTimer);
            lookupModal.classList.remove('is-open');
            lookupModal.setAttribute('aria-hidden', 'true');
            if (lookupInput) {
                lookupInput.value = '';
                setTimeout(() => lookupInput.focus(), 150);
            }
        }

        function renderLookupModal(payload, isError = false) {
            const visit = payload.visit ?? null;
            const status = visit?.status ?? 'error';
            const statusLabel = visit?.status_label ?? 'Không tìm thấy';
            const mode = currentLookupMode();
            const message = payload.message ?? visit?.status_hint ?? 'Vui lòng kiểm tra lại mã vừa nhập.';
            const normalizedMessage = message.toLowerCase();
            const isSuccess = Boolean(payload.ok) && (
                mode === 'checkout'
                    ? normalizedMessage.includes('check-out thành công')
                    : normalizedMessage.includes('check-in thành công')
            );
            const alertType = isSuccess ? 'success' : (isError ? 'danger' : 'warning');
            const alertTitle = isSuccess
                ? (mode === 'checkout' ? 'Check-out thành công' : 'Check-in thành công')
                : (mode === 'checkout' ? 'Check-out thất bại' : 'Check-in thất bại');
            const alertIcon = isSuccess
                ? 'bi-check-circle'
                : (isError ? 'bi-exclamation-triangle' : 'bi-info-circle');
            const rows = visit ? [
                ['bi-calendar2-check', 'Mã lịch hẹn', visit.code],
                ['bi-person', 'Khách', visit.visitor_name],
                ['bi-buildings', 'Công ty', visit.visitor_company],
                ['bi-person-badge', 'Người tiếp khách', visit.host_name],
                ['bi-briefcase', 'Phòng ban', visit.department],
                ['bi-clock', 'Giờ hẹn', visit.scheduled_at],
            ] : [
                ['bi-info-circle', 'Kết quả', payload.message ?? 'Không tìm thấy thông tin phù hợp.'],
            ];

            modalTitle.textContent = isError && !visit
                ? 'Không tìm thấy lịch hẹn'
                : (currentLookupMode() === 'checkout' ? 'Kết quả check-out' : 'Kết quả check-in');
            modalMessage.textContent = message;
            modalStatus.className = `kiosk-modal-status is-${status}`;
            modalStatus.querySelector('span').textContent = statusLabel;
            modalAlert.className = `kiosk-modal-alert is-${alertType}`;
            modalAlert.querySelector('i').className = `bi ${alertIcon}`;
            modalAlert.querySelector('strong').textContent = alertTitle;
            modalAlert.querySelector('span').textContent = message;
            modalIcon.innerHTML = status === 'approved'
                ? '<i class="bi bi-check2-circle"></i>'
                : status === 'checked_in'
                    ? '<i class="bi bi-person-check"></i>'
                    : status === 'checked_out'
                        ? '<i class="bi bi-box-arrow-left"></i>'
                        : isError
                            ? '<i class="bi bi-exclamation-triangle"></i>'
                            : '<i class="bi bi-hourglass-split"></i>';

            modalDetails.innerHTML = rows.map(([icon, label, value]) => `
                <div class="kiosk-modal-row">
                    <i class="bi ${icon}"></i>
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
            lookupModalAutoCloseTimer = setTimeout(closeLookupModal, 15000);
        }

        lookupForm?.addEventListener('submit', async (event) => {
            event.preventDefault();
            if (lookupInProgress) return;

            const formData = new FormData(lookupForm);
            const submitButton = lookupForm.querySelector('button[type="submit"]');
            lookupInProgress = true;
            submitButton.disabled = true;
            submitButton.innerHTML = currentLookupMode() === 'checkout'
                ? '<span class="spinner-border spinner-border-sm me-2"></span>Đang check-out'
                : '<span class="spinner-border spinner-border-sm me-2"></span>Đang check-in';

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
                lookupInProgress = false;
                submitButton.disabled = false;
                submitButton.innerHTML = lookupButtonHtml();
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

        translateKiosk();
        setLookupMode(document.getElementById('kioskLookupMode')?.value);
    </script>
</body>
</html>

<!doctype html>
@php
    $pageSettings = $kioskSettings ?? [];
    $pagePrimaryColor = $pageSettings['kiosk.primary_color'] ?? '#146bd7';
    $pagePrimaryColor = preg_match('/^#[0-9a-fA-F]{6}$/', (string) $pagePrimaryColor) ? $pagePrimaryColor : '#146bd7';
    $pageSecondaryColor = $pageSettings['kiosk.secondary_color'] ?? '#0cb4d8';
    $pageSecondaryColor = preg_match('/^#[0-9a-fA-F]{6}$/', (string) $pageSecondaryColor) ? $pageSecondaryColor : '#0cb4d8';
    $pageBackgroundColor = $pageSettings['kiosk.background_color'] ?? '#f4f8fd';
    $pageBackgroundColor = preg_match('/^#[0-9a-fA-F]{6}$/', (string) $pageBackgroundColor) ? $pageBackgroundColor : '#f4f8fd';
    $pageSurfaceColor = $pageSettings['kiosk.surface_color'] ?? '#ffffff';
    $pageSurfaceColor = preg_match('/^#[0-9a-fA-F]{6}$/', (string) $pageSurfaceColor) ? $pageSurfaceColor : '#ffffff';
@endphp
<html lang="vi" style="--kiosk-primary: {{ $pagePrimaryColor }}; --kiosk-secondary: {{ $pageSecondaryColor }}; --kiosk-background: {{ $pageBackgroundColor }}; --kiosk-surface-color: {{ $pageSurfaceColor }};">
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
            --kiosk-blue: {{ $pagePrimaryColor }};
            --kiosk-cyan: {{ $pageSecondaryColor }};
            --kiosk-bg: {{ $pageBackgroundColor }};
            --kiosk-surface: {{ $pageSurfaceColor }};
        }

        * { box-sizing: border-box; }

        html, body {
            height: 100vh;
            overflow: hidden;
        }

        body {
            margin: 0;
            color: var(--kiosk-ink);
            background: var(--kiosk-bg);
            font-family: "Manrope", sans-serif;
        }

        .kiosk-shell {
            height: 100vh;
            display: grid;
            grid-template-rows: auto minmax(0, 1fr);
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
            grid-template-columns: minmax(0, 1120px);
            gap: 1.25rem;
            align-items: stretch;
            justify-content: center;
            min-height: 0;
        }

        .kiosk-card {
            border: 1px solid var(--kiosk-line);
            border-radius: 20px;
            background: color-mix(in srgb, var(--kiosk-surface) 96%, transparent);
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
            border-color: var(--kiosk-line);
            box-shadow: none;
            outline: none;
        }

        .kiosk-select-shell {
            position: relative;
            width: 100%;
        }

        .kiosk-select-shell .form-select {
            position: absolute;
            inset: 0;
            z-index: 0;
            opacity: 0;
            pointer-events: none;
        }

        .kiosk-select-button {
            position: relative;
            z-index: 1;
            width: 100%;
            min-height: 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .8rem;
            padding: .58rem .88rem;
            border: 1px solid var(--kiosk-line);
            border-radius: 12px;
            background: #fff;
            color: var(--kiosk-ink);
            font: inherit;
            font-size: .88rem;
            font-weight: 500;
            text-align: left;
        }

        .kiosk-input-wrap .kiosk-select-button {
            padding-left: 2.55rem;
        }
        .kiosk-select-button i {
            color: #7088a4;
            font-size: .95rem;
        }

        .kiosk-select-menu {
            position: absolute;
            top: calc(100% + .35rem);
            left: 0;
            right: 0;
            z-index: 80;
            display: none;
            max-height: 210px;
            overflow-y: auto;
            padding: .35rem;
            border: 1px solid #dbe7f4;
            border-radius: 14px;
            background: #fff;
        }

        .kiosk-select-shell.is-open .kiosk-select-menu {
            display: grid;
            gap: .15rem;
        }

        .kiosk-select-option {
            width: 100%;
            min-height: 34px;
            border: 0;
            border-radius: 10px;
            background: transparent;
            color: var(--kiosk-ink);
            font: inherit;
            font-size: .84rem;
            font-weight: 600;
            text-align: left;
            padding: .42rem .55rem;
        }
        .kiosk-select-option:hover,
        .kiosk-select-option.is-selected {
            background: #fff;
            color: var(--kiosk-ink);
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

        .kiosk-safety-policy {
            align-items: flex-start;
        }

        .kiosk-safety-list {
            margin: .45rem 0 0 1rem;
            padding: 0;
        }

        .kiosk-safety-list li {
            margin: .22rem 0;
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
            position: relative;
            z-index: 20;
            width: 100%;
            max-height: 150px;
            overflow-y: auto;
            border-radius: 14px;
            box-shadow: 0 18px 36px rgba(17, 39, 68, .14);
        }

        .premium-result-list:empty {
            display: none;
        }

        .premium-result-list .list-group-item {
            padding: .5rem .7rem;
            font-size: .76rem;
            line-height: 1.25;
        }

        .premium-result-list .list-group-item strong {
            font-size: .8rem;
        }

        .premium-result-list .list-group-item .text-secondary {
            font-size: .7rem;
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

        .kiosk-registration-qr {
            display: grid;
            place-items: center;
            gap: .45rem;
            padding: .75rem;
        }

        .kiosk-registration-qr svg {
            display: block;
            width: 92px;
            height: 92px;
        }

        .kiosk-registration-qr strong {
            color: var(--kiosk-ink);
            font-size: .76rem;
            font-weight: 700;
        }

        .kiosk-registration-qr span {
            margin: 0;
            color: #617893;
            font-size: .68rem;
            font-weight: 600;
            text-align: center;
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

       

            .kiosk-main {
                grid-template-columns: minmax(0, 1120px);
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

        /* Kiosk layout refinement after removing the side panel */
        .kiosk-header {
            width: min(1040px, 100%);
            margin-inline: auto;
        }

        .kiosk-main {
            grid-template-columns: minmax(0, 1040px);
        }

        .kiosk-form-card {
            padding: 1.35rem 1.5rem 1.45rem;
        }

        .kiosk-form-section:last-of-type > :not(.kiosk-section-title) {
            grid-column: 1 / -1;
        }

        .kiosk-policy .form-check-input {
            float: none;
            flex: 0 0 auto;
            margin: 0;
        }

        @media (max-width: 1100px) {
            .kiosk-header,
            .kiosk-main {
                width: 100%;
            }

            .kiosk-main {
                grid-template-columns: minmax(0, 1fr);
            }
        }
        .kiosk-last-inline {
            display: flex;
            align-items: center;
            gap: .75rem;
            margin-top: .8rem;
            padding: .72rem .85rem;
            border: 1px solid var(--kiosk-line);
            border-radius: 14px;
            background: #f8fbff;
        }

        .kiosk-last-inline > i {
            display: grid;
            width: 34px;
            height: 34px;
            flex: 0 0 34px;
            place-items: center;
            border-radius: 11px;
            color: var(--kiosk-blue);
            background: #fff;
        }

        .kiosk-last-inline div {
            min-width: 0;
            display: grid;
            gap: .08rem;
        }

        .kiosk-last-inline strong { font-size: .78rem; }
        .kiosk-last-inline span { color: var(--kiosk-muted); font-size: .73rem; }
        .kiosk-last-inline a {
            margin-left: auto;
            color: var(--kiosk-blue);
            font-size: .76rem;
            font-weight: 800;
            text-decoration: none;
            white-space: nowrap;
        }

        @media (max-width: 560px) {
            .kiosk-last-inline { align-items: flex-start; flex-wrap: wrap; }
            .kiosk-last-inline a { width: 100%; margin-left: 46px; }
        }
        /* Portrait touch kiosk mode */
        @media (orientation: portrait) and (min-width: 761px) {
            .kiosk-shell {
                width: min(900px, calc(100vw - 36px));
                gap: 1.15rem;
                padding: 1.25rem 0;
            }
            .kiosk-header, .kiosk-main { width: 100%; }
            .kiosk-logo { height: 58px; }
            .kiosk-tools .form-select { height: 52px; border-radius: 14px; }
            .kiosk-form-card { padding: 1.6rem 1.7rem 1.7rem; }
            .kiosk-title { margin-bottom: 1rem; }
            .kiosk-title h1 { font-size: 1.8rem; }
            .kiosk-title p { font-size: .95rem; }
            .kiosk-flat-form, .kiosk-form-section { gap: .9rem 1.2rem; }
            .kiosk-section-title { margin-top: .2rem; font-size: .82rem; }
            .kiosk-section-title i { width: 28px; height: 28px; }
            .kiosk-flat-form .form-label { margin-bottom: .42rem; font-size: .82rem; }
            .kiosk-flat-form .form-control, .kiosk-flat-form .form-select { min-height: 54px; border-radius: 14px; font-size: 1rem; }
            .kiosk-input-wrap .form-control, .kiosk-input-wrap .form-select { padding-left: 2.7rem; }
            .kiosk-input-wrap i { left: 1rem; font-size: 1rem; }
            .kiosk-extra-toggle { min-height: 50px; font-size: .9rem; }
            .kiosk-policy { min-height: 52px; padding: .7rem .8rem; font-size: .9rem; }
            .kiosk-policy .form-check-input { width: 1.3rem; height: 1.3rem; }
            .kiosk-submit { min-height: 56px; font-size: 1rem; }
        }
        .kiosk-policy-copy a {
            color: var(--kiosk-blue);
            font-weight: 800;
            text-decoration: underline;
            text-underline-offset: 2px;
        }    </style>
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
    $secondaryColor = $settings['kiosk.secondary_color'] ?? '#0cb4d8';
    $secondaryColor = preg_match('/^#[0-9a-fA-F]{6}$/', (string) $secondaryColor) ? $secondaryColor : '#0cb4d8';
    $backgroundColor = $settings['kiosk.background_color'] ?? '#f4f8fd';
    $backgroundColor = preg_match('/^#[0-9a-fA-F]{6}$/', (string) $backgroundColor) ? $backgroundColor : '#f4f8fd';
    $surfaceColor = $settings['kiosk.surface_color'] ?? '#ffffff';
    $surfaceColor = preg_match('/^#[0-9a-fA-F]{6}$/', (string) $surfaceColor) ? $surfaceColor : '#ffffff';
    $lastVisit = $lastKioskVisit ?? null;
    $selfRegistrationUrl = request()->getSchemeAndHttpHost().route('kiosk.register', [], false);
    $lastStatusLabels = [
        'pending' => 'Đang chờ phê duyệt',
        'approved' => 'Đã được duyệt',
        'checked_in' => 'Đã check-in',
        'checked_out' => 'Đã rời công ty',
        'rejected' => 'Bị từ chối',
        'cancelled' => 'Đã hủy',
    ];
@endphp
<body>
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
                </div>

                @php
                    $defaultCheckin = now();
                    $defaultCheckout = now()->addHours(4);
                @endphp
                <form class="kiosk-flat-form" id="kioskRegisterForm" method="post" action="{{ route('kiosk.checkin.manual') }}">
                    @csrf
                    <input type="hidden" name="registration_form" value="kiosk_v2">

                    <div class="kiosk-form-section">
                        <div class="kiosk-section-title"><i class="bi bi-person-fill"></i>1. Visitor Information</div>

                        <div>
                            <label class="form-label">Full name <span class="text-danger">*</span></label>
                            <div class="kiosk-input-wrap">
                                <i class="bi bi-person"></i>
                                <input class="form-control" id="kioskVisitorName" name="visitor_name" value="{{ old('visitor_name') }}" placeholder="Enter full name" required>
                            </div>
                        </div>
                        <div>
                            <label class="form-label">Phone number <span class="text-danger">*</span></label>
                            <div class="kiosk-input-wrap">
                                <i class="bi bi-telephone"></i>
                                <input class="form-control" name="visitor_phone" value="{{ old('visitor_phone') }}" placeholder="Enter phone number" required>
                            </div>
                        </div>
                        <div>
                            <label class="form-label">Email <span class="text-secondary">(Optional)</span></label>
                            <div class="kiosk-input-wrap">
                                <i class="bi bi-envelope"></i>
                                <input class="form-control" type="email" name="visitor_email" value="{{ old('visitor_email') }}" placeholder="example@email.com">
                            </div>
                        </div>
                        <div>
                            <label class="form-label">Company/Organization <span class="text-danger">*</span></label>
                            <div class="kiosk-input-wrap">
                                <i class="bi bi-building"></i>
                                <input class="form-control" name="visitor_company" value="{{ old('visitor_company') }}" placeholder="Enter company/organization name" required>
                            </div>
                        </div>
                        <div>
                            <label class="form-label">Citizen Identification Card/Passport <span class="text-danger">*</span></label>
                            <div class="kiosk-input-wrap">
                                <i class="bi bi-passport"></i>
                                <input class="form-control" name="visitor_identity_no" value="{{ old('visitor_identity_no') }}" placeholder="Enter Citizen ID Number/Passport" required>
                            </div>
                        </div>
                        <div>
                            <label class="form-label">Visitor ID card number <span class="text-danger">*</span></label>
                            <div class="kiosk-input-wrap">
                                <i class="bi bi-person-vcard"></i>
                                <select class="form-select" name="visitor_id_card_number" required>
                                    <option value="" disabled data-label-vi="Chọn thẻ khách" data-label-en="Select visitor card" @selected(! old('visitor_id_card_number'))>Chọn thẻ khách</option>
                                    @foreach (($visitorCardOptions ?? collect()) as $card)
                                        <option value="{{ $card['value'] }}" data-label-vi="{{ $card['label_vi'] }}" data-label-en="{{ $card['label_en'] }}" @selected((string) old('visitor_id_card_number') === (string) $card['value'])>{{ $card['label_en'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="kiosk-form-section">
                        <div class="kiosk-section-title"><i class="bi bi-calendar2-check-fill"></i>2. Check-in/out Information</div>

                        <div>
                            <label class="form-label">Check-in date <span class="text-danger">*</span></label>
                            <input class="form-control" id="kioskCheckinDate" type="date" name="checkin_date" value="{{ old('checkin_date', $defaultCheckin->toDateString()) }}" required>
                        </div>
                        <div>
                            <label class="form-label">Check-in time <span class="text-danger">*</span></label>
                            <input class="form-control" id="kioskCheckinTime" type="time" name="checkin_time" value="{{ old('checkin_time', $defaultCheckin->format('H:i')) }}" required>
                        </div>
                        <div>
                            <label class="form-label">Check-out date <span class="text-danger">*</span></label>
                            <input class="form-control" id="kioskCheckoutDate" type="date" name="checkout_date" value="{{ old('checkout_date', $defaultCheckout->toDateString()) }}" required>
                        </div>
                        <div>
                            <label class="form-label">Check-out time <span class="text-danger">*</span></label>
                            <input class="form-control" id="kioskCheckoutTime" type="time" name="checkout_time" value="{{ old('checkout_time', $defaultCheckout->format('H:i')) }}" required>
                        </div>
                    </div>

                    <div class="kiosk-form-section">
                        <div class="kiosk-section-title"><i class="bi bi-diagram-3-fill"></i>3. Meeting Information</div>

                        <div>
                            <label class="form-label">Meeting person <span class="text-danger">*</span></label>
                            <div class="kiosk-input-wrap">
                                <i class="bi bi-person-workspace"></i>
                                <input class="form-control" name="host_name" value="{{ old('host_name') }}" autocomplete="off" placeholder="Nhập người cần gặp" required>
                            </div>
                            <input name="host_employee_id" type="hidden" value="">
                        </div>
                        <div>
                            <label class="form-label">Department <span class="text-danger">*</span></label>
                            <select class="form-select" id="selectedDepartment" name="department_id" required>
                                <option value="" disabled @selected(! old('department_id'))>Select department</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}" data-label-vi="{{ $department->name_vi ?: $department->name }}" data-label-en="{{ $department->name_en ?: $department->name }}" @selected((string) old('department_id') === (string) $department->id)>{{ $department->name_en ?: $department->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="kiosk-form-section">
                        <div class="kiosk-section-title"><i class="bi bi-briefcase-fill"></i>4. Visiting Information</div>

                        <div>
                            <label class="form-label">Visiting purpose <span class="text-danger">*</span></label>
                            <select class="form-select" name="purpose" required>
                                <option value="" disabled data-label-vi="Chọn mục đích" data-label-en="Select purpose" {{ old('purpose') ? '' : 'selected' }}>Select purpose</option>
                                @foreach ([
                                    'Họp' => 'Meeting',
                                    'Tham quan' => 'Visit',
                                    'Đào tạo' => 'Training',
                                    'Đánh giá, kiểm tra' => 'Audit',
                                    'Phỏng vấn' => 'Interview',
                                    'Nhà thầu làm việc' => 'Contractor Work',
                                    'Bảo trì, sửa chữa' => 'Maintenance & Repair',
                                    'Giao nhận hàng hóa, chứng từ' => 'Delivery & Collection',
                                    'Khác' => 'Other',
                                ] as $purposeValue => $purposeLabel)
                                    <option value="{{ $purposeValue }}" data-label-vi="{{ $purposeValue }}" data-label-en="{{ $purposeLabel }}" @selected(old('purpose') === $purposeValue)>{{ $purposeLabel }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <label class="form-check kiosk-policy kiosk-safety-policy">
                        <input class="form-check-input" type="checkbox" name="safety_acknowledged" value="1" required>
                        <span class="form-check-label kiosk-policy-copy">
                            <span class="kiosk-safety-text" data-lang="en">
                                I acknowledge and agree to comply with the following safety and security requirements while on DHL premise:
                                <ul class="kiosk-safety-list">
                                    <li>Display visitor badge at all times.</li>
                                    <li>Remain within authorized areas only and be accompanied by an authorized escort when required.</li>
                                    <li>Photography and video recording are prohibited without prior authorization.</li>
                                    <li>No weapons, illegal substances, alcohol, hazardous materials, or prohibited items are allowed on site.</li>
                                    <li>Comply with all safety instructions, emergency procedures, and site regulations.</li>
                                    <li>Smoking is prohibited on the premises.</li>
                                    <li>Immediately report any incident, injury, unsafe condition, security concern, or emergency.</li>
                                    <li>In the event of an emergency alarm, proceed immediately to the designated Assembly Point and follow the instructions of DHL personnel.</li>
                                    <li>Return visitor badge before leaving the premises.</li>
                                </ul>
                            </span>
                            <span class="kiosk-safety-text" data-lang="vi" hidden>
                                T&#244;i x&#225;c nh&#7853;n &#273;&#227; &#273;&#7885;c, hi&#7875;u v&#224; &#273;&#7891;ng &#253; tu&#226;n th&#7911; c&#225;c quy &#273;&#7883;nh v&#7873; an to&#224;n v&#224; an ninh sau &#273;&#226;y trong th&#7901;i gian c&#243; m&#7863;t t&#7841;i c&#417; s&#7903; DHL:
                                <ul class="kiosk-safety-list">
                                    <li>Lu&#244;n &#273;eo th&#7867; kh&#225;ch trong th&#7901;i gian &#7903; t&#7841;i c&#417; s&#7903; DHL.</li>
                                    <li>Ch&#7881; ho&#7841;t &#273;&#7897;ng trong khu v&#7921;c &#273;&#432;&#7907;c ph&#233;p v&#224; c&#243; ng&#432;&#7901;i &#273;i c&#249;ng khi &#273;&#432;&#7907;c y&#234;u c&#7847;u.</li>
                                    <li>Kh&#244;ng ch&#7909;p &#7843;nh ho&#7863;c quay phim khi ch&#432;a &#273;&#432;&#7907;c cho ph&#233;p.</li>
                                    <li>Kh&#244;ng mang v&#361; kh&#237;, ch&#7845;t c&#7845;m, r&#432;&#7907;u bia, v&#7853;t li&#7879;u nguy hi&#7875;m ho&#7863;c v&#7853;t ph&#7849;m b&#7883; c&#7845;m v&#224;o c&#417; s&#7903;.</li>
                                    <li>Tu&#226;n th&#7911; m&#7885;i h&#432;&#7899;ng d&#7851;n v&#7873; an to&#224;n, an ninh v&#224; quy tr&#236;nh kh&#7849;n c&#7845;p.</li>
                                    <li>Kh&#244;ng h&#250;t thu&#7889;c trong khu&#244;n vi&#234;n DHL.</li>
                                    <li>B&#225;o c&#225;o ngay m&#7885;i s&#7921; c&#7889;, th&#432;&#417;ng t&#237;ch, t&#236;nh hu&#7889;ng m&#7845;t an to&#224;n, v&#7845;n &#273;&#7873; an ninh ho&#7863;c t&#236;nh hu&#7889;ng kh&#7849;n c&#7845;p.</li>
                                    <li>Khi c&#243; b&#225;o &#273;&#7897;ng kh&#7849;n c&#7845;p, nhanh ch&#243;ng di chuy&#7875;n &#273;&#7871;n &#272;i&#7875;m T&#7853;p K&#7871;t v&#224; tu&#226;n th&#7911; theo h&#432;&#7899;ng d&#7851;n c&#7911;a nh&#226;n vi&#234;n DHL.</li>
                                    <li>Ho&#224;n tr&#7843; th&#7867; kh&#225;ch tr&#432;&#7899;c khi r&#7901;i kh&#7887;i c&#417; s&#7903;.</li>
                                </ul>
                            </span>
                        </span>
                    </label>

                    <label class="form-check kiosk-policy">
                        <input class="form-check-input" type="checkbox" name="policy_accepted" value="1" required>
                        <span class="form-check-label kiosk-policy-copy">
                            <span class="kiosk-safety-text" data-lang="en">
                                I consent to DHL collecting and processing the personal data provided in this form for the purpose of visitor access and ensuring safety and security at its premises. DHL’s Privacy Notice is available at
                                <a href="{{ route('kiosk.privacy-notice') }}" target="_blank" rel="noopener noreferrer">DHL Privacy Notice</a>.
                            </span>
                            <span class="kiosk-safety-text" data-lang="vi" hidden>
                                Tôi đồng ý cho DHL thu thập, lưu trữ và xử lý thông tin cá nhân được cung cấp trong biểu mẫu này nhằm mục đích quản lý việc ra vào, bảo đảm an toàn, an ninh tại cơ sở. Thông báo Bảo mật của DHL tại
                                <a href="{{ route('kiosk.privacy-notice') }}" target="_blank" rel="noopener noreferrer">Privacy Notice - DHL - Global</a>.
                            </span>
                        </span>
                    </label>

                    <button class="btn kiosk-submit" type="submit">
                        <i class="bi bi-send-check me-2"></i>
                        Submit visit request
                    </button>
                </form>
                @if ($lastVisit)
                    <div class="kiosk-last-inline">
                        <i class="bi bi-card-checklist"></i>
                        <div>
                            <strong>Trạng thái yêu cầu gần nhất</strong>
                            <span>{{ $lastStatusLabels[$lastVisit->status] ?? 'Đang chờ xử lý' }} · {{ $lastVisit->code }}</span>
                        </div>
                        <a href="{{ route('kiosk.checkin.status', $lastVisit) }}">Xem chi tiết <i class="bi bi-chevron-right"></i></a>
                    </div>
                @endif
            </section>
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
            '3. Thông tin chuyến thăm': '3. Visit information',
            'Mục đích đến': 'Purpose of visit',
            'Chọn mục đích': 'Select a purpose',
            'Họp': 'Meeting',
            'Tham quan': 'Visit',
            'Đào tạo': 'Training',
            'Đánh giá, kiểm tra': 'Audit',
            'Phỏng vấn': 'Interview',
            'Nhà thầu làm việc': 'Contractor Work',
            'Bảo trì, sửa chữa': 'Maintenance & Repair',
            'Giao nhận hàng hóa, chứng từ': 'Delivery & Collection',
            'Khác': 'Other',
            'Tôi đồng ý tuân thủ quy định ra/vào và hướng dẫn của lễ tân/bảo vệ.': 'I agree to follow the access rules and instructions from reception/security.',
            'Gửi yêu cầu tiếp khách': 'Submit visit request',
            'Đăng ký nhanh': 'Quick registration',
            'Quét QR bằng điện thoại để mở form kiosk và nhập thông tin trước.': 'Scan the QR code with a phone to open the kiosk form and enter information in advance.',
            'Quét để đăng ký': 'Scan to register',
            'Khách có thể dùng điện thoại cá nhân để nhập thông tin nhanh hơn, hoặc lễ tân gửi link này trước cho khách.': 'Visitors can use their phone to enter information faster, or reception can send this link in advance.',
            'Check-in trực tiếp': 'Direct check-in',
            'Check-out trực tiếp': 'Direct check-out',
            'Quét QR hoặc nhập mã lịch hẹn đã duyệt để hệ thống check-in ngay.': 'Enter an approved visit code to check in.',
            'Nhập mã lịch hẹn đã duyệt để hệ thống check-in ngay.': 'Enter an approved visit code to check in.',
            'Quét QR hoặc nhập mã lịch hẹn của khách đang trong công ty để check-out ngay.': 'Enter the visit code of a visitor currently inside to check out.',
            'Nhập mã lịch hẹn của khách đang trong công ty để check-out ngay.': 'Enter the visit code of a visitor currently inside to check out.',
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

            // DHL kiosk form v2 translations
            '1. Thông tin khách': '1. Visitor Information',
            'Họ và tên': 'Full name',
            'Số điện thoại': 'Phone number',
            'Công ty/Tổ chức': 'Company/Organization',
            'CCCD/Hộ chiếu': 'Citizen Identification Card/Passport',
            'Số thẻ khách': 'Visitor ID card number',
            'Chọn thẻ khách': 'Select visitor card',
            '2. Thông tin check-in/out': '2. Check-in/out Information',
            'Ngày check-in': 'Check-in date',
            'Giờ check-in': 'Check-in time',
            'Ngày check-out': 'Check-out date',
            'Giờ check-out': 'Check-out time',
            '3. Thông tin gặp': '3. Meeting Information',
            'Người cần gặp': 'Meeting person',
            'Phòng ban': 'Department',
            '(Tùy chọn)': '(Optional)',
            'Chọn phòng ban': 'Select department',
            '4. Thông tin chuyến thăm': '4. Visiting Information',
            'Mục đích đến': 'Visiting purpose',
            'Chọn mục đích': 'Select purpose',
        };
        const kioskPlaceholders = {
            'Nhập họ và tên': 'Enter full name',
            'Nhập số điện thoại': 'Enter phone number',
            'Nhập tên công ty': 'Enter company name',
            'Nhập số CCCD': 'Enter identity number',
            'Nhập nơi cấp': 'Enter place of issue',
            'Tìm tên nhân viên': 'Search employee name',
            'Tự động sau khi chọn': 'Filled automatically after selection',
            'Nhập mã lịch hẹn': 'Enter visit code',
            'Nhập mã lịch hẹn để check-out': 'Enter visit code to check out',
            'Nhập họ và tên': 'Enter full name',
            'Nhập số điện thoại': 'Enter phone number',
            'Nhập tên công ty/tổ chức': 'Enter company/organization name',
            'Nhập số CCCD/Hộ chiếu': 'Enter Citizen ID Number/Passport',
            'Nhập số thẻ': 'Enter ID number',
            'Nhập người cần gặp': 'Enter meeting person',
        };
        const reverseKioskTranslations = Object.fromEntries(Object.entries(kioskTranslations).map(([vi, en]) => [en, vi]));
        const reverseKioskPlaceholders = Object.fromEntries(Object.entries(kioskPlaceholders).map(([vi, en]) => [en, vi]));
        let kioskLanguage = localStorage.getItem('kiosk-language-v2') === 'vi' ? 'vi' : 'en';

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

        function updateSafetyLanguage() {
            document.querySelectorAll('.kiosk-safety-text[data-lang]').forEach((node) => {
                node.hidden = node.dataset.lang !== kioskLanguage;
            });
        }

        function updateLocalizedOptions() {
            document.querySelectorAll('option[data-label-vi][data-label-en]').forEach((option) => {
                option.textContent = kioskLanguage === 'en' ? option.dataset.labelEn : option.dataset.labelVi;
                const select = option.closest('select');
                const shell = select?.closest('.kiosk-select-shell');
                const item = shell?.querySelector(`.kiosk-select-option[data-value="${CSS.escape(option.value)}"]`);
                if (item) item.textContent = option.textContent;
                if (select?.value === option.value) {
                    const label = shell?.querySelector('.kiosk-select-button span');
                    if (label) label.textContent = option.textContent;
                }
            });
        }

        function enhanceKioskSelects() {
            document.querySelectorAll('.kiosk-flat-form select.form-select').forEach((select) => {
                if (select.dataset.kioskSelectEnhanced === '1') return;
                select.dataset.kioskSelectEnhanced = '1';

                const shell = document.createElement('div');
                shell.className = 'kiosk-select-shell';
                select.parentNode.insertBefore(shell, select);
                shell.appendChild(select);

                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'kiosk-select-button';
                button.innerHTML = '<span></span><i class="bi bi-chevron-down"></i>';

                const menu = document.createElement('div');
                menu.className = 'kiosk-select-menu';

                const refresh = () => {
                    const selected = select.options[select.selectedIndex];
                    button.querySelector('span').textContent = selected?.textContent?.trim() || select.options[0]?.textContent?.trim() || 'Select';
                    menu.querySelectorAll('.kiosk-select-option').forEach((item) => {
                        item.classList.toggle('is-selected', item.dataset.value === select.value);
                    });
                };

                Array.from(select.options).forEach((option) => {
                    if (option.disabled && option.value === '') return;

                    const item = document.createElement('button');
                    item.type = 'button';
                    item.className = 'kiosk-select-option';
                    item.dataset.value = option.value;
                    item.textContent = option.textContent.trim();
                    item.addEventListener('click', () => {
                        select.value = option.value;
                        select.dispatchEvent(new Event('change', { bubbles: true }));
                        shell.classList.remove('is-open');
                        refresh();
                    });
                    menu.appendChild(item);
                });

                button.addEventListener('click', (event) => {
                    event.stopPropagation();
                    document.querySelectorAll('.kiosk-select-shell.is-open').forEach((openShell) => {
                        if (openShell !== shell) openShell.classList.remove('is-open');
                    });
                    shell.classList.toggle('is-open');
                });

                select.addEventListener('change', refresh);
                shell.appendChild(button);
                shell.appendChild(menu);
                refresh();
            });
        }

        document.addEventListener('click', () => {
            document.querySelectorAll('.kiosk-select-shell.is-open').forEach((shell) => shell.classList.remove('is-open'));
        });
        const kioskLanguageSelect = document.getElementById('kioskLanguage');
        if (kioskLanguageSelect) {
            kioskLanguageSelect.value = kioskLanguage;
            kioskLanguageSelect.addEventListener('change', () => {
                kioskLanguage = kioskLanguageSelect.value === 'en' ? 'en' : 'vi';
                localStorage.setItem('kiosk-language-v2', kioskLanguage);
                updateLocalizedOptions();
                enhanceKioskSelects();
        translateKiosk();
        updateSafetyLanguage();
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


        const visitTimeFields = [
            document.getElementById('kioskCheckinDate'),
            document.getElementById('kioskCheckinTime'),
            document.getElementById('kioskCheckoutDate'),
            document.getElementById('kioskCheckoutTime'),
        ].filter(Boolean);
        let visitTimesEdited = @json($errors->has('checkin_date') || $errors->has('checkin_time') || $errors->has('checkout_date') || $errors->has('checkout_time'));

        function localDateValue(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        function localTimeValue(date) {
            return `${String(date.getHours()).padStart(2, '0')}:${String(date.getMinutes()).padStart(2, '0')}`;
        }

        function fillRealtimeVisitSchedule(force = false) {
            if ((!force && visitTimesEdited) || visitTimeFields.length !== 4) return;
            const checkin = new Date();
            const checkout = new Date(checkin.getTime() + (4 * 60 * 60 * 1000));
            visitTimeFields[0].value = localDateValue(checkin);
            visitTimeFields[1].value = localTimeValue(checkin);
            visitTimeFields[2].value = localDateValue(checkout);
            visitTimeFields[3].value = localTimeValue(checkout);
        }

        fillRealtimeVisitSchedule();
        setInterval(() => fillRealtimeVisitSchedule(), 30000);
        visitTimeFields.forEach((field) => {
            field.addEventListener('focus', () => fillRealtimeVisitSchedule());
            field.addEventListener('input', () => { visitTimesEdited = true; });
        });
        const extraPanel = document.querySelector('[data-kiosk-extra-panel]');
        const extraToggle = document.querySelector('[data-kiosk-extra-toggle]');
        extraToggle?.addEventListener('click', () => {
            const isOpen = extraPanel?.classList.toggle('is-open') ?? false;
            extraToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });

        const lookupForm = document.getElementById('kioskLookupForm');
        const lookupInput = document.getElementById('kioskQrInput');
        const lookupSubmitButton = document.getElementById('kioskLookupSubmit');
        const lookupModeInput = document.getElementById('kioskLookupMode');
        const lookupHeading = document.getElementById('kioskLookupHeading');
        const lookupHelp = document.getElementById('kioskLookupHelp');
        const lookupDivider = document.getElementById('kioskLookupDivider');
        const registerForm = document.getElementById('kioskRegisterForm');
        registerForm?.addEventListener('submit', () => {
            fillRealtimeVisitSchedule(!visitTimesEdited);
        });
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
                    : kioskLanguage === 'en' ? 'Enter visit code' : 'Nhập mã lịch hẹn';
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
                    ? kioskText('Nhập mã lịch hẹn của khách đang trong công ty để check-out ngay.')
                    : kioskText('Nhập mã lịch hẹn đã duyệt để hệ thống check-in ngay.');
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

        updateLocalizedOptions();
        enhanceKioskSelects();
        translateKiosk();
        updateSafetyLanguage();
        setLookupMode(document.getElementById('kioskLookupMode')?.value);
    </script>
</body>
</html>


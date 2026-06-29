<!doctype html>
@php
    $settings = $kioskSettings ?? [];
    $primary = $settings['kiosk.primary_color'] ?? '#d40511';
    $secondary = $settings['kiosk.secondary_color'] ?? '#ffcc00';
@endphp
<html lang="en" style="--dhl-red: {{ $primary }}; --dhl-yellow: {{ $secondary }};">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Privacy Notice - DHL - Global</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; padding: 24px 14px; background: #fff; color: #000; font-family: "Manrope", sans-serif; }
        .privacy-card { width: min(100%, 620px); margin: 0 auto; overflow: hidden; border: 1px solid #e5e7eb; border-radius: 20px; background: #fff; box-shadow: 0 18px 44px rgba(0,0,0,.08); }
        .privacy-bar { height: 10px; background: var(--dhl-yellow); }
        .privacy-content { padding: 28px; }
        .privacy-icon { display: grid; width: 48px; height: 48px; place-items: center; border-radius: 14px; background: #fff4f4; color: var(--dhl-red); font-size: 22px; }
        h1 { margin: 18px 0 10px; font-size: 25px; line-height: 1.25; }
        p { margin: 0; font-size: 14px; line-height: 1.65; }
        .privacy-actions { display: grid; gap: 10px; margin-top: 24px; }
        .privacy-action { display: inline-flex; min-height: 46px; align-items: center; justify-content: center; gap: 8px; padding: 10px 16px; border-radius: 12px; font-weight: 700; text-decoration: none; }
        .privacy-primary { background: var(--dhl-red); color: #fff; }
        .privacy-back { border: 1px solid #d1d5db; color: #000; background: #fff; }
        @media (max-width: 480px) { .privacy-content { padding: 22px 18px; } }
    </style>
</head>
<body>
    <main class="privacy-card">
        <div class="privacy-bar"></div>
        <div class="privacy-content">
            <div class="privacy-icon"><i class="bi bi-shield-check"></i></div>
            <h1>Privacy Notice - DHL - Global</h1>
            <p>DHL is committed to protecting personal data. Read the official DHL Global Privacy Notice to understand how personal information is collected, processed, and protected.</p>
            <div class="privacy-actions">
                <a class="privacy-action privacy-primary" href="https://www.dhl.com/global-en/home/footer/privacy-notice.html" target="_blank" rel="noopener noreferrer">
                    <i class="bi bi-box-arrow-up-right"></i> Read DHL Privacy Notice
                </a>
                <a class="privacy-action privacy-back" href="{{ route('kiosk.register') }}">
                    <i class="bi bi-arrow-left"></i> Back to registration
                </a>
            </div>
        </div>
    </main>
</body>
</html>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kích hoạt bản quyền | Khách Mời VMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root{color-scheme:light;--blue:#1573e6;--blue-dark:#0d62c8;--blue-soft:#eef6ff;--text:#10233d;--muted:#6d8199;--line:#dce7f3;--page:#f5f8fc;--success:#13966b;--success-soft:#eaf8f2;--danger:#c93c4b}
        *{box-sizing:border-box}
        body{min-height:100vh;margin:0;background:var(--page);color:var(--text);font-family:Manrope,"Segoe UI",Arial,sans-serif}
        button,input,textarea{font:inherit}
        .license-page{min-height:100vh;display:grid;place-items:center;padding:28px 18px}
        .license-card{width:min(850px,100%);overflow:hidden;border:1px solid #dfe8f2;border-radius:10px;background:#fff;box-shadow:0 18px 45px rgba(22,48,82,.08)}
        .license-header{display:flex;align-items:flex-start;justify-content:space-between;gap:24px;padding:26px 30px 22px;border-bottom:1px solid #edf2f7}
        .license-heading{display:flex;align-items:flex-start;gap:15px;min-width:0}
        .license-heading-icon{width:52px;height:52px;display:grid;flex:0 0 52px;place-items:center;border:1px solid #d5e7fb;border-radius:8px;background:var(--blue-soft);color:var(--blue);font-size:1.55rem}
        .license-heading h1{margin:0;font-size:1.15rem;font-weight:700;letter-spacing:0}
        .license-heading p{max-width:520px;margin:6px 0 0;color:var(--muted);font-size:.82rem;line-height:1.55}
        .status-box{display:flex;align-items:center;gap:10px;flex:0 0 auto;padding:11px 14px;border-radius:8px;background:#fff5e8;color:#a86612}
        .status-box.active{background:var(--success-soft);color:var(--success)}
        .status-icon{width:25px;height:25px;display:grid;place-items:center;border-radius:50%;background:currentColor;color:#fff;font-size:.75rem}
        .status-icon i{color:#fff}
        .status-copy{display:grid;gap:1px}
        .status-copy small{font-size:.66rem;color:#72869c}
        .status-copy strong{font-size:.77rem;font-weight:700}
        .license-form{padding:4px 30px 26px}
        .step{padding:22px 0}
        .step+.step{border-top:1px solid #edf2f7}
        .step-title{display:flex;align-items:center;gap:10px;margin:0 0 17px;font-size:.88rem;font-weight:700}
        .step-number{width:26px;height:26px;display:grid;flex:0 0 26px;place-items:center;border-radius:50%;background:var(--blue);color:#fff;font-size:.75rem;font-weight:700;box-shadow:0 5px 12px rgba(21,115,230,.2)}
        .field{display:grid;gap:7px;margin-left:36px}
        .field+.field{margin-top:14px}
        .field label{font-size:.73rem;font-weight:600;color:#405772}
        .license-info-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;margin-left:36px;margin-top:14px}
        .license-info-item{min-height:58px;padding:11px 13px;border:1px solid #e0e7ef;border-radius:8px;background:#fafbfd}
        .license-info-item span{display:block;color:#6d8199;font-size:.68rem;font-weight:600}
        .license-info-item strong{display:block;margin-top:5px;color:#203a57;font-size:.8rem;font-weight:600;line-height:1.35}
        .license-info-item.active strong{color:var(--success)}
        .license-info-item.warning strong{color:#a86612}
        .license-info-item.expired strong{color:var(--danger)}
        .machine-row{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:10px;padding:7px;border:1px solid #d7e5f5;border-radius:8px;background:#f8fbff}
        .machine-code{display:flex;align-items:center;min-width:0;padding:0 7px;color:var(--blue);font-family:Consolas,"Courier New",monospace;font-size:.84rem;font-weight:600;word-break:break-all}
        .input-display{width:100%;min-height:42px;padding:11px 13px;border:1px solid #e0e7ef;border-radius:8px;background:#fafbfd;color:#657a92;font-size:.8rem}
        .copy-button,.secondary-button,.primary-button,.tab-button,.choose-button{border-radius:7px;transition:border-color .15s ease,background .15s ease,color .15s ease,transform .15s ease}
        .copy-button{min-height:38px;padding:0 14px;border:1px solid #d8e7f8;background:#fff;color:var(--blue);font-size:.75rem;font-weight:600}
        .copy-button:hover{border-color:#9fc6f4;background:#f1f7ff}
        .license-tabs{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin:0 0 12px 36px}
        .tab-button{min-height:40px;border:1px solid #dce5ef;background:#fff;color:#60758e;font-size:.77rem;font-weight:600}
        .tab-button.active{border-color:#75adf0;background:#f8fbff;color:var(--blue);box-shadow:0 0 0 2px rgba(21,115,230,.06)}
        .tab-button i{margin-right:6px}
        .upload-panel{margin-left:36px}
        .drop-zone{min-height:188px;display:grid;place-items:center;padding:24px;border:1.5px dashed #9fc7f7;border-radius:8px;background:#fbfdff;text-align:center;cursor:pointer;transition:border-color .15s ease,background .15s ease}
        .drop-zone:hover,.drop-zone.dragging{border-color:var(--blue);background:#f3f8ff}
        .drop-icon{font-size:2.15rem;color:var(--blue)}
        .drop-zone p{margin:8px 0 10px;color:#405772;font-size:.82rem}
        .drop-zone .or{display:block;margin:-3px 0 10px;color:#8496aa;font-size:.72rem}
        .choose-button{display:inline-flex;align-items:center;gap:6px;min-height:34px;padding:0 13px;border:0;background:var(--blue);color:#fff;font-size:.75rem;font-weight:600;box-shadow:0 6px 14px rgba(21,115,230,.18)}
        .file-hint{display:block;margin-top:10px;color:#8496aa;font-size:.69rem}
        .file-name{display:none;margin-top:12px;color:var(--success);font-size:.76rem;font-weight:600}
        .file-name.visible{display:block}
        .paste-panel{display:none;margin-left:36px}
        .paste-panel.active{display:block}
        .paste-panel textarea{width:100%;min-height:188px;resize:vertical;padding:13px;border:1px solid #cfdff1;border-radius:8px;outline:0;color:#29425f;background:#fbfdff;font-family:Consolas,"Courier New",monospace;font-size:.76rem;line-height:1.5}
        .paste-panel textarea:focus{border-color:#75adf0;box-shadow:0 0 0 3px rgba(21,115,230,.08)}
        .confirm-note{display:flex;align-items:flex-start;gap:9px;margin-left:36px;padding:12px 13px;border:1px solid #d7e8fa;border-radius:8px;background:#f5faff;color:#60758e;font-size:.73rem;line-height:1.5}
        .confirm-note i{margin-top:1px;color:var(--blue)}
        .messages{display:grid;gap:9px;padding:18px 30px 0}
        .message{padding:11px 13px;border:1px solid;border-radius:8px;font-size:.78rem}
        .message.success{border-color:#b9e7d5;background:#effaf5;color:#087451}
        .message.error{border-color:#f1c6cb;background:#fff4f5;color:var(--danger)}
        .license-footer{display:flex;align-items:center;justify-content:space-between;gap:12px;padding-top:18px}
        .secondary-button,.primary-button{display:inline-flex;align-items:center;justify-content:center;gap:7px;min-height:40px;padding:0 16px;font-size:.78rem;font-weight:600}
        .secondary-button{border:1px solid #dce5ef;background:#fff;color:#50677f;text-decoration:none}
        .secondary-button:hover{background:#f7f9fc}
        .primary-button{border:0;background:var(--blue);color:#fff;box-shadow:0 8px 18px rgba(21,115,230,.2)}
        .primary-button:hover{background:var(--blue-dark);transform:translateY(-1px)}
        .primary-button:disabled{cursor:not-allowed;opacity:.58;transform:none}
        @media(max-width:760px){.license-info-grid{grid-template-columns:1fr}}
        @media(max-width:650px){.license-page{padding:12px}.license-header{flex-direction:column;padding:21px}.status-box{width:100%}.license-form{padding:4px 21px 21px}.messages{padding:16px 21px 0}.field,.license-info-grid,.license-tabs,.upload-panel,.paste-panel,.confirm-note{margin-left:0}.machine-row{grid-template-columns:1fr}.license-tabs{grid-template-columns:1fr}.license-footer{align-items:stretch;flex-direction:column-reverse}.license-footer button{width:100%}}
    </style>
</head>
<body>
    <main class="license-page">
        <section class="license-card">
            <header class="license-header">
                <div class="license-heading">
                    <span class="license-heading-icon"><i class="bi bi-key"></i></span>
                    <div>
                        <h1>Kích hoạt bản quyền</h1>
                        <p>Gửi mã máy chủ cho bên cung cấp để được cấp file license. Sau đó nhập file license vào bên dưới để kích hoạt.</p>
                    </div>
                </div>
                @php
                    $isTrial = ($licenseStatus['status'] ?? null) === 'trial';
                    $statusText = $isTrial ? 'Dùng thử' : ($licenseStatus['valid'] ? 'Đã kích hoạt' : 'Chưa kích hoạt');
                    if (($licenseStatus['status'] ?? null) === 'trial_expired') {
                        $statusText = 'Hết hạn dùng thử';
                    }
                @endphp
                <div class="status-box {{ $licenseStatus['valid'] ? 'active' : '' }}">
                    <span class="status-icon"><i class="bi {{ $licenseStatus['valid'] ? 'bi-check-lg' : 'bi-lock-fill' }}"></i></span>
                    <span class="status-copy">
                        <small>Trạng thái</small>
                        <strong>{{ $statusText }}</strong>
                    </span>
                </div>
            </header>

            @if (session('status') || session('error') || $errors->any())
                <div class="messages">
                    @if (session('status'))
                        <div class="message success"><i class="bi bi-check-circle me-1"></i>{{ session('status') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="message error"><i class="bi bi-exclamation-circle me-1"></i>{{ session('error') }}</div>
                    @endif
                    @if ($errors->any())
                        <div class="message error"><i class="bi bi-exclamation-circle me-1"></i>{{ $errors->first() }}</div>
                    @endif
                </div>
            @endif

            <form class="license-form" method="post" action="{{ route('license.store') }}" enctype="multipart/form-data" data-license-form>
                @csrf

                <section class="step">
                    @php
                        $licensePayload = is_array($licenseStatus['payload'] ?? null) ? $licenseStatus['payload'] : [];
                        $isTrial = ($licenseStatus['status'] ?? null) === 'trial';
                        $isTrialLike = in_array(($licenseStatus['status'] ?? null), ['trial', 'trial_expired'], true);
                        $issuedAt = is_string($licensePayload['issued_at'] ?? null) ? $licensePayload['issued_at'] : null;
                        $expiresAt = is_string($licenseStatus['expires_at'] ?? null) ? $licenseStatus['expires_at'] : null;
                        if ($isTrialLike) {
                            $issuedAt = is_string($licenseStatus['trial_started_at'] ?? null) ? $licenseStatus['trial_started_at'] : null;
                            $expiresAt = is_string($licenseStatus['trial_ends_at'] ?? null) ? $licenseStatus['trial_ends_at'] : null;
                        }
                        $issuedLabel = 'Chưa có thông tin';
                        if ($issuedAt) {
                            try {
                                $issuedLabel = \Carbon\CarbonImmutable::parse($issuedAt)->timezone(config('app.timezone'))->format('d/m/Y');
                            } catch (\Throwable) {
                                $issuedLabel = $issuedAt;
                            }
                        }
                        $expiryLabel = 'Chưa có thông tin';
                        $remainingLabel = 'Chưa kích hoạt';
                        $remainingClass = '';
                        if ($licenseStatus['valid'] || $isTrialLike) {
                            if ($expiresAt) {
                                try {
                                    $expiryDate = \Carbon\CarbonImmutable::parse($expiresAt)->timezone(config('app.timezone'))->endOfDay();
                                    $remainingDays = (int) floor(now()->startOfDay()->diffInDays($expiryDate, false));
                                    $expiryLabel = $expiryDate->format('d/m/Y');
                                    if ($remainingDays < 0) {
                                        $remainingLabel = 'Đã hết hạn';
                                        $remainingClass = 'expired';
                                    } elseif ($remainingDays === 0) {
                                        $remainingLabel = 'Hết hạn hôm nay';
                                        $remainingClass = 'warning';
                                    } else {
                                        $remainingLabel = 'Còn '.$remainingDays.' ngày';
                                        $remainingClass = $remainingDays <= 30 ? 'warning' : 'active';
                                    }
                                } catch (\Throwable) {
                                    $expiryLabel = $expiresAt;
                                    $remainingLabel = 'Đã kích hoạt';
                                    $remainingClass = 'active';
                                }
                            } elseif ($isTrial) {
                                $expiryLabel = 'Kết thúc dùng thử';
                                $remainingLabel = 'Dùng thử';
                                $remainingClass = 'warning';
                            } else {
                                $expiryLabel = 'Bản quyền vĩnh viễn';
                                $remainingLabel = 'Không giới hạn';
                                $remainingClass = 'active';
                            }
                        }
                    @endphp
                    <h2 class="step-title"><span class="step-number">1</span>Thông tin máy chủ</h2>
                    <div class="field">
                        <label>Mã máy chủ (Machine ID)</label>
                        <div class="machine-row">
                            <span class="machine-code" id="deviceCode">{{ $licenseStatus['device_id'] }}</span>
                            <button class="copy-button" type="button" data-copy-device>
                                <i class="bi bi-copy"></i> Sao chép
                            </button>
                        </div>
                    </div>
                    <div class="field">
                        <label>Khách hàng</label>
                        <div class="input-display">
                            {{ $isTrialLike ? 'Dùng thử 15 ngày' : ($licenseStatus['customer'] ?: 'Chưa có thông tin khách hàng trong file license') }}
                        </div>
                    </div>
                    <div class="license-info-grid">
                        <div class="license-info-item">
                            <span>Ngày cấp</span>
                            <strong>{{ $issuedLabel }}</strong>
                        </div>
                        <div class="license-info-item">
                            <span>Ngày hết hạn</span>
                            <strong>{{ $expiryLabel }}</strong>
                        </div>
                        <div class="license-info-item {{ $remainingClass }}">
                            <span>Thời hạn còn lại</span>
                            <strong>{{ $remainingLabel }}</strong>
                        </div>
                    </div>
                </section>

                <section class="step">
                    <h2 class="step-title"><span class="step-number">2</span>Nhập license</h2>
                    <div class="license-tabs" role="tablist">
                        <button class="tab-button active" type="button" data-license-tab="file"><i class="bi bi-upload"></i>Tải file license</button>
                        <button class="tab-button" type="button" data-license-tab="paste"><i class="bi bi-code-slash"></i>Dán nội dung license</button>
                    </div>

                    <div class="upload-panel" data-license-panel="file">
                        <label class="drop-zone" for="license_file" data-drop-zone>
                            <span>
                                <i class="bi bi-cloud-arrow-up drop-icon"></i>
                                <p>Kéo và thả file license vào đây</p>
                                <span class="or">hoặc</span>
                                <span class="choose-button"><i class="bi bi-folder2-open"></i>Chọn file từ máy tính</span>
                                <span class="file-hint">Định dạng hỗ trợ: .license, .lic, .json</span>
                                <span class="file-name" data-file-name></span>
                            </span>
                        </label>
                        <input id="license_file" name="license_file" type="file" accept=".license,.lic,.json,application/json" hidden>
                    </div>

                    <div class="paste-panel" data-license-panel="paste">
                        <textarea id="license_text" name="license_text" placeholder="Dán toàn bộ nội dung file license vào đây...">{{ old('license_text') }}</textarea>
                    </div>
                </section>

                <section class="step">
                    <h2 class="step-title"><span class="step-number">3</span>Xác nhận</h2>
                    <div class="confirm-note">
                        <i class="bi bi-info-circle"></i>
                        <span>Sau khi kích hoạt, hệ thống sẽ tự động kiểm tra tính hợp lệ, mã máy chủ và thời hạn của license. Có thể tải license mới lên để gia hạn hoặc thay thế license hiện tại.</span>
                    </div>
                    <footer class="license-footer">
                        <a class="secondary-button" href="{{ auth()->check() ? route('admin.dashboard') : route('login') }}">
                            <i class="bi bi-arrow-left"></i>{{ auth()->check() ? 'Về trang chủ' : 'Quay lại đăng nhập' }}
                        </a>
                        <button class="primary-button" type="submit"><i class="bi bi-shield-check"></i>Kích hoạt</button>
                    </footer>
                </section>
            </form>
        </section>
    </main>

    <script>
        const form = document.querySelector('[data-license-form]');
        const fileInput = document.getElementById('license_file');
        const textInput = document.getElementById('license_text');
        const dropZone = document.querySelector('[data-drop-zone]');
        const fileName = document.querySelector('[data-file-name]');

        document.querySelector('[data-copy-device]')?.addEventListener('click', async (event) => {
            const text = document.getElementById('deviceCode')?.textContent?.trim() || '';
            await navigator.clipboard?.writeText(text);
            const button = event.currentTarget;
            const original = button.innerHTML;
            button.innerHTML = '<i class="bi bi-check-lg"></i> Đã sao chép';
            setTimeout(() => button.innerHTML = original, 1600);
        });

        document.querySelectorAll('[data-license-tab]').forEach((button) => {
            button.addEventListener('click', () => {
                const mode = button.dataset.licenseTab;
                document.querySelectorAll('[data-license-tab]').forEach((tab) => tab.classList.toggle('active', tab === button));
                document.querySelector('[data-license-panel="file"]').style.display = mode === 'file' ? 'block' : 'none';
                document.querySelector('[data-license-panel="paste"]').classList.toggle('active', mode === 'paste');
            });
        });

        const showFile = (file) => {
            fileName.textContent = file ? `Đã chọn: ${file.name}` : '';
            fileName.classList.toggle('visible', Boolean(file));
        };

        fileInput?.addEventListener('change', () => showFile(fileInput.files?.[0]));

        ['dragenter', 'dragover'].forEach((name) => dropZone?.addEventListener(name, (event) => {
            event.preventDefault();
            dropZone.classList.add('dragging');
        }));
        ['dragleave', 'drop'].forEach((name) => dropZone?.addEventListener(name, (event) => {
            event.preventDefault();
            dropZone.classList.remove('dragging');
        }));
        dropZone?.addEventListener('drop', (event) => {
            const files = event.dataTransfer?.files;
            if (!files?.length) return;
            const transfer = new DataTransfer();
            transfer.items.add(files[0]);
            fileInput.files = transfer.files;
            showFile(files[0]);
        });
    </script>
</body>
</html>

<!doctype html>
@php
    $pageSettings = $kioskSettings ?? [];
    $pagePrimaryColor = $pageSettings['kiosk.primary_color'] ?? '#d40511';
    $pagePrimaryColor = preg_match('/^#[0-9a-fA-F]{6}$/', (string) $pagePrimaryColor) ? $pagePrimaryColor : '#d40511';
    $pageSecondaryColor = $pageSettings['kiosk.secondary_color'] ?? '#ffcc00';
    $pageSecondaryColor = preg_match('/^#[0-9a-fA-F]{6}$/', (string) $pageSecondaryColor) ? $pageSecondaryColor : '#ffcc00';
    $pageBackgroundColor = $pageSettings['kiosk.background_color'] ?? '#ffffff';
    $pageBackgroundColor = preg_match('/^#[0-9a-fA-F]{6}$/', (string) $pageBackgroundColor) ? $pageBackgroundColor : '#ffffff';
    $pageSurfaceColor = $pageSettings['kiosk.surface_color'] ?? '#ffffff';
    $pageSurfaceColor = preg_match('/^#[0-9a-fA-F]{6}$/', (string) $pageSurfaceColor) ? $pageSurfaceColor : '#ffffff';
@endphp
<html lang="vi" style="--kiosk-primary: {{ $pagePrimaryColor }}; --kiosk-secondary: {{ $pageSecondaryColor }}; --kiosk-background: {{ $pageBackgroundColor }}; --kiosk-surface-color: {{ $pageSurfaceColor }};">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng ký khách | VMS Kiosk</title>
    @php
        $settings = $kioskSettings ?? [];
        $faviconUrl = $settings['app.favicon_url'] ?? $settings['kiosk.customer_logo_url'] ?? $settings['kiosk.logo_url'] ?? $settings['admin.logo_url'] ?? null;
    @endphp
    @if ($faviconUrl)
        <link rel="icon" href="{{ $faviconUrl }}">
        <link rel="shortcut icon" href="{{ $faviconUrl }}">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --ink: #000000;
            --muted: #000000;
            --line: #dce8f5;
            --soft: #f5f9fe;
            --blue: {{ $pagePrimaryColor }};
            --cyan: {{ $pageSecondaryColor }};
            --page-bg: {{ $pageBackgroundColor }};
            --surface: {{ $pageSurfaceColor }};
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            color: var(--ink);
            background: var(--page-bg);
            font-family: "Manrope", system-ui, sans-serif;
        }

        .mobile-kiosk {
            width: min(100%, 560px);
            margin: 0 auto;
            padding: 18px 14px 28px;
        }

        .mk-card {
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: 22px;
            background: color-mix(in srgb, var(--surface) 96%, transparent);
            box-shadow: 0 18px 40px rgba(17, 39, 68, .07);
        }

        .mk-hero {
            padding: 20px 18px 16px;
            border-bottom: 1px solid #edf4fb;
        }

        .mk-hero h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -.02em;
        }

        .mk-hero p {
            margin: 6px 0 0;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.45;
        }

        .mk-alert {
            margin: 14px 18px 0;
            padding: 12px 13px;
            border-radius: 15px;
            font-size: 13px;
            line-height: 1.45;
        }

        .mk-alert.danger {
            border: 1px solid #fecaca;
            background: #fff7f7;
            color: #b91c1c;
        }

        .mk-alert.success {
            border: 1px solid #bbf7d0;
            background: #f0fdf4;
            color: #047857;
        }

        .mk-form {
            display: grid;
            gap: 16px;
            padding: 18px;
        }

        .mk-section {
            display: grid;
            gap: 11px;
        }

        .mk-section-title {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--blue);
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .mk-field {
            display: grid;
            gap: 6px;
        }

        .mk-field label {
            color: #000000;
            font-size: 13px;
            font-weight: 600;
        }

        .mk-input {
            position: relative;
        }

        .mk-input i {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: #7f94ae;
            font-size: 15px;
        }

        .mk-input input,
        .mk-input select {
            width: 100%;
            min-height: 50px;
            padding: 0 13px 0 40px;
            border: 1px solid #d7e5f3;
            border-radius: 15px;
            background: #fff;
            color: var(--ink);
            font: inherit;
            font-size: 15px;
            outline: none;
        }

        .mk-input input:focus,
        .mk-input select:focus {
            border-color: var(--line);
            box-shadow: none;
            outline: none;
        }

        .mk-extra {
            border: 1px solid #e7f0fa;
            border-radius: 17px;
            background: #fbfdff;
        }

        .mk-extra summary {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 13px 14px;
            color: #000000;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            list-style: none;
        }

        .mk-extra summary::-webkit-details-marker { display: none; }

        .mk-extra-body {
            display: grid;
            gap: 11px;
            padding: 0 14px 14px;
        }

        .mk-selected {
            min-height: 20px;
            color: var(--muted);
            font-size: 13px;
        }

        .mk-results {
            display: grid;
            gap: 7px;
        }

        .mk-result {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 10px 12px;
            border: 1px solid #e0ebf6;
            border-radius: 13px;
            background: #fff;
            color: var(--ink);
            text-align: left;
            font: inherit;
        }

        .mk-result strong {
            display: block;
            font-size: 14px;
            font-weight: 700;
        }

        .mk-result span {
            display: block;
            margin-top: 2px;
            color: var(--muted);
            font-size: 12px;
        }

        .mk-policy {
            display: flex;
            gap: 10px;
            align-items: flex-start;
            padding: 12px;
            border-radius: 15px;
            background: #ffffff;
            color: #000000;
            font-size: 13px;
            line-height: 1.45;
        }

        .mk-policy input {
            width: 18px;
            height: 18px;
            margin-top: 1px;
            flex: 0 0 auto;
        }

        .mk-policy a {
            color: var(--blue);
            font-weight: 700;
        }

        .mk-submit {
            width: 100%;
            min-height: 52px;
            border: 0;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--blue), var(--cyan));
            color: #fff;
            font: inherit;
            font-size: 16px;
            font-weight: 700;
            box-shadow: 0 14px 28px rgba(20, 107, 215, .18);
        }

        .mk-submit:disabled {
            opacity: .72;
        }

        .mk-footer {
            margin-top: 14px;
            color: var(--muted);
            font-size: 12px;
            text-align: center;
        }
    </style>
</head>
@php
    $hotline = trim((string) ($settings['kiosk.hotline'] ?? '1900 0000'));
    $primaryColor = $settings['kiosk.primary_color'] ?? '#d40511';
    $primaryColor = preg_match('/^#[0-9a-fA-F]{6}$/', (string) $primaryColor) ? $primaryColor : '#d40511';
    $secondaryColor = $settings['kiosk.secondary_color'] ?? '#ffcc00';
    $secondaryColor = preg_match('/^#[0-9a-fA-F]{6}$/', (string) $secondaryColor) ? $secondaryColor : '#ffcc00';
    $backgroundColor = $settings['kiosk.background_color'] ?? '#ffffff';
    $backgroundColor = preg_match('/^#[0-9a-fA-F]{6}$/', (string) $backgroundColor) ? $backgroundColor : '#ffffff';
    $surfaceColor = $settings['kiosk.surface_color'] ?? '#ffffff';
    $surfaceColor = preg_match('/^#[0-9a-fA-F]{6}$/', (string) $surfaceColor) ? $surfaceColor : '#ffffff';
    $noticeType = session('error') || $errors->any() ? 'danger' : (session('status') ? 'success' : null);
    $noticeMessage = session('error') ?? session('status') ?? ($errors->any() ? $errors->first() : null);
@endphp
<body>
    <main class="mobile-kiosk">

        <section class="mk-card">
            <div class="mk-hero">
                <h1>Đăng ký khách</h1>
                <p>Nhập thông tin trên điện thoại để lễ tân xử lý nhanh hơn khi bạn đến công ty.</p>
            </div>

            @if ($noticeMessage)
                <div class="mk-alert {{ $noticeType }}">{{ $noticeMessage }}</div>
            @endif

            <form class="mk-form" id="mobileKioskRegisterForm" method="post" action="{{ route('kiosk.checkin.manual') }}" data-disable-on-submit>
                @csrf
                <section class="mk-section">
                    <div class="mk-section-title"><i class="bi bi-person-fill"></i>Thông tin khách</div>

                    <div class="mk-field">
                        <label for="visitor_name">Họ và tên *</label>
                        <div class="mk-input">
                            <i class="bi bi-person"></i>
                            <input id="visitor_name" name="visitor_name" value="{{ old('visitor_name') }}" placeholder="Nhập họ và tên" required>
                        </div>
                    </div>

                    <div class="mk-field">
                        <label for="visitor_phone">Số điện thoại *</label>
                        <div class="mk-input">
                            <i class="bi bi-telephone"></i>
                            <input id="visitor_phone" name="visitor_phone" value="{{ old('visitor_phone') }}" placeholder="Nhập số điện thoại" required>
                        </div>
                    </div>

                    <div class="mk-field">
                        <label for="visitor_email">Email *</label>
                        <div class="mk-input">
                            <i class="bi bi-envelope"></i>
                            <input id="visitor_email" type="email" name="visitor_email" value="{{ old('visitor_email') }}" placeholder="example@email.com" required>
                        </div>
                    </div>

                    <div class="mk-field">
                        <label for="visitor_company">Công ty / Tổ chức *</label>
                        <div class="mk-input">
                            <i class="bi bi-building"></i>
                            <input id="visitor_company" name="visitor_company" value="{{ old('visitor_company') }}" placeholder="Nhập tên công ty" required>
                        </div>
                    </div>
                </section>

                <details class="mk-extra" {{ old('visitor_identity_no') || old('visitor_identity_issued_place') || old('visitor_identity_issued_date') || old('expected_checkout_time') ? 'open' : '' }}>
                    <summary>
                        <span><i class="bi bi-plus-circle"></i> Thông tin xác thực</span>
                        <i class="bi bi-chevron-down"></i>
                    </summary>
                    <div class="mk-extra-body">
                        <div class="mk-field">
                            <label for="visitor_identity_no">CCCD / Hộ chiếu</label>
                            <div class="mk-input">
                                <i class="bi bi-card-text"></i>
                                <input id="visitor_identity_no" name="visitor_identity_no" value="{{ old('visitor_identity_no') }}" placeholder="Nhập số giấy tờ">
                            </div>
                        </div>

                        <div class="mk-field">
                            <label for="visitor_identity_issued_place">Nơi cấp</label>
                            <div class="mk-input">
                                <i class="bi bi-geo-alt"></i>
                                <input id="visitor_identity_issued_place" name="visitor_identity_issued_place" value="{{ old('visitor_identity_issued_place') }}" placeholder="Nhập nơi cấp">
                            </div>
                        </div>

                        <div class="mk-field">
                            <label for="visitor_identity_issued_date">Ngày cấp</label>
                            <div class="mk-input">
                                <i class="bi bi-calendar3"></i>
                                <input id="visitor_identity_issued_date" type="date" name="visitor_identity_issued_date" value="{{ old('visitor_identity_issued_date') }}">
                            </div>
                        </div>

                        <div class="mk-field">
                            <label for="expected_checkout_time">Dự kiến rời đi</label>
                            <div class="mk-input">
                                <i class="bi bi-clock"></i>
                                <input id="expected_checkout_time" type="time" name="expected_checkout_time" value="{{ old('expected_checkout_time', now()->addHours(2)->format('H:i')) }}">
                            </div>
                        </div>
                    </div>
                </details>

                <section class="mk-section">
                    <div class="mk-section-title"><i class="bi bi-diagram-3-fill"></i>Người cần gặp</div>

                    <div class="mk-field">
                        <label for="employeeSearch">Tìm nhân viên *</label>
                        <div class="mk-input">
                            <i class="bi bi-search"></i>
                            <input id="employeeSearch" autocomplete="off" placeholder="Nhập tên nhân viên" data-search-url="{{ route('kiosk.employees.search') }}">
                        </div>
                        <input id="hostEmployeeId" name="host_employee_id" type="hidden" value="{{ old('host_employee_id') }}" required>
                        <div class="mk-selected" id="selectedHost">Chưa chọn nhân viên.</div>
                        <div class="mk-results" id="employeeResults"></div>
                    </div>

                    <div class="mk-field">
                        <label for="selectedDepartment">Phòng ban</label>
                        <div class="mk-input">
                            <i class="bi bi-hospital"></i>
                            <input id="selectedDepartment" placeholder="Tự động sau khi chọn" readonly>
                        </div>
                    </div>
                </section>

                <section class="mk-section">
                    <div class="mk-section-title"><i class="bi bi-briefcase-fill"></i>Thông tin chuyến thăm</div>

                    <div class="mk-field">
                        <label for="purpose">Mục đích đến *</label>
                        <div class="mk-input">
                            <i class="bi bi-bullseye"></i>
                            <select id="purpose" name="purpose" required>
                                <option value="" disabled {{ old('purpose') ? '' : 'selected' }}>Chọn mục đích</option>
                                @foreach (['Họp', 'Tham quan', 'Đào tạo', 'Đánh giá, kiểm tra', 'Phỏng vấn', 'Nhà thầu làm việc', 'Bảo trì, sửa chữa', 'Giao nhận hàng hóa, chứng từ', 'Khác'] as $purpose)
                                    <option value="{{ $purpose }}" @selected(old('purpose') === $purpose)>{{ $purpose }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </section>

                <label class="mk-policy">
                    <input type="checkbox" name="policy_accepted" value="1" required>
                    <span>
                        By submitting this form, you consent to the collection and processing of your personal data
                        for visitor access, safety, and security purposes. Please refer to our
                        <a href="{{ route('kiosk.privacy-notice') }}">Privacy Notice - DHL - Global</a>.
                    </span>
                </label>
                <button class="mk-submit" type="submit" data-loading-text="Đang gửi yêu cầu...">
                    <i class="bi bi-send-check"></i>
                    Gửi yêu cầu tiếp khách
                </button>
            </form>
        </section>

        <p class="mk-footer">Nếu cần hỗ trợ, vui lòng liên hệ lễ tân hoặc gọi {{ $hotline }}.</p>
    </main>

    <script>
        const employeeSearch = document.getElementById('employeeSearch');
        const employeeResults = document.getElementById('employeeResults');
        const hostEmployeeId = document.getElementById('hostEmployeeId');
        const selectedHost = document.getElementById('selectedHost');
        const selectedDepartment = document.getElementById('selectedDepartment');
        let employeeTimer = null;

        function clearEmployeeResults() {
            if (employeeResults) employeeResults.innerHTML = '';
        }

        function chooseEmployee(employee) {
            hostEmployeeId.value = employee.id;
            employeeSearch.value = employee.name;
            selectedHost.textContent = `${employee.name}${employee.position ? ' - ' + employee.position : ''}`;
            selectedDepartment.value = employee.department || '';
            clearEmployeeResults();
        }

        function renderEmployeeResults(items) {
            clearEmployeeResults();

            if (!items.length) {
                employeeResults.innerHTML = '<div class="mk-selected">Không tìm thấy nhân viên phù hợp.</div>';
                return;
            }

            items.forEach((employee) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'mk-result';
                button.innerHTML = `
                    <span>
                        <strong>${employee.name}</strong>
                        <span>${[employee.position, employee.department].filter(Boolean).join(' - ') || 'Nhân viên'}</span>
                    </span>
                    <i class="bi bi-chevron-right"></i>
                `;
                button.addEventListener('click', () => chooseEmployee(employee));
                employeeResults.appendChild(button);
            });
        }

        employeeSearch?.addEventListener('input', () => {
            clearTimeout(employeeTimer);
            hostEmployeeId.value = '';
            selectedDepartment.value = '';
            selectedHost.textContent = 'Chưa chọn nhân viên.';

            const term = employeeSearch.value.trim();
            if (term.length < 2) {
                clearEmployeeResults();
                return;
            }

            employeeTimer = setTimeout(async () => {
                const url = `${employeeSearch.dataset.searchUrl}?q=${encodeURIComponent(term)}`;
                try {
                    const response = await fetch(url, { headers: { Accept: 'application/json' } });
                    const payload = await response.json();
                    renderEmployeeResults(payload.data || []);
                } catch (error) {
                    employeeResults.innerHTML = '<div class="mk-selected">Không tìm được nhân viên. Vui lòng thử lại.</div>';
                }
            }, 220);
        });

        document.querySelectorAll('[data-disable-on-submit]').forEach((form) => {
            form.addEventListener('submit', () => {
                const button = form.querySelector('[type="submit"]');
                if (!button) return;

                button.disabled = true;
                button.innerHTML = `<span>${button.dataset.loadingText || 'Đang xử lý...'}</span>`;
            });
        });
    </script>
</body>
</html>

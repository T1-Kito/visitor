<!doctype html>
<html lang="vi">
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
            --ink: #0f2238;
            --muted: #6d829d;
            --line: #dce8f5;
            --soft: #f5f9fe;
            --blue: var(--kiosk-primary, #146bd7);
            --cyan: #0cb4d8;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            color: var(--ink);
            background:
                radial-gradient(circle at 10% -8%, rgba(12, 180, 216, .16), transparent 34%),
                linear-gradient(180deg, #ffffff 0%, #f3f8fd 100%);
            font-family: "Manrope", system-ui, sans-serif;
        }

        .mobile-kiosk {
            width: min(100%, 560px);
            margin: 0 auto;
            padding: 18px 14px 28px;
        }

        .mk-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 14px;
        }

        .mk-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .mk-logo {
            width: 92px;
            height: 48px;
            display: grid;
            place-items: center;
            flex: 0 0 auto;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--blue), var(--cyan));
            color: #fff;
            overflow: hidden;
        }

        .mk-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .mk-brand-title {
            min-width: 0;
        }

        .mk-brand-title strong {
            display: block;
            font-size: 15px;
            font-weight: 700;
            line-height: 1.2;
        }

        .mk-brand-title span {
            display: block;
            margin-top: 2px;
            color: var(--muted);
            font-size: 12px;
            line-height: 1.25;
        }

        .mk-help {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            flex: 0 0 auto;
            border: 1px solid var(--line);
            border-radius: 14px;
            background: #fff;
            color: var(--blue);
            text-decoration: none;
        }

        .mk-card {
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: 22px;
            background: rgba(255, 255, 255, .96);
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
            color: #203956;
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
            border-color: color-mix(in srgb, var(--blue) 60%, white);
            box-shadow: 0 0 0 4px color-mix(in srgb, var(--blue) 12%, transparent);
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
            color: #28506f;
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
            background: #f6faff;
            color: #395979;
            font-size: 13px;
            line-height: 1.45;
        }

        .mk-policy input {
            width: 18px;
            height: 18px;
            margin-top: 1px;
            flex: 0 0 auto;
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
    $companyName = trim((string) ($settings['kiosk.company_name'] ?? 'Công ty ABC'));
    $systemName = trim((string) ($settings['kiosk.system_name'] ?? 'VMS Kiosk'));
    $hotline = trim((string) ($settings['kiosk.hotline'] ?? '1900 0000'));
    $ownerLogoUrl = $settings['kiosk.owner_logo_url'] ?? ($settings['admin.logo_url'] ?? null);
    $customerLogoUrl = $settings['kiosk.customer_logo_url'] ?? ($settings['kiosk.logo_url'] ?? null);
    $logoUrl = $customerLogoUrl ?: $ownerLogoUrl;
    $primaryColor = $settings['kiosk.primary_color'] ?? '#146bd7';
    $primaryColor = preg_match('/^#[0-9a-fA-F]{6}$/', (string) $primaryColor) ? $primaryColor : '#146bd7';
    $noticeType = session('error') || $errors->any() ? 'danger' : (session('status') ? 'success' : null);
    $noticeMessage = session('error') ?? session('status') ?? ($errors->any() ? $errors->first() : null);
@endphp
<body style="--kiosk-primary: {{ $primaryColor }};">
    <main class="mobile-kiosk">
        <header class="mk-header">
            <div class="mk-brand">
                <div class="mk-logo">
                    @if ($logoUrl)
                        <img src="{{ $logoUrl }}" alt="{{ $companyName }}">
                    @else
                        <i class="bi bi-shield-check"></i>
                    @endif
                </div>
                <div class="mk-brand-title">
                    <strong>{{ $systemName }}</strong>
                    <span>{{ $companyName }}</span>
                </div>
            </div>
            <a class="mk-help" href="tel:{{ preg_replace('/\s+/', '', $hotline) }}" aria-label="Gọi hỗ trợ">
                <i class="bi bi-telephone"></i>
            </a>
        </header>

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
                                @foreach (['Họp', 'Giao hàng', 'Phỏng vấn', 'Tham quan', 'Khác'] as $purpose)
                                    <option value="{{ $purpose }}" @selected(old('purpose') === $purpose)>{{ $purpose }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </section>

                <label class="mk-policy">
                    <input type="checkbox" name="policy_accepted" value="1" required>
                    <span>Tôi đồng ý tuân thủ quy định ra/vào và hướng dẫn của lễ tân/bảo vệ.</span>
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

<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kiosk | Gatehouse Pro</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/admin-ui.css') }}" rel="stylesheet">
    <style>
        .kiosk-demo-grid {
            grid-template-columns: minmax(230px, 20%) minmax(560px, 56%) minmax(280px, 24%) !important;
            gap: 0.95rem !important;
        }

        .kiosk-demo-welcome .premium-steps {
            display: none !important;
        }

        .kiosk-demo-welcome .kiosk-fs-content {
            justify-content: space-between;
        }

        .kiosk-flow-strip {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.55rem;
            margin-bottom: 0.9rem;
        }

        .kiosk-flow-step {
            display: flex;
            align-items: center;
            gap: 0.55rem;
            min-height: 42px;
            padding: 0.55rem 0.7rem;
            border: 1px solid #dbe8f6;
            border-radius: 14px;
            background: #f8fbff;
            color: #526b87;
            font-size: 0.78rem;
            font-weight: 600;
        }

        .kiosk-flow-step span {
            width: 24px;
            height: 24px;
            display: grid;
            place-items: center;
            flex: 0 0 24px;
            border-radius: 999px;
            background: linear-gradient(135deg, var(--kiosk-primary, #146bd7), #0cb4d8);
            color: #fff;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .kiosk-demo-qr .premium-qr-scanner.compact {
            min-height: 190px;
        }

        @media (max-width: 1200px) {
            .kiosk-demo-grid {
                grid-template-columns: 1fr !important;
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
    $welcomeTitle = ($settings['kiosk.welcome_title'] ?? null) ?: "Chào mừng bạn đến {$companyName}";
    $welcomeDescription = $settings['kiosk.welcome_description'] ?? 'Vui lòng đăng ký thông tin hoặc check-in bằng QR để được hỗ trợ nhanh chóng.';
    $hotline = $settings['kiosk.hotline'] ?? '1900 0000';
    $workingHours = $settings['kiosk.working_hours'] ?? '07:30 - 18:00';
    $logoUrl = $settings['kiosk.logo_url'] ?? null;
    $backgroundUrl = $settings['kiosk.background_url'] ?? null;
    $primaryColor = $settings['kiosk.primary_color'] ?? '#146bd7';
    $primaryColor = preg_match('/^#[0-9a-fA-F]{6}$/', (string) $primaryColor) ? $primaryColor : '#146bd7';
@endphp
<body class="kiosk-premium-body kiosk-fullscreen-body" style="--kiosk-primary: {{ $primaryColor }}; @if ($backgroundUrl) --kiosk-bg-image: url('{{ $backgroundUrl }}'); @endif">
    <main class="kiosk-fullscreen-shell">
        @php
            $noticeType = session('error') || $errors->any() ? 'danger' : (session('status') ? 'success' : null);
            $noticeMessage = session('error') ?? session('status') ?? ($errors->any() ? $errors->first() : null);
        @endphp
        @if ($noticeMessage)
            <div class="kiosk-notice-layer" id="kioskNotice">
                <div class="kiosk-notice kiosk-notice-{{ $noticeType }}">
                    <div class="kiosk-notice-icon">
                        <i class="bi {{ $noticeType === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' }}"></i>
                    </div>
                    <div>
                        <strong>{{ $noticeType === 'success' ? 'Thao tác thành công' : 'Cần kiểm tra lại' }}</strong>
                        <p>{{ $noticeMessage }}</p>
                    </div>
                    <button type="button" class="kiosk-notice-close" aria-label="Đóng thông báo" onclick="document.getElementById('kioskNotice')?.remove()">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>
        @endif

        <section class="kiosk-fullscreen-grid kiosk-demo-grid">
            <aside class="kiosk-fs-welcome kiosk-demo-welcome">
                <div class="kiosk-fs-overlay"></div>
                <div class="kiosk-fs-content">
                    <div class="kiosk-fs-panel-head">
                        <div class="premium-brand kiosk-fs-brand">
                            @if ($logoUrl)
                                <img class="kiosk-fs-logo" src="{{ $logoUrl }}" alt="{{ $companyName }}">
                            @else
                                <div class="premium-brand-mark"><i class="bi bi-shield-check"></i></div>
                            @endif
                            <div>
                                <strong>{{ $systemName }}</strong>
                                <span>{{ $subtitle }}</span>
                            </div>
                        </div>

                        <div class="kiosk-fs-panel-tools">
                            <select class="form-select form-select-sm" aria-label="Chọn ngôn ngữ">
                                <option>Tiếng Việt</option>
                                <option>English</option>
                            </select>
                            <div class="kiosk-fs-clock">
                                <strong id="kioskClock">--:--</strong>
                                <span id="kioskDate">--</span>
                            </div>
                        </div>
                    </div>

                    <div class="kiosk-fs-copy">
                        <h2>{{ $welcomeTitle }}</h2>
                        <p>{{ $welcomeDescription }}</p>
                    </div>

                    <div class="premium-steps compact">
                        <div class="premium-step"><span>1</span><div><strong>Nhập thông tin</strong><small>Điền đầy đủ thông tin khách</small></div></div>
                        <div class="premium-step"><span>2</span><div><strong>Chọn người cần gặp</strong><small>Tìm kiếm và chọn nhân viên</small></div></div>
                        <div class="premium-step"><span>3</span><div><strong>Chờ duyệt / Check-in</strong><small>Nhận kết quả xử lý tại quầy</small></div></div>
                    </div>

                    <div class="premium-support-box compact">
                        <i class="bi bi-telephone-inbound"></i>
                        <div>
                            <span>Hỗ trợ lễ tân / bảo vệ</span>
                            <strong>{{ $hotline }}</strong>
                            <small>Giờ làm việc: {{ $workingHours }}</small>
                        </div>
                    </div>
                </div>
            </aside>

            <section class="kiosk-fs-form-card kiosk-demo-form">
                <div class="kiosk-card-title">
                    <div>
                        <h2>Đăng ký khách walk-in</h2>
                        <p>Nhập thông tin khách và người cần gặp để gửi yêu cầu phê duyệt.</p>
                    </div>
                    <span class="status-badge status-checked-in">Walk-in</span>
                </div>

                <form method="post" action="{{ route('kiosk.checkin.manual') }}">
                    @csrf
                    <div class="kiosk-form-group">
                        <h3>Thông tin khách</h3>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                <div class="kiosk-input-wrap"><i class="bi bi-person"></i><input class="form-control" name="visitor_name" value="{{ old('visitor_name') }}" placeholder="Nhập họ và tên" required></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                <div class="kiosk-input-wrap"><i class="bi bi-telephone"></i><input class="form-control" name="visitor_phone" value="{{ old('visitor_phone') }}" placeholder="Nhập số điện thoại" required></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email nếu có</label>
                                <div class="kiosk-input-wrap"><i class="bi bi-envelope"></i><input class="form-control" type="email" name="visitor_email" value="{{ old('visitor_email') }}" placeholder="email@company.com"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Công ty / tổ chức</label>
                                <div class="kiosk-input-wrap"><i class="bi bi-building"></i><input class="form-control" name="visitor_company" value="{{ old('visitor_company') }}" placeholder="Nhập tên công ty"></div>
                            </div>
                        </div>
                    </div>

                    <div class="kiosk-form-group">
                        <h3>Thông tin gặp</h3>
                        <div class="row g-3">
                            <div class="col-md-7">
                                <label class="form-label">Người cần gặp <span class="text-danger">*</span></label>
                                <div class="kiosk-input-wrap">
                                    <i class="bi bi-search"></i>
                                    <input class="form-control" id="employeeSearch" autocomplete="off" placeholder="Tìm tên nhân viên" data-search-url="{{ route('kiosk.employees.search') }}">
                                </div>
                                <input id="hostEmployeeId" name="host_employee_id" type="hidden" value="{{ old('host_employee_id') }}" required>
                                <div class="small text-secondary mt-2" id="selectedHost">Chưa chọn nhân viên.</div>
                                <div class="list-group premium-result-list mt-2" id="employeeResults"></div>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Phòng ban</label>
                                <input class="form-control" id="selectedDepartment" placeholder="Tự động sau khi chọn" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="kiosk-form-group">
                        <h3>Thông tin chuyến thăm</h3>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Mục đích đến <span class="text-danger">*</span></label>
                                <select class="form-select" name="purpose" required>
                                    <option value="" disabled {{ old('purpose') ? '' : 'selected' }}>Chọn mục đích</option>
                                    @foreach (['Họp', 'Giao hàng', 'Phỏng vấn', 'Tham quan', 'Khác'] as $purpose)
                                        <option value="{{ $purpose }}" {{ old('purpose') === $purpose ? 'selected' : '' }}>{{ $purpose }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Dự kiến rời đi</label>
                                <div class="kiosk-input-wrap"><i class="bi bi-clock"></i><input class="form-control" type="time" name="expected_checkout_time" value="{{ old('expected_checkout_time', now()->addHours(2)->format('H:i')) }}"></div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Ghi chú</label>
                                <textarea class="form-control" name="visitor_note" rows="2" placeholder="Ghi chú thêm nếu có">{{ old('visitor_note') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <label class="form-check premium-policy kiosk-three-policy">
                        <input class="form-check-input" type="checkbox" name="policy_accepted" value="1" required>
                        <span class="form-check-label">Tôi đồng ý tuân thủ quy định ra/vào và hướng dẫn của lễ tân/bảo vệ.</span>
                    </label>

                    <button class="btn btn-brand w-100 kiosk-three-submit" type="submit"><i class="bi bi-send-check me-2"></i>Gửi yêu cầu tiếp khách</button>
                </form>
            </section>

            <aside class="kiosk-fs-qr-card kiosk-demo-qr">
                <div class="kiosk-card-title compact">
                    <h2>Check-in bằng QR</h2>
                    <p>Quét mã QR trên giấy mời hoặc nhập mã lịch hẹn.</p>
                </div>

                <div class="premium-qr-scanner compact">
                    <div class="premium-qr-corners"></div>
                    <i class="bi bi-qr-code"></i>
                    <span>Đưa mã QR vào khung</span>
                </div>

                <form class="premium-qr-form compact" method="post" action="{{ route('kiosk.checkin.scan-qr') }}">
                    @csrf
                    <label class="form-label">Hoặc nhập mã</label>
                    <div class="kiosk-input-wrap"><i class="bi bi-upc-scan"></i><input class="form-control" name="qr_token" placeholder="Nhập mã lịch hẹn / mã QR"></div>
                    <button class="btn btn-brand w-100" type="submit"><i class="bi bi-search me-1"></i>Kiểm tra mã</button>
                </form>

                @php
                    $lastVisit = $lastKioskVisit ?? null;
                    $lastStatusLabels = [
                        'pending' => 'Đang chờ phê duyệt',
                        'approved' => 'Đã được duyệt',
                        'checked_in' => 'Đã check-in',
                        'checked_out' => 'Đã rời công ty',
                        'rejected' => 'Bị từ chối',
                        'cancelled' => 'Đã hủy',
                    ];
                    $lastNextStep = match ($lastVisit?->status) {
                        'pending' => 'Chờ người tiếp khách duyệt',
                        'approved' => 'Bấm kiểm tra để check-in',
                        'checked_in' => 'Làm theo hướng dẫn tại quầy',
                        'rejected' => 'Liên hệ lễ tân để được hỗ trợ',
                        'cancelled' => 'Tạo yêu cầu mới nếu cần',
                        default => 'Chờ lễ tân xác nhận',
                    };
                @endphp
                <div class="kiosk-status-preview">
                    <span class="status-badge {{ $lastVisit ? 'status-approved' : 'status-pending' }}">Trạng thái yêu cầu</span>
                    <h3>{{ $lastVisit ? 'Yêu cầu gần nhất' : 'Đang chờ thao tác' }}</h3>
                    <p>
                        @if ($lastVisit)
                            Đây là mã yêu cầu gần nhất trên kiosk này. Bạn có thể kiểm tra lại trạng thái bất cứ lúc nào.
                        @else
                            Sau khi gửi yêu cầu hoặc nhập mã QR, trạng thái xử lý sẽ hiển thị tại đây.
                        @endif
                    </p>
                    <div class="kiosk-status-lines">
                        <div><span>Mã lịch</span><strong>{{ $lastVisit?->code ?? '-' }}</strong></div>
                        <div><span>Khách</span><strong>{{ $lastVisit?->visitor?->full_name ?? '-' }}</strong></div>
                        <div><span>Trạng thái</span><strong>{{ $lastStatusLabels[$lastVisit?->status] ?? 'Chưa có dữ liệu' }}</strong></div>
                        <div><span>Tiếp theo</span><strong>{{ $lastNextStep }}</strong></div>
                    </div>
                    @if ($lastVisit)
                        <a class="btn btn-outline-primary w-100 mt-3" href="{{ route('kiosk.checkin.status', $lastVisit) }}">
                            <i class="bi bi-arrow-clockwise me-1"></i>
                            Kiểm tra lại trạng thái
                        </a>
                    @endif
                </div>
            </aside>
        </section>
    </main>

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

@extends('layouts.admin')

@section('title', 'Cài đặt máy in')
@section('page_title', 'Cài đặt máy in')
@section('page_subtitle', 'Kết nối Printer Bridge, chọn máy in nhiệt và in thử mã QR')

@section('content')
    @php
        $testQrSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
            ->size(220)
            ->margin(1)
            ->generate('TEST-QR-001');
    @endphp

    <div class="printer-page">
        <section class="printer-topline">
            <div>
                <span class="printer-kicker">Printer Bridge</span>
                <h1>Thiết lập in mã QR</h1>
                <p>Chọn máy in đang cắm trên máy lễ tân hoặc máy admin. Cấu hình này lưu trên Printer Bridge local, không cần sửa file thủ công.</p>
            </div>
            <a class="btn btn-light" href="{{ route('admin.settings.index') }}">
                <i class="bi bi-grid"></i>
                Tất cả cài đặt
            </a>
        </section>

        <section class="printer-health-card">
            <div class="printer-health-icon">
                <i class="bi bi-printer"></i>
            </div>
            <div class="printer-health-copy">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <h2>Kết nối máy in local</h2>
                    <span id="bridgeStatusBadge" class="status-badge status-pending">Chưa kiểm tra</span>
                </div>
                <p>Bridge mặc định chạy tại <code>http://127.0.0.1:9191</code>. Nếu máy lễ tân dùng port khác, nhập lại địa chỉ bên dưới.</p>
            </div>
            <div class="printer-health-form">
                <input id="bridgeUrl" class="form-control" value="{{ $defaultBridgeUrl }}" placeholder="http://127.0.0.1:9191">
                <button id="checkBridgeBtn" class="btn btn-brand" type="button">
                    <i class="bi bi-plug"></i>
                    Kiểm tra
                </button>
            </div>
        </section>

        <div id="printerMessage" class="printer-setup-message d-none"></div>

        <div class="row g-4">
            <div class="col-xl-8">
                <div class="gate-card printer-config-card">
                    <div class="gate-card-head">
                        <div>
                            <h2>Cấu hình in</h2>
                            <p>Ưu tiên dùng “Xem trước rồi in” khi chưa chắc driver hỗ trợ ESC/POS. Khi test ổn mới chuyển sang in nhiệt trực tiếp.</p>
                        </div>
                    </div>

                    <div class="printer-form-grid">
                        <div class="printer-field printer-field-wide">
                            <label class="form-label">Máy in</label>
                            <select id="printerName" class="form-select form-select-lg">
                                <option value="">Chưa kết nối Printer Bridge</option>
                            </select>
                            <small>Hệ thống sẽ tự tải danh sách máy in Windows. Máy in nhiệt thường có tên như XPrinter, POS, Receipt, Epson TM, Rongta, Gprinter.</small>
                        </div>

                        <div class="printer-field">
                            <label class="form-label">Khổ giấy</label>
                            <select id="paper" class="form-select form-select-lg">
                                <option value="80mm">80mm</option>
                                <option value="58mm">58mm</option>
                            </select>
                        </div>

                        <div class="printer-field">
                            <label class="form-label">Chế độ in</label>
                            <select id="mode" class="form-select form-select-lg">
                                <option value="preview">Xem trước rồi in</option>
                                <option value="escpos">In nhiệt trực tiếp ESC/POS</option>
                            </select>
                        </div>

                        <label class="form-check printer-toggle printer-field-wide">
                            <input id="openAfterPrint" class="form-check-input" type="checkbox" checked>
                            <span>
                                <strong>Mở phiếu sau khi tạo</strong>
                                <small>Dùng cho chế độ xem trước. Nếu chọn ESC/POS trực tiếp thì có thể tắt.</small>
                            </span>
                        </label>
                    </div>

                    <div class="printer-action-bar">
                        <button id="refreshPrintersBtn" class="btn btn-light" type="button">
                            <i class="bi bi-arrow-clockwise"></i>
                            Tải lại máy in
                        </button>
                        <button id="savePrinterBtn" class="btn btn-brand" type="button">
                            <i class="bi bi-save2"></i>
                            Lưu cấu hình
                        </button>
                        <button id="testPrintBtn" class="btn btn-outline-primary" type="button">
                            <i class="bi bi-printer"></i>
                            In thử QR
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="gate-card mb-4">
                    <div class="gate-card-head">
                        <div>
                            <h2>Trạng thái</h2>
                            <p>Thông tin lấy trực tiếp từ Printer Bridge trên máy hiện tại.</p>
                        </div>
                    </div>

                    <div class="printer-status-list">
                        <div>
                            <span>Bridge</span>
                            <strong id="bridgeState">Chưa kiểm tra</strong>
                        </div>
                        <div>
                            <span>Máy tính</span>
                            <strong id="bridgeHost">-</strong>
                        </div>
                        <div>
                            <span>Chế độ</span>
                            <strong id="bridgeMode">-</strong>
                        </div>
                        <div>
                            <span>Máy in</span>
                            <strong id="bridgePrinter">-</strong>
                        </div>
                    </div>
                </div>

                <div class="gate-card printer-guide-card">
                    <div class="gate-card-head">
                        <div>
                            <h2>Quy trình setup</h2>
                            <p>Làm một lần trên máy có cắm máy in.</p>
                        </div>
                    </div>

                    <ol class="printer-setup-steps">
                        <li><span>1</span><p>Chạy <code>printer-bridge/start-printer-bridge.ps1</code>.</p></li>
                        <li><span>2</span><p>Bấm <strong>Kiểm tra</strong> để kết nối bridge.</p></li>
                        <li><span>3</span><p>Chọn máy in nhiệt và khổ giấy.</p></li>
                        <li><span>4</span><p>Bấm <strong>Lưu cấu hình</strong> rồi <strong>In thử QR</strong>.</p></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (() => {
            const defaultBridgeUrl = @json($defaultBridgeUrl);
            const testQrSvg = @json((string) $testQrSvg);
            const nodes = {
                bridgeUrl: document.getElementById('bridgeUrl'),
                bridgeStatusBadge: document.getElementById('bridgeStatusBadge'),
                checkBridgeBtn: document.getElementById('checkBridgeBtn'),
                refreshPrintersBtn: document.getElementById('refreshPrintersBtn'),
                savePrinterBtn: document.getElementById('savePrinterBtn'),
                testPrintBtn: document.getElementById('testPrintBtn'),
                printerName: document.getElementById('printerName'),
                paper: document.getElementById('paper'),
                mode: document.getElementById('mode'),
                openAfterPrint: document.getElementById('openAfterPrint'),
                printerMessage: document.getElementById('printerMessage'),
                bridgeState: document.getElementById('bridgeState'),
                bridgeHost: document.getElementById('bridgeHost'),
                bridgeMode: document.getElementById('bridgeMode'),
                bridgePrinter: document.getElementById('bridgePrinter'),
            };

            nodes.bridgeUrl.value = localStorage.getItem('gatehouse_printer_bridge_url') || defaultBridgeUrl;

            function bridgeUrl(path = '') {
                return nodes.bridgeUrl.value.replace(/\/+$/, '') + path;
            }

            function setMessage(message, type = 'info') {
                nodes.printerMessage.className = `printer-setup-message printer-setup-${type}`;
                nodes.printerMessage.textContent = message;
            }

            function setBridgeState(ok, message) {
                nodes.bridgeStatusBadge.className = `status-badge ${ok ? 'status-approved' : 'status-rejected'}`;
                nodes.bridgeStatusBadge.textContent = ok ? 'Đã kết nối' : 'Mất kết nối';
                nodes.bridgeState.textContent = message;
            }

            function isLikelyThermal(printer) {
                const text = `${printer.Name || ''} ${printer.DriverName || ''} ${printer.PortName || ''}`.toLowerCase();
                return ['pos', 'thermal', 'receipt', 'xprinter', 'epson tm', 'rongta', 'gprinter', '58', '80'].some((keyword) => text.includes(keyword));
            }

            function renderPrinters(printers, selectedName = '') {
                nodes.printerName.innerHTML = '';

                if (!printers.length) {
                    nodes.printerName.innerHTML = '<option value="">Không tìm thấy máy in trên máy này</option>';
                    return;
                }

                printers
                    .sort((a, b) => Number(isLikelyThermal(b)) - Number(isLikelyThermal(a)))
                    .forEach((printer) => {
                        const option = document.createElement('option');
                        option.value = printer.Name;
                        option.textContent = `${printer.Name}${isLikelyThermal(printer) ? ' - có vẻ là máy in nhiệt' : ''}`;
                        option.selected = printer.Name === selectedName;
                        nodes.printerName.appendChild(option);
                    });
            }

            async function requestJson(path, options = {}) {
                const response = await fetch(bridgeUrl(path), {
                    headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
                    ...options,
                });
                const payload = await response.json().catch(() => ({}));
                if (!response.ok || payload.ok === false) {
                    throw new Error(payload.message || 'Không thể kết nối Printer Bridge.');
                }
                return payload;
            }

            async function loadConfig() {
                const payload = await requestJson('/config');
                const config = payload.config || {};
                nodes.paper.value = config.paper || '80mm';
                nodes.mode.value = config.mode || 'preview';
                nodes.openAfterPrint.checked = config.openAfterPrint !== false;
                nodes.bridgeMode.textContent = nodes.mode.options[nodes.mode.selectedIndex].textContent;
                nodes.bridgePrinter.textContent = config.printerName || 'Chưa chọn';
                return config;
            }

            async function loadPrinters(selectedName = '') {
                const payload = await requestJson('/printers');
                renderPrinters(payload.printers || [], selectedName);
            }

            async function checkBridge() {
                localStorage.setItem('gatehouse_printer_bridge_url', nodes.bridgeUrl.value);
                const health = await requestJson('/health');
                setBridgeState(true, 'Đang hoạt động');
                nodes.bridgeHost.textContent = health.hostname || '-';
                nodes.bridgeMode.textContent = health.mode || '-';
                nodes.bridgePrinter.textContent = health.printerName || 'Chưa chọn';
                const config = await loadConfig();
                await loadPrinters(config.printerName || '');
                setMessage('Đã kết nối Printer Bridge và tải danh sách máy in.', 'success');
            }

            async function savePrinter() {
                localStorage.setItem('gatehouse_printer_bridge_url', nodes.bridgeUrl.value);
                const payload = await requestJson('/config', {
                    method: 'POST',
                    body: JSON.stringify({
                        paper: nodes.paper.value,
                        mode: nodes.mode.value,
                        printerName: nodes.printerName.value,
                        openAfterPrint: nodes.openAfterPrint.checked,
                    }),
                });

                nodes.bridgeMode.textContent = nodes.mode.options[nodes.mode.selectedIndex].textContent;
                nodes.bridgePrinter.textContent = payload.config?.printerName || 'Chưa chọn';
                setMessage('Đã lưu cấu hình máy in trên Printer Bridge.', 'success');
            }

            async function testPrint() {
                await savePrinter();
                await requestJson('/print', {
                    method: 'POST',
                    body: JSON.stringify({
                        code: 'TEST-QR-001',
                        qrToken: 'TEST-QR-001',
                        qrSvg: testQrSvg,
                        visitorName: 'Khách demo',
                        visitorCompany: 'Gatehouse Pro',
                        hostName: 'Lễ tân',
                        scheduledAt: new Intl.DateTimeFormat('vi-VN', {
                            hour: '2-digit',
                            minute: '2-digit',
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric',
                        }).format(new Date()),
                        status: 'In thử',
                    }),
                });
                setMessage('Đã gửi lệnh in thử. Nếu dùng chế độ xem trước, bridge sẽ mở phiếu QR.', 'success');
            }

            nodes.checkBridgeBtn.addEventListener('click', () => {
                checkBridge().catch((error) => {
                    setBridgeState(false, 'Không kết nối được');
                    setMessage(`${error.message} Hãy chạy printer-bridge/start-printer-bridge.ps1 rồi thử lại.`, 'danger');
                });
            });
            nodes.refreshPrintersBtn.addEventListener('click', () => loadPrinters(nodes.printerName.value).catch((error) => setMessage(error.message, 'danger')));
            nodes.savePrinterBtn.addEventListener('click', () => savePrinter().catch((error) => setMessage(error.message, 'danger')));
            nodes.testPrintBtn.addEventListener('click', () => testPrint().catch((error) => setMessage(error.message, 'danger')));

            checkBridge().catch(() => {
                nodes.printerName.innerHTML = '<option value="">Chưa kết nối Printer Bridge</option>';
                nodes.bridgeStatusBadge.textContent = 'Chưa kết nối';
            });
        })();
    </script>
@endpush

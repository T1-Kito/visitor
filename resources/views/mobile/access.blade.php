@extends('layouts.mobile')

@section('title', $title)

@push('styles')
    <style>
        .m-camera-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin-top: 10px;
        }

        .m-camera-btn {
            min-height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: 1px solid #bfdbfe;
            border-radius: 999px;
            background: rgba(255, 255, 255, .9);
            color: #0f6eea;
            font: inherit;
            font-size: .86rem;
            padding: 0 14px;
            box-shadow: 0 10px 22px rgba(15, 111, 234, .12);
        }

        .m-camera-btn i {
            color: inherit !important;
            font-size: 1rem !important;
        }

        .m-scan-frame.in-frame-camera {
            min-height: 190px;
            gap: 8px;
            align-content: center;
        }

        .m-camera-sheet[hidden] {
            display: none !important;
        }

        .m-camera-sheet {
            position: fixed;
            inset: 0;
            z-index: 80;
            display: grid;
            place-items: center;
            padding: max(14px, env(safe-area-inset-top, 0px)) 14px max(14px, env(safe-area-inset-bottom, 0px));
            background: rgba(15, 23, 42, .58);
            backdrop-filter: blur(8px);
        }

        .m-camera-panel {
            width: min(100%, 420px);
            max-height: min(760px, calc(100dvh - 28px));
            display: grid;
            grid-template-rows: auto minmax(0, 1fr) auto;
            border-radius: 24px;
            background: #fff;
            box-shadow: 0 24px 70px rgba(15, 23, 42, .24);
            overflow: hidden;
        }

        .m-camera-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 16px;
            border-bottom: 1px solid #edf2f7;
        }

        .m-camera-head strong {
            font-size: 1rem;
            font-weight: 600;
        }

        .m-camera-head button {
            width: 38px;
            height: 38px;
            border: 1px solid #dbe8f7;
            border-radius: 14px;
            background: #fff;
            color: #365270;
        }

        .m-camera-view {
            position: relative;
            margin: 16px;
            border-radius: 20px;
            overflow: hidden;
            background: #0f172a;
            aspect-ratio: 1 / 1;
            max-height: min(58dvh, 420px);
        }

        .m-camera-view video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        #mobile-qr-reader {
            position: absolute;
            inset: 0;
        }

        #mobile-qr-reader video {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
        }

        #mobile-qr-reader__dashboard,
        #mobile-qr-reader__header_message {
            display: none !important;
        }

        .m-camera-frame {
            position: absolute;
            inset: 14%;
            border: 2px solid rgba(255, 255, 255, .92);
            border-radius: 18px;
            box-shadow: 0 0 0 999px rgba(15, 23, 42, .28);
        }

        .m-camera-frame::after {
            content: "";
            position: absolute;
            left: 10%;
            right: 10%;
            top: 50%;
            height: 2px;
            background: #38bdf8;
            box-shadow: 0 0 18px rgba(56, 189, 248, .9);
        }

        .m-camera-status {
            min-height: 42px;
            margin: 0 16px 16px;
            padding: 10px 12px;
            border-radius: 14px;
            background: #f1f7ff;
            color: #526a88;
            font-size: .86rem;
            text-align: center;
        }

        .m-camera-status.danger {
            background: #fff1f2;
            color: #be123c;
        }

        @media (max-width: 520px) {
            .m-camera-sheet {
                padding: max(12px, env(safe-area-inset-top, 0px)) 12px max(12px, env(safe-area-inset-bottom, 0px));
            }

            .m-camera-panel {
                width: min(100%, 390px);
                border-radius: 22px;
            }

            .m-camera-head {
                padding: 12px 14px;
            }

            .m-camera-view {
                margin: 14px;
                max-height: 54dvh;
            }

            .m-camera-status {
                margin: 0 14px 14px;
                font-size: .78rem;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $isCheckin = $mode === 'checkin';
        $statusLabels = [
            'pending' => 'Chờ duyệt',
            'approved' => 'Đã duyệt',
            'checked_in' => 'Đang trong công ty',
            'checked_out' => 'Đã ra',
            'rejected' => 'Từ chối',
            'cancelled' => 'Đã hủy',
        ];
    @endphp

    <section class="m-page-head">
        <a href="{{ route('mobile.home') }}" aria-label="Quay lại"><i class="bi bi-chevron-left"></i></a>
        <div>
            <h1>{{ $title }}</h1>
            <p>{{ $subtitle }}</p>
        </div>
    </section>

    @if (session('status'))
        <div class="m-toast"><i class="bi bi-check-circle"></i><span>{{ session('status') }}</span></div>
    @endif
    @if (session('error'))
        <div class="m-toast danger"><i class="bi bi-exclamation-triangle"></i><span>{{ session('error') }}</span></div>
    @endif

    <section class="m-scan-card">
        <div class="m-scan-frame in-frame-camera">
            <i class="bi {{ $isCheckin ? 'bi-upc-scan' : 'bi-person-bounding-box' }}"></i>
            <strong>{{ $isCheckin ? 'Quét mã check-in' : 'Quét mã check-out' }}</strong>
            <span>Có thể dùng camera điện thoại hoặc nhập mã thủ công.</span>
            <button class="m-camera-btn" type="button" data-open-camera>
                <i class="bi bi-camera"></i>
                Quét bằng camera
            </button>
        </div>

        <form class="m-scan-form" action="{{ $scanRoute }}" method="post" data-mobile-scan-form>
            @csrf
            <input type="hidden" name="mobile" value="1">
            <input name="qr_token" autocomplete="off" autofocus data-qr-input placeholder="Nhập mã lịch hẹn hoặc mã QR">
            <button type="submit"><i class="bi bi-search"></i>Kiểm tra</button>
        </form>
    </section>

    @if ($scannedVisit)
        <section class="m-section">
            <div class="m-section-head">
                <div>
                    <h2>Khách vừa quét</h2>
                    <span>{{ $statusLabels[$scannedVisit->status] ?? $scannedVisit->status }}</span>
                </div>
                <a href="{{ route('mobile.visits.show', $scannedVisit) }}">Chi tiết</a>
            </div>
            <div class="m-detail-card compact">
                <div class="m-person-row">
                    <span class="m-avatar">{{ mb_substr($scannedVisit->visitor?->full_name ?? '-', 0, 1) }}</span>
                    <div>
                        <strong>{{ $scannedVisit->visitor?->full_name ?? '-' }}</strong>
                        <small>{{ $scannedVisit->visitor?->company ?? '-' }}</small>
                    </div>
                </div>
                <div class="m-detail-lines">
                    <div><span>Mã lịch</span><strong>{{ $scannedVisit->code }}</strong></div>
                    <div><span>Người gặp</span><strong>{{ $scannedVisit->hostEmployee?->name ?? '-' }}</strong></div>
                    <div><span>Phòng ban</span><strong>{{ $scannedVisit->hostEmployee?->department?->name ?? '-' }}</strong></div>
                    <div><span>Giờ hẹn</span><strong>{{ $scannedVisit->scheduled_at?->format('H:i - d/m/Y') ?? '-' }}</strong></div>
                </div>
            </div>
        </section>
    @endif

    <section class="m-section">
        <div class="m-section-head">
            <h2>{{ $isCheckin ? 'Khách chờ check-in' : 'Khách đang trong công ty' }}</h2>
            <span>{{ count($visits) }} khách</span>
        </div>
        <div class="m-visit-list">
            @forelse ($visits as $visit)
                <a class="m-visit" href="{{ $visit['url'] }}">
                    <span class="m-avatar">{{ mb_substr($visit['visitor'], 0, 1) }}</span>
                    <span class="m-visit-main">
                        <strong>{{ $visit['visitor'] }}</strong>
                        <small>{{ $visit['code'] }} · {{ $visit['department'] }}</small>
                    </span>
                    <span class="m-visit-side">
                        <strong>{{ $isCheckin ? $visit['time'] : $visit['checkin_at'] }}</strong>
                        <small>{{ $visit['host'] }}</small>
                    </span>
                </a>
            @empty
                <div class="m-empty"><i class="bi bi-person-check"></i><span>Chưa có khách phù hợp.</span></div>
            @endforelse
        </div>
    </section>

    <div class="m-camera-sheet" data-camera-modal hidden>
        <div class="m-camera-panel" role="dialog" aria-modal="true" aria-label="Quét mã QR bằng camera">
            <div class="m-camera-head">
                <strong>{{ $isCheckin ? 'Quét QR check-in' : 'Quét QR check-out' }}</strong>
                <button type="button" data-close-camera aria-label="Đóng camera"><i class="bi bi-x-lg"></i></button>
            </div>
            <div class="m-camera-view">
                <div id="mobile-qr-reader" hidden></div>
                <video data-camera-video playsinline muted></video>
                <span class="m-camera-frame" aria-hidden="true"></span>
            </div>
            <div class="m-camera-status" data-camera-status>Đưa mã QR vào giữa khung để hệ thống tự đọc.</div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
        (() => {
            const openButton = document.querySelector('[data-open-camera]');
            const closeButton = document.querySelector('[data-close-camera]');
            const modal = document.querySelector('[data-camera-modal]');
            const video = document.querySelector('[data-camera-video]');
            const html5Reader = document.getElementById('mobile-qr-reader');
            const statusBox = document.querySelector('[data-camera-status]');
            const input = document.querySelector('[data-qr-input]');
            const form = document.querySelector('[data-mobile-scan-form]');
            let detector = null;
            let html5Qr = null;
            let stream = null;
            let scanTimer = null;
            let locked = false;

            const setStatus = (message, danger = false) => {
                if (!statusBox) return;
                statusBox.textContent = message;
                statusBox.classList.toggle('danger', danger);
            };

            const stopCamera = () => {
                window.clearTimeout(scanTimer);
                scanTimer = null;
                locked = false;
                if (html5Qr) {
                    const scanner = html5Qr;
                    html5Qr = null;
                    scanner.stop().then(() => scanner.clear()).catch(() => {});
                }
                if (stream) {
                    stream.getTracks().forEach((track) => track.stop());
                    stream = null;
                }
                if (video) {
                    video.pause();
                    video.srcObject = null;
                    video.hidden = false;
                }
                if (html5Reader) html5Reader.hidden = true;
                if (modal) modal.hidden = true;
            };

            const normalizeQrValue = (value) => {
                const raw = String(value || '').trim();
                if (!raw) return '';

                try {
                    const url = new URL(raw);
                    return url.searchParams.get('qr_token')
                        || url.searchParams.get('code')
                        || url.pathname.split('/').filter(Boolean).pop()
                        || raw;
                } catch (_) {
                    return raw;
                }
            };

            const scanLoop = async () => {
                if (!detector || !video || locked || video.readyState < 2) {
                    scanTimer = window.setTimeout(scanLoop, 180);
                    return;
                }

                try {
                    const codes = await detector.detect(video);
                    if (codes.length > 0) {
                        const value = normalizeQrValue(codes[0].rawValue);
                        if (value) {
                            locked = true;
                            setStatus('Đã đọc được mã, đang xử lý...');
                            if (input) input.value = value;
                            stopCamera();
                            if (form) form.submit();
                            return;
                        }
                    }
                } catch (_) {
                    setStatus('Camera đang đọc mã, vui lòng giữ QR trong khung.');
                }

                scanTimer = window.setTimeout(scanLoop, 180);
            };

            const submitDecodedValue = (rawValue) => {
                if (locked) return;
                const value = normalizeQrValue(rawValue);
                if (!value) return;

                locked = true;
                setStatus('Đã đọc được mã, đang xử lý...');
                if (input) input.value = value;
                stopCamera();
                if (form) form.submit();
            };

            const startHtml5QrScanner = async () => {
                if (!window.Html5Qrcode || !html5Reader) {
                    setStatus('Trình duyệt này chưa hỗ trợ quét QR bằng camera. Bạn có thể nhập mã thủ công.', true);
                    return;
                }

                if (video) video.hidden = true;
                html5Reader.hidden = false;
                html5Qr = new Html5Qrcode('mobile-qr-reader');
                await html5Qr.start(
                    { facingMode: 'environment' },
                    { fps: 10, qrbox: { width: 240, height: 240 }, aspectRatio: 1 },
                    (decodedText) => submitDecodedValue(decodedText),
                    () => {}
                );
            };

            const openCamera = async () => {
                if (!navigator.mediaDevices?.getUserMedia && !window.Html5Qrcode) {
                    setStatus('Không mở được camera trên thiết bị này. Vui lòng nhập mã thủ công.', true);
                    if (modal) modal.hidden = false;
                    return;
                }

                try {
                    if (modal) modal.hidden = false;
                    setStatus('Đang mở camera...');

                    if (!('BarcodeDetector' in window)) {
                        setStatus('Đang mở bộ quét QR dự phòng...');
                        await startHtml5QrScanner();
                        setStatus('Đưa mã QR vào giữa khung để hệ thống tự đọc.');
                        return;
                    }

                    if (html5Reader) html5Reader.hidden = true;
                    if (video) video.hidden = false;
                    detector = new BarcodeDetector({ formats: ['qr_code'] });
                    stream = await navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: { ideal: 'environment' },
                            width: { ideal: 1280 },
                            height: { ideal: 1280 },
                        },
                        audio: false,
                    });
                    video.srcObject = stream;
                    await video.play();
                    setStatus('Đưa mã QR vào giữa khung để hệ thống tự đọc.');
                    scanLoop();
                } catch (error) {
                    stopCamera();
                    if (modal) modal.hidden = false;
                    setStatus('Chưa mở được camera. Hãy kiểm tra quyền camera hoặc dùng HTTPS rồi thử lại.', true);
                }
            };

            openButton?.addEventListener('click', openCamera);
            closeButton?.addEventListener('click', stopCamera);
            modal?.addEventListener('click', (event) => {
                if (event.target === modal) stopCamera();
            });
            window.addEventListener('pagehide', stopCamera);
        })();
    </script>
@endpush

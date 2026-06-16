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
            border: 1px solid rgba(212, 5, 17, .18);
            border-radius: 999px;
            background: #fff8d6;
            color: var(--m-primary);
            font: inherit;
            font-size: .86rem;
            padding: 0 14px;
            box-shadow: 0 10px 22px rgba(212, 5, 17, .08);
        }

        .m-camera-btn i {
            color: inherit !important;
            font-size: 1rem !important;
        }

        .m-access-head {
            grid-template-columns: 42px minmax(0, 1fr) 42px;
        }

        .m-access-settings-btn {
            width: 42px;
            height: 42px;
            display: grid;
            place-items: center;
            border: 1px solid #d8e6f4;
            border-radius: 14px;
            background: #fff;
            color: #42617f;
            font-size: 1rem;
        }

        .m-access-settings-sheet[hidden] {
            display: none !important;
        }

        .m-access-settings-sheet {
            position: fixed;
            inset: 0;
            z-index: 90;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            padding: 0 max(0px, env(safe-area-inset-right, 0px)) 0 max(0px, env(safe-area-inset-left, 0px));
            background: rgba(15, 23, 42, .48);
            backdrop-filter: blur(5px);
        }

        .m-access-settings-panel {
            width: min(100%, 520px);
            max-height: min(88dvh, 720px);
            overflow: auto;
            padding: 10px 16px calc(18px + env(safe-area-inset-bottom, 0px));
            border-radius: 24px 24px 0 0;
            background: #fff;
            box-shadow: 0 -20px 60px rgba(15, 35, 60, .2);
        }

        .m-access-settings-handle {
            width: 42px;
            height: 4px;
            margin: 0 auto 12px;
            border-radius: 999px;
            background: #d5e0ec;
        }

        .m-access-settings-head,
        .m-access-setting-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .m-access-settings-head {
            margin-bottom: 14px;
        }

        .m-access-settings-head h2 {
            margin: 0;
            color: #10233d;
            font-size: 1rem;
            font-weight: 600;
        }

        .m-access-settings-head p,
        .m-access-setting-copy span {
            margin: 3px 0 0;
            color: #788ba3;
            font-size: .72rem;
        }

        .m-access-settings-close {
            width: 36px;
            height: 36px;
            display: grid;
            place-items: center;
            flex: 0 0 36px;
            border: 0;
            border-radius: 12px;
            background: #f1f5f9;
            color: #526b87;
        }

        .m-access-settings-body {
            display: grid;
            gap: 9px;
        }

        .m-access-setting-row {
            min-height: 62px;
            padding: 10px 12px;
            border: 1px solid #e4edf7;
            border-radius: 15px;
            background: #fbfdff;
        }

        .m-access-setting-copy label {
            display: block;
            color: #203852;
            font-size: .82rem;
            font-weight: 500;
        }

        .m-access-switch {
            width: 42px;
            height: 24px;
            position: relative;
            flex: 0 0 42px;
            appearance: none;
            border: 0;
            border-radius: 999px;
            background: #cbd5e1;
            cursor: pointer;
            transition: background .18s ease;
        }

        .m-access-switch::after {
            content: "";
            position: absolute;
            top: 3px;
            left: 3px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #fff;
            box-shadow: 0 2px 5px rgba(15, 23, 42, .2);
            transition: transform .18s ease;
        }

        .m-access-switch:checked {
            background: var(--m-primary);
        }

        .m-access-switch:checked::after {
            transform: translateX(18px);
        }

        .m-access-switch:focus-visible {
            outline: 3px solid rgba(212, 5, 17, .18);
            outline-offset: 2px;
        }

        .m-access-minutes {
            width: 92px;
            min-height: 38px;
            padding: 6px 8px;
            border: 1px solid #d7e5f3;
            border-radius: 11px;
            background: #fff;
            color: #10233d;
            font: inherit;
            font-size: .82rem;
            text-align: center;
        }

        .m-access-warning-field {
            display: grid;
            gap: 6px;
            color: #203852;
            font-size: .82rem;
            font-weight: 500;
        }

        .m-access-warning-field textarea {
            width: 100%;
            min-height: 82px;
            padding: 10px 12px;
            border: 1px solid #d7e5f3;
            border-radius: 13px;
            color: #10233d;
            font: inherit;
            font-size: .8rem;
            font-weight: 400;
            resize: vertical;
        }

        .m-access-settings-save {
            width: 100%;
            min-height: 44px;
            margin-top: 12px;
            border: 0;
            border-radius: 13px;
            background: var(--m-secondary);
            color: #111827;
            font: inherit;
            font-size: .86rem;
            font-weight: 500;
        }

        @media (min-width: 560px) {
            .m-access-settings-sheet {
                padding-bottom: 14px;
            }

            .m-access-settings-panel {
                border-radius: 24px;
            }
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

        .m-result-sheet[hidden] {
            display: none !important;
        }

        .m-result-sheet {
            position: fixed;
            inset: 0;
            z-index: 90;
            display: grid;
            place-items: center;
            padding: max(16px, env(safe-area-inset-top, 0px)) 16px max(16px, env(safe-area-inset-bottom, 0px));
            background: rgba(15, 23, 42, .6);
            backdrop-filter: blur(9px);
        }

        .m-result-panel {
            width: min(100%, 410px);
            max-height: calc(100dvh - 32px);
            overflow-y: auto;
            border-radius: 26px;
            background: #fff;
            box-shadow: 0 26px 80px rgba(15, 23, 42, .28);
        }

        .m-result-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 17px 18px;
            border-bottom: 1px solid #e8eef5;
        }

        .m-result-head h2 {
            margin: 0;
            font-size: 1.05rem;
            font-weight: 600;
        }

        .m-result-close {
            width: 40px;
            height: 40px;
            display: grid;
            place-items: center;
            border: 1px solid #d9e5f1;
            border-radius: 14px;
            color: #526a88;
            background: #fff;
            font-size: 1rem;
        }

        .m-result-body {
            display: grid;
            gap: 14px;
            padding: 18px;
        }

        .m-result-summary {
            display: grid;
            grid-template-columns: 52px minmax(0, 1fr);
            align-items: center;
            gap: 13px;
            padding: 15px;
            border: 1px solid;
            border-radius: 19px;
        }

        .m-result-summary.success {
            border-color: #b7e8cc;
            color: #087a43;
            background: #effcf5;
        }

        .m-result-summary.danger {
            border-color: #fecaca;
            color: #b42318;
            background: #fff3f4;
        }

        .m-result-icon {
            width: 52px;
            height: 52px;
            display: grid;
            place-items: center;
            border-radius: 17px;
            background: rgba(255, 255, 255, .72);
            font-size: 1.45rem;
        }

        .m-result-summary h3,
        .m-result-summary p {
            margin: 0;
        }

        .m-result-summary h3 {
            font-size: .94rem;
            font-weight: 600;
        }

        .m-result-summary p {
            margin-top: 4px;
            color: #51677f;
            font-size: .76rem;
            line-height: 1.45;
        }

        .m-result-details {
            overflow: hidden;
            border: 1px solid #e0e9f2;
            border-radius: 18px;
            background: #fbfdff;
        }

        .m-result-row {
            min-height: 48px;
            display: grid;
            grid-template-columns: minmax(0, .8fr) minmax(0, 1.2fr);
            align-items: center;
            gap: 12px;
            padding: 10px 13px;
            border-bottom: 1px solid #e8eef5;
            font-size: .76rem;
        }

        .m-result-row:last-child {
            border-bottom: 0;
        }

        .m-result-row span {
            color: var(--m-muted);
        }

        .m-result-row strong {
            overflow-wrap: anywhere;
            color: var(--m-text);
            font-weight: 500;
            text-align: right;
        }

        .m-result-action {
            width: 100%;
            min-height: 46px;
            border: 0;
            border-radius: 15px;
            color: #111827;
            background: var(--m-secondary);
            font: inherit;
            font-size: .84rem;
            font-weight: 500;
        }

        .m-result-timer {
            margin: -3px 0 0;
            color: var(--m-muted);
            font-size: .68rem;
            text-align: center;
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

            .m-result-panel {
                border-radius: 23px;
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
        $resultMessage = session('error')
            ?: (session('status') ?: ($errors->any() ? $errors->first() : null));
        $resultIsSuccess = session('status') && ! session('error') && ! $errors->any();
        $allowEarlyCheckin = ($accessSettings['access.allow_early_checkin'] ?? '1') === '1';
        $allowLateCheckin = ($accessSettings['access.allow_late_checkin'] ?? '1') === '1';
        $warningEnabled = ($accessSettings['access.warning_enabled'] ?? '1') === '1';
    @endphp

    <section class="m-page-head m-access-head">
        <a href="{{ route('mobile.home') }}" aria-label="Quay lại"><i class="bi bi-chevron-left"></i></a>
        <div>
            <h1>{{ $title }}</h1>
            <p>{{ $subtitle }}</p>
        </div>
        @if (auth()->user()?->hasPermission('system.manage'))
            <button class="m-access-settings-btn" type="button" data-open-access-settings aria-label="Cấu hình nhanh">
                <i class="bi bi-gear"></i>
            </button>
        @endif
    </section>

    @if (auth()->user()?->hasPermission('system.manage'))
        <div class="m-access-settings-sheet" data-access-settings-sheet hidden>
            <form class="m-access-settings-panel" method="post" action="{{ route('admin.access.quick-settings.update') }}">
                @csrf
                @method('put')
                <input type="hidden" name="return_mode" value="{{ $mode }}">
                <input type="hidden" name="return_mobile" value="1">
                <div class="m-access-settings-handle"></div>
                <div class="m-access-settings-head">
                    <div>
                        <h2>Cấu hình Check-in/Check-out</h2>
                        <p>Thiết lập nhanh khung giờ và cảnh báo.</p>
                    </div>
                    <button class="m-access-settings-close" type="button" data-close-access-settings aria-label="Đóng">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="m-access-settings-body">
                    <div class="m-access-setting-row">
                        <div class="m-access-setting-copy">
                            <label for="mobileAllowEarly">Cho phép check-in sớm</label>
                            <span>Cho khách vào trước giờ hẹn.</span>
                        </div>
                        <input class="m-access-switch" id="mobileAllowEarly" type="checkbox" name="allow_early_checkin" value="1" @checked($allowEarlyCheckin)>
                    </div>
                    <div class="m-access-setting-row">
                        <div class="m-access-setting-copy">
                            <label for="mobileEarlyMinutes">Số phút check-in sớm</label>
                            <span>Mặc định 30 phút.</span>
                        </div>
                        <input class="m-access-minutes" id="mobileEarlyMinutes" type="number" name="early_checkin_minutes" min="0" max="1440" value="{{ old('early_checkin_minutes', $accessSettings['access.early_checkin_minutes'] ?? 30) }}" required>
                    </div>
                    <div class="m-access-setting-row">
                        <div class="m-access-setting-copy">
                            <label for="mobileAllowLate">Cho phép check-in trễ</label>
                            <span>Cho khách vào sau giờ hẹn.</span>
                        </div>
                        <input class="m-access-switch" id="mobileAllowLate" type="checkbox" name="allow_late_checkin" value="1" @checked($allowLateCheckin)>
                    </div>
                    <div class="m-access-setting-row">
                        <div class="m-access-setting-copy">
                            <label for="mobileLateMinutes">Số phút check-in trễ</label>
                            <span>Mặc định 60 phút.</span>
                        </div>
                        <input class="m-access-minutes" id="mobileLateMinutes" type="number" name="late_checkin_minutes" min="0" max="1440" value="{{ old('late_checkin_minutes', $accessSettings['access.late_checkin_minutes'] ?? 60) }}" required>
                    </div>
                    <div class="m-access-setting-row">
                        <div class="m-access-setting-copy">
                            <label for="mobileWarningEnabled">Bật cảnh báo</label>
                            <span>Hiện nội dung khi ngoài khung giờ.</span>
                        </div>
                        <input class="m-access-switch" id="mobileWarningEnabled" type="checkbox" name="warning_enabled" value="1" @checked($warningEnabled)>
                    </div>
                    <label class="m-access-warning-field" for="mobileWarningMessage">
                        Nội dung cảnh báo
                        <textarea id="mobileWarningMessage" name="warning_message" maxlength="500" placeholder="Nhập nội dung cảnh báo...">{{ old('warning_message', $accessSettings['access.warning_message'] ?? '') }}</textarea>
                    </label>
                </div>
                <button class="m-access-settings-save" type="submit">
                    <i class="bi bi-check2"></i> Lưu cấu hình
                </button>
            </form>
        </div>
    @endif

    <section class="m-scan-card">
        <div class="m-scan-frame in-frame-camera">
            <i class="bi {{ $isCheckin ? 'bi-upc-scan' : 'bi-person-bounding-box' }}"></i>
            <strong>{{ $isCheckin ? 'Quét mã check-in' : 'Quét mã check-out' }}</strong>
            <span>Dùng camera điện thoại để quét mã QR.</span>
            <button class="m-camera-btn" type="button" data-open-camera>
                <i class="bi bi-camera"></i>
                Quét bằng camera
            </button>
        </div>

        <form action="{{ $scanRoute }}" method="post" data-mobile-scan-form hidden>
            @csrf
            <input type="hidden" name="mobile" value="1">
            <input type="hidden" name="qr_token" data-qr-input>
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

    @if ($resultMessage)
        <div class="m-result-sheet" data-result-modal>
            <div class="m-result-panel" role="dialog" aria-modal="true" aria-labelledby="mobile-result-title">
                <div class="m-result-head">
                    <h2 id="mobile-result-title">Kết quả {{ $isCheckin ? 'check-in' : 'check-out' }}</h2>
                    <button class="m-result-close" type="button" data-close-result aria-label="Đóng">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="m-result-body">
                    <div class="m-result-summary {{ $resultIsSuccess ? 'success' : 'danger' }}">
                        <span class="m-result-icon">
                            <i class="bi {{ $resultIsSuccess ? 'bi-check-circle' : 'bi-exclamation-triangle' }}"></i>
                        </span>
                        <div>
                            <h3>{{ $resultIsSuccess ? ($isCheckin ? 'Check-in thành công' : 'Check-out thành công') : ($isCheckin ? 'Không thể check-in' : 'Không thể check-out') }}</h3>
                            <p>{{ $resultMessage }}</p>
                        </div>
                    </div>

                    @if ($scannedVisit)
                        <div class="m-result-details">
                            <div class="m-result-row">
                                <span>Mã lịch hẹn</span>
                                <strong>{{ $scannedVisit->code }}</strong>
                            </div>
                            <div class="m-result-row">
                                <span>Khách</span>
                                <strong>{{ $scannedVisit->visitor?->full_name ?? '-' }}</strong>
                            </div>
                            <div class="m-result-row">
                                <span>Người tiếp khách</span>
                                <strong>{{ $scannedVisit->hostEmployee?->name ?? '-' }}</strong>
                            </div>
                            <div class="m-result-row">
                                <span>Phòng ban</span>
                                <strong>{{ $scannedVisit->hostEmployee?->department?->name ?? '-' }}</strong>
                            </div>
                            <div class="m-result-row">
                                <span>Trạng thái</span>
                                <strong>{{ $statusLabels[$scannedVisit->status] ?? $scannedVisit->status }}</strong>
                            </div>
                        </div>
                    @endif

                    <button class="m-result-action" type="button" data-close-result>Đóng</button>
                    <p class="m-result-timer">Tự động đóng sau <span data-result-countdown>15</span> giây</p>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
        (() => {
            const sheet = document.querySelector('[data-access-settings-sheet]');
            const openButton = document.querySelector('[data-open-access-settings]');
            const closeButton = document.querySelector('[data-close-access-settings]');

            const openSheet = () => {
                if (!sheet) return;
                sheet.hidden = false;
                document.body.style.overflow = 'hidden';
            };

            const closeSheet = () => {
                if (!sheet) return;
                sheet.hidden = true;
                document.body.style.overflow = '';
            };

            openButton?.addEventListener('click', openSheet);
            closeButton?.addEventListener('click', closeSheet);
            sheet?.addEventListener('click', (event) => {
                if (event.target === sheet) closeSheet();
            });
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && sheet && !sheet.hidden) closeSheet();
            });
        })();

        (() => {
            const openButton = document.querySelector('[data-open-camera]');
            const closeButton = document.querySelector('[data-close-camera]');
            const modal = document.querySelector('[data-camera-modal]');
            const video = document.querySelector('[data-camera-video]');
            const html5Reader = document.getElementById('mobile-qr-reader');
            const statusBox = document.querySelector('[data-camera-status]');
            const input = document.querySelector('[data-qr-input]');
            const form = document.querySelector('[data-mobile-scan-form]');
            const resultModal = document.querySelector('[data-result-modal]');
            const resultCountdown = document.querySelector('[data-result-countdown]');
            let detector = null;
            let html5Qr = null;
            let stream = null;
            let scanTimer = null;
            let locked = false;
            let resultTimer = null;

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
                    setStatus('Trình duyệt này chưa hỗ trợ quét QR bằng camera.', true);
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
                    setStatus('Không mở được camera trên thiết bị này.', true);
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
            form?.addEventListener('submit', () => {
                input?.blur();
            });

            const closeResult = () => {
                window.clearInterval(resultTimer);
                resultTimer = null;
                if (resultModal) resultModal.hidden = true;
                input?.focus({ preventScroll: true });
            };

            document.querySelectorAll('[data-close-result]').forEach((button) => {
                button.addEventListener('click', closeResult);
            });
            resultModal?.addEventListener('click', (event) => {
                if (event.target === resultModal) closeResult();
            });

            if (resultModal && resultCountdown) {
                let seconds = 15;
                resultTimer = window.setInterval(() => {
                    seconds -= 1;
                    resultCountdown.textContent = String(Math.max(seconds, 0));
                    if (seconds <= 0) closeResult();
                }, 1000);
            }

            window.addEventListener('pagehide', stopCamera);
        })();
    </script>
@endpush

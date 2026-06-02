@extends('layouts.admin')

@section('title', 'Khách vào | Quản lý khách')
@section('page_title', 'Khách vào')
@section('page_subtitle', 'Nhập mã QR hoặc mã lịch hẹn để làm thủ tục khách vào')

@push('styles')
<style>
.ci-page{display:grid;gap:1rem}.ci-top{display:grid;grid-template-columns:minmax(360px,38%) minmax(0,62%);gap:1rem;align-items:stretch}.ci-card{background:#fff;border:1px solid #e1ebf6;border-radius:22px;box-shadow:0 14px 34px rgba(17,39,68,.07);overflow:hidden}.ci-head{padding:1rem 1.15rem .85rem;border-bottom:1px solid #edf3fb}.ci-head h3{margin:0;color:#0b1f3a;font-family:"Plus Jakarta Sans",sans-serif;font-size:1rem;font-weight:900}.ci-head p{margin:.2rem 0 0;color:#6e83a0;font-size:.78rem}.ci-scan-box{padding:1rem}.ci-scan-frame{position:relative;display:grid;place-items:center;height:230px;border:1px dashed #a8c9eb;border-radius:18px;background:linear-gradient(90deg,rgba(20,107,215,.055) 1px,transparent 1px),linear-gradient(rgba(20,107,215,.055) 1px,transparent 1px),#eef7ff;background-size:24px 24px}.ci-scan-frame i{color:#b9d5ef;font-size:3.5rem}.ci-corners:before,.ci-corners:after,.ci-corner-tr,.ci-corner-bl{content:"";position:absolute;width:28px;height:28px;border:3px solid #146bd7}.ci-corners{position:absolute;inset:0}.ci-corners:before{top:12px;left:12px;border-right:0;border-bottom:0;border-radius:4px 0 0}.ci-corners:after{right:12px;bottom:12px;border-left:0;border-top:0;border-radius:0 0 4px}.ci-corner-tr{top:12px;right:12px;border-left:0;border-bottom:0}.ci-corner-bl{left:12px;bottom:12px;border-right:0;border-top:0}.ci-reader-copy{position:relative;z-index:1;text-align:center;color:#5a7a99}.ci-reader-copy strong{display:block;margin-top:.35rem;color:#0b1f3a;font-size:1rem;font-weight:900}.ci-reader-copy span{font-size:.78rem}.ci-hint{margin:.8rem 0 .65rem;color:#7890aa;text-align:center;font-size:.76rem}.ci-form{display:grid;gap:.62rem}.ci-input-wrap{position:relative}.ci-input-wrap i{position:absolute;right:.85rem;top:50%;transform:translateY(-50%);color:#9aafca;pointer-events:none}.ci-input-wrap input{width:100%;min-height:46px;padding:.7rem 2.4rem .7rem .9rem;border:1.5px solid #d8e5f2;border-radius:13px;color:#0b1f3a;font-size:.86rem}.ci-input-wrap input:focus{outline:0;border-color:#146bd7;box-shadow:0 0 0 4px rgba(20,107,215,.1)}.ci-btn-blue{display:flex;align-items:center;justify-content:center;gap:.45rem;width:100%;min-height:46px;border:0;border-radius:13px;background:linear-gradient(135deg,#146bd7,#0cb4d8);color:#fff;font-weight:900;box-shadow:0 12px 24px rgba(20,107,215,.18)}.ci-detail{display:flex;flex-direction:column;min-height:100%}.ci-empty{flex:1;min-height:0;display:grid;place-items:center;text-align:center;color:#8aa0ba;padding:1.5rem}.ci-empty i{font-size:3rem;color:#c5dbf2}.ci-empty strong{display:block;margin:.5rem 0 .25rem;color:#526b87;font-size:1rem}.ci-empty p{max-width:440px;margin:0 auto;line-height:1.55}.ci-profile{display:flex;align-items:center;gap:1rem;padding:1.1rem 1.25rem;border-bottom:1px solid #edf3fb}.ci-avatar{width:70px;height:70px;display:grid;place-items:center;border-radius:22px;background:#dbeafe;color:#1d4ed8;font-size:1.55rem;font-weight:900}.ci-name{margin:0;color:#0b1f3a;font-family:"Plus Jakarta Sans",sans-serif;font-size:1.25rem;font-weight:900}.ci-company{color:#5a7a99;font-size:.86rem}.ci-info-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.65rem;padding:1rem 1.25rem}.ci-info{display:grid;grid-template-columns:34px 1fr;gap:.55rem;align-items:center;padding:.72rem;border:1px solid #edf3fb;border-radius:15px;background:#fbfdff}.ci-info i{width:34px;height:34px;display:grid;place-items:center;border-radius:12px;background:#eff6ff;color:#146bd7}.ci-label{display:block;color:#7086a1;font-size:.7rem;font-weight:900}.ci-value{display:block;margin-top:.12rem;color:#0b1f3a;font-size:.84rem;font-weight:900}.ci-meta{display:flex;align-items:center;justify-content:space-between;gap:1rem;margin:0 1.25rem;padding:.8rem;border:1px solid #edf3fb;border-radius:15px;background:#f8fbff}.ci-code{color:#0b1f3a;font-weight:900}.ci-btn-checkin{display:flex;align-items:center;justify-content:center;gap:.55rem;width:calc(100% - 2.5rem);margin:1rem 1.25rem 1.25rem;min-height:48px;border:0;border-radius:15px;background:linear-gradient(135deg,#059669,#10b981);color:#fff;font-weight:900}.ci-warning{margin:1rem 1.25rem 1.25rem;padding:.85rem;border:1px solid #fed7aa;border-radius:15px;background:#fff7ed;color:#9a3412;font-size:.84rem;font-weight:800}.ci-warning-info{border-color:#bfdbfe;background:#eff6ff;color:#1d4ed8}.ci-warning-danger{border-color:#fecaca;background:#fff1f2;color:#be123c}.ci-list-card{min-height:420px}.ci-list-head{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:1rem 1.15rem;border-bottom:1px solid #edf3fb}.ci-list-head h3{margin:0;color:#0b1f3a;font-family:"Plus Jakarta Sans",sans-serif;font-size:1rem;font-weight:900}.ci-list-head p{margin:.18rem 0 0;color:#6e83a0;font-size:.78rem}.ci-pill{display:inline-flex;align-items:center;gap:.28rem;padding:.25rem .68rem;border-radius:999px;background:#dbeafe;color:#1d4ed8;font-size:.72rem;font-weight:900}.ci-qhead,.ci-qrow{display:grid;grid-template-columns:150px 100px minmax(180px,1fr) minmax(180px,1fr) 140px 150px;gap:.8rem;align-items:center}.ci-qhead{padding:.85rem 1.15rem .55rem;color:#7086a1;font-size:.7rem;font-weight:900;text-transform:uppercase;border-bottom:1px solid #edf3fb}.ci-qrow{width:100%;padding:.82rem 1.15rem;border:0;border-bottom:1px solid #f0f5fb;background:#fff;text-align:left;cursor:pointer}.ci-qrow:hover{background:#f2f8ff}.ci-qcode{color:#146bd7;font-size:.82rem;font-weight:900}.ci-qtime{color:#0b1f3a;font-size:.84rem;font-weight:900}.ci-qtime small{display:block;color:#7890aa;font-weight:700}.ci-qname{color:#0b1f3a;font-size:.88rem;font-weight:900}.ci-qmuted{color:#7890aa;font-size:.74rem}.ci-qdept{display:inline-flex;width:max-content;padding:.25rem .65rem;border-radius:999px;background:#eff6ff;color:#315b89;font-size:.72rem;font-weight:900}.ci-qstatus{display:inline-flex;align-items:center;gap:.3rem;width:max-content;padding:.3rem .62rem;border-radius:999px;background:#ecfdf5;color:#047857;font-size:.72rem;font-weight:900}.ci-list-empty{padding:2.5rem;text-align:center;color:#8aa0ba}.ci-viewall{display:flex;align-items:center;justify-content:center;gap:.35rem;padding:.8rem;color:#146bd7;font-size:.82rem;font-weight:900;text-decoration:none;border-top:1px solid #edf3fb}.ci-viewall:hover{background:#f0f7ff}
.ci-pager{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:.85rem 1.15rem;border-top:1px solid #edf3fb;background:#fbfdff}.ci-pager-info{color:#7086a1;font-size:.78rem;font-weight:800}.ci-pager-buttons{display:flex;align-items:center;gap:.35rem}.ci-page-btn{min-width:34px;height:34px;border:1px solid #d8e5f2;border-radius:10px;background:#fff;color:#315b89;font-size:.78rem;font-weight:900}.ci-page-btn:hover{background:#eff6ff;border-color:#b9d5ef}.ci-page-btn.active{border-color:#146bd7;background:#146bd7;color:#fff}.ci-page-btn:disabled{opacity:.45;cursor:not-allowed}
@media(max-width:1200px){.ci-top{grid-template-columns:1fr}.ci-info-grid{grid-template-columns:1fr}.ci-qhead{display:none}.ci-qrow{grid-template-columns:1fr;gap:.35rem}}@media(max-width:768px){.ci-list-head{align-items:flex-start;flex-direction:column}.ci-scan-frame{height:190px}.ci-pager{align-items:flex-start;flex-direction:column}}
</style>
@endpush

@section('content')
<div class="ci-page">
    <div class="ci-top">
        <section class="ci-card">
            <div class="ci-head">
                <h3>Quét mã khách vào</h3>
                <p>Dùng thiết bị đọc QR tại quầy hoặc nhập mã lịch hẹn thủ công.</p>
            </div>

            <div class="ci-scan-box">
                <div class="ci-scan-frame">
                    <div class="ci-corners"></div><div class="ci-corner-tr"></div><div class="ci-corner-bl"></div>
                    <div class="ci-reader-copy">
                        <i class="bi bi-upc-scan"></i>
                        <strong>Thiết bị đọc QR</strong>
                        <span>Đưa mã QR của khách vào đầu đọc</span>
                    </div>
                </div>

                <p class="ci-hint">Ô nhập mã đã được chọn sẵn. Máy quét USB đọc xong sẽ tự kiểm tra nếu có phím Enter.</p>

                <form class="ci-form" id="checkinQrForm" action="{{ route('admin.checkin.scan-qr') }}" method="post">
                    @csrf
                    <div class="ci-input-wrap">
                        <input type="text" name="qr_token" id="checkinCodeInput" value="{{ old('qr_token') }}" placeholder="Nhập mã lịch hẹn (VD: WK-260529-001)" autocomplete="off" autofocus>
                        <i class="bi bi-upc-scan"></i>
                    </div>
                    <button class="ci-btn-blue" type="submit">
                        <i class="bi bi-search"></i>
                        Kiểm tra mã
                    </button>
                </form>
            </div>
        </section>

        <section class="ci-card ci-detail">
            @if ($scannedVisit)
                <div class="ci-head">
                    <div class="d-flex align-items-start justify-content-between gap-3">
                        <div>
                            <h3>Thông tin khách</h3>
                            <p>Kiểm tra trước khi xác nhận khách vào công ty.</p>
                        </div>
                        <x-status-badge :status="$scannedVisit->status" />
                    </div>
                </div>

                <div class="ci-profile">
                    <div class="ci-avatar">{{ strtoupper(mb_substr($scannedVisit->visitor?->full_name ?? 'K', 0, 1)) }}</div>
                    <div>
                        <h3 class="ci-name">{{ $scannedVisit->visitor?->full_name ?? '-' }}</h3>
                        <div class="ci-company">{{ $scannedVisit->visitor?->company ?? 'Khách vãng lai' }}</div>
                    </div>
                </div>

                <div class="ci-info-grid">
                    <div class="ci-info"><i class="bi bi-person-badge"></i><div><span class="ci-label">Người cần gặp</span><span class="ci-value">{{ $scannedVisit->hostEmployee?->name ?? '-' }}</span></div></div>
                    <div class="ci-info"><i class="bi bi-building"></i><div><span class="ci-label">Phòng ban</span><span class="ci-value">{{ $scannedVisit->hostEmployee?->department?->name ?? '-' }}</span></div></div>
                    <div class="ci-info"><i class="bi bi-clock"></i><div><span class="ci-label">Giờ hẹn</span><span class="ci-value">{{ $scannedVisit->scheduled_at?->format('H:i - d/m/Y') ?? '-' }}</span></div></div>
                    <div class="ci-info"><i class="bi bi-telephone"></i><div><span class="ci-label">Số điện thoại</span><span class="ci-value">{{ $scannedVisit->visitor?->phone ?? '-' }}</span></div></div>
                    <div class="ci-info"><i class="bi bi-envelope"></i><div><span class="ci-label">Email</span><span class="ci-value">{{ $scannedVisit->visitor?->email ?? '-' }}</span></div></div>
                    <div class="ci-info"><i class="bi bi-chat-square-text"></i><div><span class="ci-label">Mục đích đến</span><span class="ci-value">{{ $scannedVisit->purpose ?? '-' }}</span></div></div>
                </div>

                <div class="ci-meta">
                    <span>{{ $scannedVisit->qr_token && !$scannedQrExpired ? 'Mã hợp lệ' : 'QR đã hết hạn' }}</span>
                    <span class="ci-code">{{ $scannedVisit->code }}</span>
                </div>

                @if ($scannedVisit->status === 'approved' && !$scannedQrExpired)
                    <form action="{{ route('admin.checkin.confirm', $scannedVisit) }}" method="post">
                        @csrf
                        <button class="ci-btn-checkin" type="submit">
                            <i class="bi bi-check-circle-fill"></i>
                            Xác nhận khách vào
                        </button>
                    </form>
                @else
                    @php
                        $checkinNotice = match ($scannedVisit->status) {
                            'checked_in' => [
                                'class' => 'ci-warning-info',
                                'icon' => 'bi-check-circle',
                                'text' => 'Khách đã check-in và đang trong công ty.',
                            ],
                            'checked_out' => [
                                'class' => 'ci-warning-info',
                                'icon' => 'bi-box-arrow-left',
                                'text' => 'Khách đã check-out, không thể check-in lại từ màn hình này.',
                            ],
                            'rejected' => [
                                'class' => 'ci-warning-danger',
                                'icon' => 'bi-x-circle',
                                'text' => 'Lịch đã bị từ chối, không thể cho khách vào.',
                            ],
                            'cancelled' => [
                                'class' => 'ci-warning-danger',
                                'icon' => 'bi-x-circle',
                                'text' => 'Lịch đã hủy, không thể cho khách vào.',
                            ],
                            default => $scannedVisit->status === 'approved'
                                ? [
                                    'class' => '',
                                    'icon' => 'bi-exclamation-triangle',
                                    'text' => 'Mã QR đã hết hạn hoặc không hợp lệ.',
                                ]
                                : [
                                    'class' => '',
                                    'icon' => 'bi-info-circle',
                                    'text' => 'Lịch chưa được duyệt, vui lòng duyệt trước khi cho khách vào.',
                                ],
                        };
                    @endphp
                    <div class="ci-warning {{ $checkinNotice['class'] }}">
                        <i class="bi {{ $checkinNotice['icon'] }} me-1"></i> {{ $checkinNotice['text'] }}
                    </div>
                @endif
            @else
                <div class="ci-empty">
                    <div>
                        <i class="bi bi-person-bounding-box"></i>
                        <strong>Chưa có khách được chọn</strong>
                        <p>Quét QR hoặc nhập mã lịch hẹn bên trái để hiển thị thông tin khách cần làm thủ tục vào.</p>
                    </div>
                </div>
            @endif
        </section>
    </div>

    <section class="ci-card ci-list-card">
        <div class="ci-list-head">
            <div>
                <h3>Khách chờ check-in</h3>
                <p>Danh sách lịch đã duyệt mới nhất đang chờ làm thủ tục vào.</p>
            </div>
            <span class="ci-pill">{{ count($upcomingToday) }} khách</span>
        </div>

        @if (count($upcomingToday) > 0)
            <div class="ci-qhead">
                <span>Mã lịch</span>
                <span>Giờ hẹn</span>
                <span>Khách</span>
                <span>Người cần gặp</span>
                <span>Phòng ban</span>
                <span>Trạng thái</span>
            </div>
        @endif

        @forelse ($upcomingToday as $visit)
            <button class="ci-qrow" type="button" data-checkin-code="{{ $visit['code'] }}">
                <span class="ci-qcode">{{ $visit['code'] }}</span>
                <span class="ci-qtime">{{ $visit['time'] }}<small>{{ $visit['date'] ?? '-' }}</small></span>
                <span><span class="ci-qname">{{ $visit['visitor'] }}</span><span class="ci-qmuted d-block">Chờ làm thủ tục vào</span></span>
                <span><span class="ci-qname">{{ $visit['host'] }}</span><span class="ci-qmuted d-block">Người tiếp khách</span></span>
                <span class="ci-qdept">{{ $visit['department'] }}</span>
                <span class="ci-qstatus"><i class="bi bi-check-circle"></i> Đã duyệt</span>
            </button>
        @empty
            <div class="ci-list-empty">
                <i class="bi bi-calendar-check d-block fs-2 mb-2"></i>
                Không có khách đã duyệt đang chờ check-in hôm nay.
            </div>
        @endforelse

        @if (count($upcomingToday) > 0)
            <div class="ci-pager" id="ciQueuePager" data-total="{{ count($upcomingToday) }}"></div>
        @endif

        <a class="ci-viewall" href="{{ route('admin.visits.index') }}">
            Xem tất cả lịch hẹn
            <i class="bi bi-arrow-right"></i>
        </a>
    </section>
</div>
@endsection

@push('scripts')
<script>
    const checkinInput = document.getElementById('checkinCodeInput');
    const checkinForm = document.getElementById('checkinQrForm');
    let checkinSubmitTimer = null;
    let checkinSubmitted = false;

    function submitCheckinLookup() {
        if (!checkinForm || !checkinInput || checkinSubmitted) return;

        const value = checkinInput.value.trim();
        if (!value) return;

        checkinSubmitted = true;
        checkinForm.requestSubmit();
    }

    function looksLikeCompleteCheckinCode(value) {
        const normalized = value.trim();

        return /^\d{8}$/.test(normalized)
            || /^(WK|VO|RP)-[A-Z0-9-]{6,}$/i.test(normalized);
    }

    function scheduleAutoCheckinLookup() {
        clearTimeout(checkinSubmitTimer);

        const value = checkinInput?.value.trim() || '';
        if (!looksLikeCompleteCheckinCode(value)) return;

        checkinSubmitTimer = setTimeout(submitCheckinLookup, 280);
    }

    if (checkinInput) {
        checkinInput.focus();
        checkinInput.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' && checkinInput.value.trim() !== '') {
                event.preventDefault();
                submitCheckinLookup();
            }
        });
        checkinInput.addEventListener('input', scheduleAutoCheckinLookup);
        checkinInput.addEventListener('paste', () => setTimeout(scheduleAutoCheckinLookup, 0));
    }

    document.querySelectorAll('[data-checkin-code]').forEach((row) => {
        row.addEventListener('click', () => {
            if (!checkinInput) return;
            checkinInput.value = row.dataset.checkinCode || '';
            checkinInput.focus();
            checkinInput.select();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });

    const checkinRows = Array.from(document.querySelectorAll('[data-checkin-code]'));
    const checkinPager = document.getElementById('ciQueuePager');
    const checkinPerPage = 5;
    let checkinPage = 1;

    function renderCheckinPager() {
        if (!checkinPager || checkinRows.length <= checkinPerPage) {
            if (checkinPager) checkinPager.style.display = 'none';
            return;
        }

        const totalPages = Math.ceil(checkinRows.length / checkinPerPage);
        checkinPage = Math.min(checkinPage, totalPages);
        const start = (checkinPage - 1) * checkinPerPage;
        const end = start + checkinPerPage;

        checkinRows.forEach((row, index) => {
            row.style.display = index >= start && index < end ? '' : 'none';
        });

        checkinPager.innerHTML = `
            <div class="ci-pager-info">Hiển thị ${start + 1} - ${Math.min(end, checkinRows.length)} trong ${checkinRows.length} khách</div>
            <div class="ci-pager-buttons">
                <button class="ci-page-btn" type="button" data-ci-page="prev" ${checkinPage === 1 ? 'disabled' : ''}>‹</button>
                ${Array.from({ length: totalPages }, (_, index) => {
                    const page = index + 1;
                    return `<button class="ci-page-btn ${page === checkinPage ? 'active' : ''}" type="button" data-ci-page="${page}">${page}</button>`;
                }).join('')}
                <button class="ci-page-btn" type="button" data-ci-page="next" ${checkinPage === totalPages ? 'disabled' : ''}>›</button>
            </div>
        `;
    }

    checkinPager?.addEventListener('click', (event) => {
        const button = event.target.closest('[data-ci-page]');
        if (!button) return;
        const target = button.dataset.ciPage;
        const totalPages = Math.ceil(checkinRows.length / checkinPerPage);
        if (target === 'prev') checkinPage = Math.max(1, checkinPage - 1);
        else if (target === 'next') checkinPage = Math.min(totalPages, checkinPage + 1);
        else checkinPage = Number(target);
        renderCheckinPager();
    });

    renderCheckinPager();
</script>
@endpush

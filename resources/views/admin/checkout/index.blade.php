@extends('layouts.admin')

@section('title', 'Khách ra | Quản lý khách')
@section('page_title', 'Khách ra')
@section('page_subtitle', 'Nhập mã QR hoặc mã lịch hẹn để làm thủ tục khách ra')

@push('styles')
<style>
.co-page{display:grid;gap:1rem}.co-top{display:grid;grid-template-columns:minmax(360px,38%) minmax(0,62%);gap:1rem;align-items:stretch}.co-card{background:#fff;border:1px solid #e1ebf6;border-radius:22px;box-shadow:0 14px 34px rgba(17,39,68,.07);overflow:hidden}.co-head{padding:1rem 1.15rem .85rem;border-bottom:1px solid #edf3fb}.co-head h3{margin:0;color:#0b1f3a;font-family:"Plus Jakarta Sans",sans-serif;font-size:1rem;font-weight:900}.co-head p{margin:.2rem 0 0;color:#6e83a0;font-size:.78rem}.co-scan-box{padding:1rem}.co-scan-frame{position:relative;display:grid;place-items:center;height:230px;border:1px dashed #a8c9eb;border-radius:18px;background:linear-gradient(90deg,rgba(20,107,215,.055) 1px,transparent 1px),linear-gradient(rgba(20,107,215,.055) 1px,transparent 1px),#eef7ff;background-size:24px 24px}.co-scan-frame i{color:#b9d5ef;font-size:3.5rem}.co-corners:before,.co-corners:after,.co-corner-tr,.co-corner-bl{content:"";position:absolute;width:28px;height:28px;border:3px solid #146bd7}.co-corners{position:absolute;inset:0}.co-corners:before{top:12px;left:12px;border-right:0;border-bottom:0;border-radius:4px 0 0}.co-corners:after{right:12px;bottom:12px;border-left:0;border-top:0;border-radius:0 0 4px}.co-corner-tr{top:12px;right:12px;border-left:0;border-bottom:0}.co-corner-bl{left:12px;bottom:12px;border-right:0;border-top:0}.co-reader-copy{position:relative;z-index:1;text-align:center;color:#5a7a99}.co-reader-copy strong{display:block;margin-top:.35rem;color:#0b1f3a;font-size:1rem;font-weight:900}.co-reader-copy span{font-size:.78rem}.co-or{display:flex;align-items:center;gap:.75rem;margin:.85rem 0;color:#7890aa;font-size:.68rem;font-weight:900;text-transform:uppercase}.co-or:before,.co-or:after{content:"";height:1px;flex:1;background:#e7eff8}.co-form{display:grid;gap:.62rem}.co-label{margin:0;color:#0b1f3a;font-size:.84rem;font-weight:900}.co-input-wrap{position:relative}.co-input-wrap i{position:absolute;right:.85rem;top:50%;transform:translateY(-50%);color:#9aafca;pointer-events:none}.co-input-wrap input{width:100%;min-height:46px;padding:.7rem 2.4rem .7rem .9rem;border:1.5px solid #d8e5f2;border-radius:13px;color:#0b1f3a;font-size:.86rem}.co-input-wrap input:focus{outline:0;border-color:#146bd7;box-shadow:0 0 0 4px rgba(20,107,215,.1)}.co-btn-blue,.co-btn-red{display:flex;align-items:center;justify-content:center;gap:.45rem;width:100%;min-height:46px;border:0;border-radius:13px;color:#fff;font-weight:900}.co-btn-blue{background:linear-gradient(135deg,#146bd7,#0cb4d8);box-shadow:0 12px 24px rgba(20,107,215,.18)}.co-btn-red{background:linear-gradient(135deg,#dc2626,#ef4444);box-shadow:0 12px 24px rgba(220,38,38,.18)}
.co-detail-card{display:flex;flex-direction:column;min-height:100%}.co-detail{padding:1rem}.co-state{display:flex;align-items:center;justify-content:space-between;gap:1rem;margin-bottom:1rem;padding:.72rem .85rem;border-radius:16px;background:#ecfdf5;color:#047857;font-size:.82rem;font-weight:900}.co-profile{display:flex;gap:1rem;align-items:center;margin-bottom:1rem}.co-avatar{width:72px;height:72px;border-radius:24px;display:grid;place-items:center;background:#dbeafe;color:#1d4ed8;font-size:1.65rem;font-weight:900}.co-name{margin:0;color:#0b1f3a;font-family:"Plus Jakarta Sans",sans-serif;font-size:1.35rem;font-weight:900}.co-company{color:#5a7a99;font-size:.86rem}.co-detail-grid{display:grid;grid-template-columns:minmax(0,1fr) 280px;gap:1rem}.co-info{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.65rem}.co-info-row{display:grid;grid-template-columns:34px 1fr;gap:.55rem;align-items:center;padding:.72rem;border:1px solid #edf3fb;border-radius:15px;background:#fbfdff}.co-info-row i{width:34px;height:34px;display:grid;place-items:center;border-radius:12px;background:#eff6ff;color:#146bd7}.co-info-row.wide{grid-column:1/-1}.co-lbl{display:block;color:#7086a1;font-size:.7rem;font-weight:900}.co-val{display:block;margin-top:.12rem;color:#0b1f3a;font-size:.84rem;font-weight:900}.co-side{display:grid;gap:.65rem}.co-side-item{padding:.82rem;border:1px solid #edf3fb;border-radius:15px;background:#fbfdff}.co-side-item span{display:block;color:#7086a1;font-size:.7rem;font-weight:900}.co-side-item strong{display:block;margin-top:.18rem;color:#0b1f3a;font-size:.95rem}.co-side-item.warning{background:#fff7ed;border-color:#fed7aa}.co-side-item.warning strong{color:#c2410c}.co-actions{display:grid;grid-template-columns:1fr 190px;gap:.65rem;margin-top:1rem}.co-btn-cancel{min-height:46px;border:1px solid #d8e5f2;border-radius:13px;background:#fff;color:#334b67;font-weight:900}.co-empty{flex:1;min-height:0;display:grid;place-items:center;text-align:center;color:#8aa0ba;padding:1.5rem}.co-empty i{font-size:3rem;color:#c5dbf2}.co-empty strong{display:block;margin:.5rem 0 .25rem;color:#526b87;font-size:1rem}.co-empty p{max-width:540px;margin:0 auto .7rem;line-height:1.55}.co-empty-hint{display:inline-flex;align-items:center;gap:.38rem;padding:.48rem .75rem;border-radius:999px;background:#eff6ff;color:#146bd7;font-size:.78rem;font-weight:900}
.co-list-card{min-height:430px}.co-list-head{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:1rem 1.15rem;border-bottom:1px solid #edf3fb}.co-list-head h3{margin:0;color:#0b1f3a;font-family:"Plus Jakarta Sans",sans-serif;font-size:1rem;font-weight:900}.co-list-head p{margin:.18rem 0 0;color:#6e83a0;font-size:.78rem}.co-filter{display:flex;gap:.55rem}.co-filter .form-control,.co-filter .form-select{min-height:38px;border-color:#d8e5f2;border-radius:11px;font-size:.78rem}.co-pill{display:inline-flex;align-items:center;gap:.28rem;padding:.25rem .68rem;border-radius:999px;background:#dbeafe;color:#1d4ed8;font-size:.72rem;font-weight:900}.co-row,.co-row-head{display:grid;grid-template-columns:minmax(180px,1.1fr) minmax(170px,1fr) 130px 100px 130px 130px 100px;gap:.8rem;align-items:center}.co-row-head{padding:.85rem 1.15rem .55rem;color:#7086a1;font-size:.7rem;font-weight:900;text-transform:uppercase;border-bottom:1px solid #edf3fb}.co-row{width:100%;padding:.82rem 1.15rem;border:0;border-bottom:1px solid #f0f5fb;background:#fff;text-align:left;cursor:pointer}.co-row:hover{background:#f2f8ff}.co-vname{color:#0b1f3a;font-size:.88rem;font-weight:900}.co-muted{color:#7890aa;font-size:.74rem}.co-remain{color:#f97316;font-size:.78rem;font-weight:900}.co-action-btn{display:inline-flex;align-items:center;justify-content:center;padding:.35rem .68rem;border:1px solid #bfd7f3;border-radius:999px;background:#fff;color:#146bd7;font-size:.72rem;font-weight:900}.co-status{display:inline-flex;align-items:center;gap:.3rem;width:max-content;padding:.3rem .62rem;border-radius:999px;font-size:.72rem;font-weight:900}.co-status-ok{background:#dcfce7;color:#047857}.co-status-late{background:#ffedd5;color:#c2410c}.co-list-empty{padding:2.5rem;text-align:center;color:#8aa0ba}.co-pager{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:.85rem 1.15rem;border-top:1px solid #edf3fb;background:#fbfdff}.co-pager-info{color:#7086a1;font-size:.78rem;font-weight:800}.co-pager-buttons{display:flex;align-items:center;gap:.35rem}.co-page-btn{min-width:34px;height:34px;border:1px solid #d8e5f2;border-radius:10px;background:#fff;color:#315b89;font-size:.78rem;font-weight:900}.co-page-btn:hover{background:#eff6ff;border-color:#b9d5ef}.co-page-btn.active{border-color:#146bd7;background:#146bd7;color:#fff}.co-page-btn:disabled{opacity:.45;cursor:not-allowed}
@media(max-width:1280px){.co-top{grid-template-columns:1fr}.co-detail-grid{grid-template-columns:1fr}.co-info{grid-template-columns:1fr}.co-row-head{display:none}.co-row{grid-template-columns:1fr;gap:.35rem}}@media(max-width:768px){.co-actions{grid-template-columns:1fr}.co-list-head{align-items:flex-start;flex-direction:column}.co-filter{width:100%;flex-direction:column}.co-scan-frame{height:190px}.co-pager{align-items:flex-start;flex-direction:column}}
</style>
@endpush

@section('content')
<div class="co-page">
    <div class="co-top">
        <section class="co-card">
            <div class="co-head">
                <h3>Quét mã khách ra</h3>
                <p>Dùng thiết bị đọc QR tại quầy hoặc nhập mã lịch hẹn thủ công.</p>
            </div>
            <div class="co-scan-box">
                <div class="co-scan-frame">
                    <div class="co-corners"></div><div class="co-corner-tr"></div><div class="co-corner-bl"></div>
                    <div class="co-reader-copy">
                        <i class="bi bi-upc-scan"></i>
                        <strong>Thiết bị đọc QR</strong>
                        <span>Đưa mã QR của khách vào đầu đọc</span>
                    </div>
                </div>
                <div class="co-or">Nhập thủ công</div>
                <form class="co-form" id="checkoutQrForm" action="{{ route('admin.checkout.scan-qr') }}" method="post">
                    @csrf
                    <label class="co-label">Nhập mã lịch hẹn / mã QR</label>
                    <div class="co-input-wrap">
                        <input id="checkoutCodeInput" type="text" name="qr_token" value="{{ old('qr_token') }}" placeholder="VD: VO-260529-001" autocomplete="off" autofocus>
                        <i class="bi bi-upc-scan"></i>
                    </div>
                    <button class="co-btn-blue" type="submit"><i class="bi bi-search"></i> Kiểm tra thông tin</button>
                </form>
            </div>
        </section>

        <section class="co-card co-detail-card">
            @if ($scannedVisit)
                @php
                    $isOverstay = $scannedVisit->expected_checkout_at?->lt(now()) ?? false;
                    $remaining = '-';
                    if ($scannedVisit->expected_checkout_at) {
                        $minutes = max(0, (int) now()->diffInMinutes($scannedVisit->expected_checkout_at));
                        $remaining = $minutes < 60
                            ? $minutes.' phút'
                            : intdiv($minutes, 60).' giờ'.($minutes % 60 > 0 ? ' '.($minutes % 60).' phút' : '');
                    }
                @endphp
                <div class="co-detail">
                    <div class="co-state">
                        <span><i class="bi bi-check-circle-fill"></i> Đã tìm thấy khách đang trong công ty.</span>
                        <x-status-badge :status="$scannedVisit->status" />
                    </div>
                    <div class="co-profile">
                        <div class="co-avatar">{{ strtoupper(mb_substr($scannedVisit->visitor?->full_name ?? 'K', 0, 1)) }}</div>
                        <div>
                            <h3 class="co-name">{{ $scannedVisit->visitor?->full_name ?? '-' }}</h3>
                            <div class="co-company">{{ $scannedVisit->visitor?->company ?? 'Khách vãng lai' }}</div>
                        </div>
                    </div>
                    <div class="co-detail-grid">
                        <div class="co-info">
                            <div class="co-info-row"><i class="bi bi-person"></i><div><span class="co-lbl">Người cần gặp</span><span class="co-val">{{ $scannedVisit->hostEmployee?->name ?? '-' }}</span></div></div>
                            <div class="co-info-row"><i class="bi bi-building"></i><div><span class="co-lbl">Phòng ban</span><span class="co-val">{{ $scannedVisit->hostEmployee?->department?->name ?? '-' }}</span></div></div>
                            <div class="co-info-row"><i class="bi bi-clock"></i><div><span class="co-lbl">Vào lúc</span><span class="co-val">{{ $scannedVisit->actual_checkin_at?->format('H:i - d/m/Y') ?? '-' }}</span></div></div>
                            <div class="co-info-row"><i class="bi bi-calendar-check"></i><div><span class="co-lbl">Giờ hẹn</span><span class="co-val">{{ $scannedVisit->scheduled_at?->format('H:i - d/m/Y') ?? '-' }}</span></div></div>
                            <div class="co-info-row"><i class="bi bi-telephone"></i><div><span class="co-lbl">Số điện thoại</span><span class="co-val">{{ $scannedVisit->visitor?->phone ?? '-' }}</span></div></div>
                            <div class="co-info-row"><i class="bi bi-envelope"></i><div><span class="co-lbl">Email</span><span class="co-val">{{ $scannedVisit->visitor?->email ?? '-' }}</span></div></div>
                            <div class="co-info-row wide"><i class="bi bi-chat-square-text"></i><div><span class="co-lbl">Mục đích đến</span><span class="co-val">{{ $scannedVisit->purpose ?? '-' }}</span></div></div>
                        </div>
                        <div class="co-side">
                            <div class="co-side-item"><span>Mã lịch hẹn</span><strong>{{ $scannedVisit->code }}</strong></div>
                            <div class="co-side-item"><span>Trạng thái hiện tại</span><strong>Đang trong công ty</strong></div>
                            <div class="co-side-item {{ $isOverstay ? 'warning' : '' }}"><span>{{ $isOverstay ? 'Thời gian quá giờ' : 'Thời gian còn lại' }}</span><strong>{{ $remaining }}</strong></div>
                            <div class="co-side-item"><span>Thẻ ra vào</span><strong>{{ $scannedVisit->activeBadge?->badge_no ?? 'Chưa cấp thẻ' }}</strong></div>
                        </div>
                    </div>
                    <div class="co-actions">
                        <form action="{{ route('admin.checkout.confirm', $scannedVisit) }}" method="post">
                            @csrf
                            <button class="co-btn-red" type="submit"><i class="bi bi-box-arrow-left"></i> Xác nhận khách ra</button>
                        </form>
                        <a class="co-btn-cancel d-flex align-items-center justify-content-center text-decoration-none" href="{{ route('admin.checkout.index') }}">Hủy thao tác</a>
                    </div>
                </div>
            @else
                <div class="co-empty">
                    <div>
                        <i class="bi bi-person-bounding-box"></i>
                        <strong>Chưa có khách được chọn</strong>
                        <p>Quét QR hoặc nhập mã lịch hẹn để hiển thị thông tin khách cần làm thủ tục ra.</p>
                        <span class="co-empty-hint"><i class="bi bi-info-circle"></i> Chỉ khách đang trong công ty mới được làm thủ tục ra.</span>
                    </div>
                </div>
            @endif
        </section>
    </div>

    <section class="co-card co-list-card">
        <div class="co-list-head">
            <div>
                <h3>Khách đang trong công ty</h3>
                <p>Danh sách khách đã vào nhưng chưa làm thủ tục ra.</p>
            </div>
            <div class="co-filter">
                <input class="form-control" id="checkoutListSearch" placeholder="Tìm kiếm khách...">
                <select class="form-select" id="checkoutDepartmentFilter"><option value="">Tất cả phòng ban</option></select>
            </div>
        </div>

        @if (count($insideVisits) > 0)
            <div class="co-row-head">
                <span>Khách</span>
                <span>Người cần gặp</span>
                <span>Phòng ban</span>
                <span>Vào lúc</span>
                <span>Thời gian ở lại</span>
                <span>Trạng thái</span>
                <span>Thao tác</span>
            </div>
        @endif

        @forelse ($insideVisits as $visit)
            <button class="co-row" type="button" data-checkout-code="{{ $visit['code'] }}" data-checkout-row data-department="{{ $visit['department'] }}" data-search="{{ strtolower($visit['code'].' '.$visit['visitor'].' '.$visit['company'].' '.$visit['host'].' '.$visit['department']) }}">
                <div><div class="co-vname">{{ $visit['visitor'] }}</div><div class="co-muted">{{ $visit['company'] }}</div></div>
                <span>{{ $visit['host'] }}</span>
                <span>{{ $visit['department'] }}</span>
                <span>{{ $visit['checkin_time'] }}</span>
                <span class="co-remain">{{ $visit['remaining'] }}</span>
                <span>
                    @if ($visit['is_overstay'])
                        <span class="co-status co-status-late"><i class="bi bi-exclamation-triangle"></i> Quá giờ</span>
                    @else
                        <span class="co-status co-status-ok"><i class="bi bi-check-circle"></i> Đang trong công ty</span>
                    @endif
                </span>
                <span><span class="co-action-btn">Khách ra</span></span>
            </button>
        @empty
            <div class="co-list-empty">
                <i class="bi bi-building-check d-block fs-2 mb-2"></i>
                Không có khách đang trong công ty.
            </div>
        @endforelse

        @if (count($insideVisits) > 0)
            <div class="co-pager" id="checkoutListPager"></div>
        @endif
    </section>
</div>
@endsection

@push('scripts')
<script>
    const checkoutInput = document.getElementById('checkoutCodeInput');
    const checkoutForm = document.getElementById('checkoutQrForm');
    let checkoutSubmitTimer = null;
    let checkoutSubmitted = false;

    function submitCheckoutLookup() {
        if (!checkoutForm || !checkoutInput || checkoutSubmitted) return;

        const value = checkoutInput.value.trim();
        if (!value) return;

        checkoutSubmitted = true;
        checkoutForm.requestSubmit();
    }

    function looksLikeCompleteCheckoutCode(value) {
        const normalized = value.trim();

        return /^\d{8}$/.test(normalized)
            || /^(WK|VO|RP)-[A-Z0-9-]{6,}$/i.test(normalized);
    }

    function scheduleAutoCheckoutLookup() {
        clearTimeout(checkoutSubmitTimer);

        const value = checkoutInput?.value.trim() || '';
        if (!looksLikeCompleteCheckoutCode(value)) return;

        checkoutSubmitTimer = setTimeout(submitCheckoutLookup, 280);
    }

    if (checkoutInput) {
        checkoutInput.focus();
        checkoutInput.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' && checkoutInput.value.trim() !== '') {
                event.preventDefault();
                submitCheckoutLookup();
            }
        });
        checkoutInput.addEventListener('input', scheduleAutoCheckoutLookup);
        checkoutInput.addEventListener('paste', () => setTimeout(scheduleAutoCheckoutLookup, 0));
    }

    document.querySelectorAll('[data-checkout-code]').forEach((row) => {
        row.addEventListener('click', () => {
            if (!checkoutInput) return;
            checkoutInput.value = row.dataset.checkoutCode || '';
            checkoutInput.focus();
            checkoutInput.select();
            window.scrollTo({ top: 0, behavior: 'smooth' });
            scheduleAutoCheckoutLookup();
        });
    });

    const checkoutRows = Array.from(document.querySelectorAll('[data-checkout-row]'));
    const checkoutPager = document.getElementById('checkoutListPager');
    const checkoutSearch = document.getElementById('checkoutListSearch');
    const checkoutDepartment = document.getElementById('checkoutDepartmentFilter');
    const checkoutPerPage = 5;
    let checkoutPage = 1;

    if (checkoutDepartment && checkoutRows.length > 0) {
        const departments = [...new Set(checkoutRows.map((row) => row.dataset.department).filter(Boolean))].sort();
        departments.forEach((department) => {
            const option = document.createElement('option');
            option.value = department;
            option.textContent = department;
            checkoutDepartment.appendChild(option);
        });
    }

    function getFilteredCheckoutRows() {
        const keyword = (checkoutSearch?.value || '').trim().toLowerCase();
        const department = checkoutDepartment?.value || '';
        return checkoutRows.filter((row) => {
            const matchKeyword = !keyword || (row.dataset.search || '').includes(keyword);
            const matchDepartment = !department || row.dataset.department === department;
            return matchKeyword && matchDepartment;
        });
    }

    function renderCheckoutPager() {
        if (!checkoutPager) return;
        const filteredRows = getFilteredCheckoutRows();
        const totalRows = filteredRows.length;
        const totalPages = Math.max(1, Math.ceil(totalRows / checkoutPerPage));
        checkoutPage = Math.min(checkoutPage, totalPages);
        const start = (checkoutPage - 1) * checkoutPerPage;
        const end = start + checkoutPerPage;

        checkoutRows.forEach((row) => row.style.display = 'none');
        filteredRows.forEach((row, index) => {
            row.style.display = index >= start && index < end ? '' : 'none';
        });

        if (checkoutRows.length <= checkoutPerPage && !checkoutSearch?.value && !checkoutDepartment?.value) {
            checkoutPager.style.display = 'none';
            return;
        }

        checkoutPager.style.display = 'flex';
        checkoutPager.innerHTML = `
            <div class="co-pager-info">${totalRows > 0 ? `Hiển thị ${start + 1} - ${Math.min(end, totalRows)} trong ${totalRows} khách` : 'Không có khách phù hợp'}</div>
            <div class="co-pager-buttons">
                <button class="co-page-btn" type="button" data-co-page="prev" ${checkoutPage === 1 ? 'disabled' : ''}>‹</button>
                ${Array.from({ length: totalPages }, (_, index) => {
                    const page = index + 1;
                    return `<button class="co-page-btn ${page === checkoutPage ? 'active' : ''}" type="button" data-co-page="${page}">${page}</button>`;
                }).join('')}
                <button class="co-page-btn" type="button" data-co-page="next" ${checkoutPage === totalPages ? 'disabled' : ''}>›</button>
            </div>
        `;
    }

    checkoutPager?.addEventListener('click', (event) => {
        const button = event.target.closest('[data-co-page]');
        if (!button) return;
        const target = button.dataset.coPage;
        const totalPages = Math.max(1, Math.ceil(getFilteredCheckoutRows().length / checkoutPerPage));
        if (target === 'prev') checkoutPage = Math.max(1, checkoutPage - 1);
        else if (target === 'next') checkoutPage = Math.min(totalPages, checkoutPage + 1);
        else checkoutPage = Number(target);
        renderCheckoutPager();
    });

    [checkoutSearch, checkoutDepartment].forEach((control) => {
        control?.addEventListener('input', () => {
            checkoutPage = 1;
            renderCheckoutPager();
        });
        control?.addEventListener('change', () => {
            checkoutPage = 1;
            renderCheckoutPager();
        });
    });

    renderCheckoutPager();
</script>
@endpush

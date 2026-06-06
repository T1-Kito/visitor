@extends('layouts.admin')

@section('title', 'Khách ra/vào')
@section('page_title', 'Khách ra/vào')
@section('page_subtitle', 'Quét mã và xử lý check-in/check-out trên cùng một màn hình')

@push('styles')
<style>
.access-page{display:grid;gap:.9rem;color:#10233d}.access-hero{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:.85rem 1rem;border:1px solid #e2edf8;border-radius:20px;background:rgba(255,255,255,.82);box-shadow:0 12px 30px rgba(17,39,68,.05)}.access-title{display:grid;gap:.15rem}.access-title h3{margin:0;font-size:1.05rem;font-weight:600;letter-spacing:0;color:#10233d}.access-title p{margin:0;color:#6f839f;font-size:.82rem}.access-tabs{display:inline-grid;grid-template-columns:repeat(2,minmax(132px,1fr));gap:.32rem;padding:.28rem;border:1px solid #d9e6f4;border-radius:16px;background:#f7fbff}.access-tab{min-height:40px;display:inline-flex;align-items:center;justify-content:center;gap:.45rem;border:0;border-radius:12px;background:transparent;color:#49627f;font-size:.86rem;font-weight:500;text-decoration:none}.access-tab.is-active{background:linear-gradient(135deg,#1976d2,#11a9c7);color:#fff;box-shadow:0 10px 20px rgba(20,107,215,.15)}.access-stats{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:.65rem}.access-stat{position:relative;overflow:hidden;padding:.8rem .9rem;border:1px solid #e2edf8;border-radius:16px;background:#fff;box-shadow:0 10px 22px rgba(17,39,68,.035)}.access-stat::after{content:"";position:absolute;right:-18px;top:-22px;width:72px;height:72px;border-radius:999px;background:var(--stat-glow,rgba(255,255,255,.45))}.access-stat span,.access-stat strong{position:relative;z-index:1}.access-stat span{display:block;color:#667d98;font-size:.72rem}.access-stat strong{display:block;margin-top:.12rem;color:#10233d;font-size:1.05rem;font-weight:600}.access-stat.stat-waiting{border-color:#d8e9ff;background:linear-gradient(135deg,#f7fbff,#edf6ff);--stat-glow:rgba(125,181,255,.22)}.access-stat.stat-inside{border-color:#d9f0e5;background:linear-gradient(135deg,#f7fffb,#edf9f3);--stat-glow:rgba(86,190,132,.2)}.access-stat.stat-in{border-color:#e6defb;background:linear-gradient(135deg,#fbf9ff,#f2edff);--stat-glow:rgba(157,124,231,.18)}.access-stat.stat-out{border-color:#fde8d4;background:linear-gradient(135deg,#fffaf5,#fff1e5);--stat-glow:rgba(245,158,87,.18)}.access-stat.stat-late{border-color:#f8dce2;background:linear-gradient(135deg,#fff8fa,#ffeff3);--stat-glow:rgba(244,114,139,.16)}.access-stat.stat-waiting strong{color:#125fa8}.access-stat.stat-inside strong{color:#0f7a4d}.access-stat.stat-in strong{color:#6d4fc2}.access-stat.stat-out strong{color:#b85b1f}.access-stat.stat-late strong{color:#b42345}.access-pane{display:none}.access-pane.is-active{display:grid;gap:.9rem}.access-workspace{display:grid;grid-template-columns:minmax(320px,38%) minmax(0,62%);gap:.9rem;align-items:stretch}.access-card{min-width:0;border:1px solid #e2edf8;border-radius:20px;background:#fff;box-shadow:0 12px 30px rgba(17,39,68,.05);overflow:hidden}.access-card-head{display:flex;align-items:flex-start;justify-content:space-between;gap:.8rem;padding:.9rem 1rem;border-bottom:1px solid #eef4fb}.access-card-head h3{margin:0;color:#10233d;font-size:.98rem;font-weight:600;letter-spacing:0}.access-card-head p{margin:.18rem 0 0;color:#7187a3;font-size:.78rem}.access-body{padding:1rem}.access-scan{display:grid;gap:.85rem}.access-frame{position:relative;height:190px;display:grid;place-items:center;border:1px dashed #aacbeb;border-radius:18px;background:linear-gradient(90deg,rgba(20,107,215,.055) 1px,transparent 1px),linear-gradient(rgba(20,107,215,.055) 1px,transparent 1px),#f2f8ff;background-size:24px 24px;color:#7aa7d5}.access-frame-copy{text-align:center}.access-frame-copy i{font-size:2.4rem;color:#b7d1ed}.access-frame-copy strong{display:block;margin-top:.25rem;color:#253a54;font-size:.92rem;font-weight:600}.access-frame-copy span{display:block;margin-top:.12rem;color:#6f839f;font-size:.76rem}.access-corner{position:absolute;width:24px;height:24px;border:2px solid #1976d2}.access-corner.tl{top:12px;left:12px;border-right:0;border-bottom:0}.access-corner.tr{top:12px;right:12px;border-left:0;border-bottom:0}.access-corner.bl{bottom:12px;left:12px;border-right:0;border-top:0}.access-corner.br{bottom:12px;right:12px;border-left:0;border-top:0}.access-form{display:grid;grid-template-columns:minmax(0,1fr) 150px;gap:.58rem;align-items:center}.access-input-wrap{position:relative}.access-input-wrap input{width:100%;min-height:46px;padding:.68rem 2.35rem .68rem .85rem;border:1px solid #d8e5f2;border-radius:13px;color:#10233d;font-size:.86rem;font-weight:400}.access-input-wrap input:focus{outline:0;border-color:#1976d2;box-shadow:0 0 0 4px rgba(25,118,210,.1)}.access-input-wrap i{position:absolute;right:.82rem;top:50%;transform:translateY(-50%);color:#9aadbf}.access-btn{min-height:46px;display:inline-flex;align-items:center;justify-content:center;gap:.45rem;border:1px solid #d6e5f4;border-radius:13px;background:#fff;color:#30506f;font-size:.86rem;font-weight:500;text-decoration:none;white-space:nowrap}.access-btn.primary{border:0;color:#fff;background:linear-gradient(135deg,#1976d2,#11a9c7);box-shadow:0 12px 24px rgba(20,107,215,.14)}.access-btn.success{border:0;color:#fff;background:linear-gradient(135deg,#16a34a,#22c55e)}.access-btn.danger{border:0;color:#fff;background:linear-gradient(135deg,#dc2626,#f97316)}.access-detail{display:flex;flex-direction:column;min-height:100%}.access-empty{flex:1;display:grid;place-items:center;padding:1.5rem;text-align:center;color:#879ab2}.access-empty i{font-size:2.7rem;color:#bdd5ee}.access-empty strong{display:block;margin:.5rem 0 .18rem;color:#526b87;font-size:.95rem;font-weight:600}.access-empty p{max-width:420px;margin:0 auto;font-size:.86rem;line-height:1.5}.access-profile{display:flex;align-items:center;gap:.85rem;padding:.95rem 1rem;border-bottom:1px solid #eef4fb}.access-avatar{width:58px;height:58px;display:grid;place-items:center;border-radius:18px;background:#e7f0ff;color:#1976d2;font-size:1.25rem;font-weight:600}.access-name{margin:0;color:#10233d;font-size:1.08rem;font-weight:600}.access-company{color:#6f839f;font-size:.82rem}.access-info-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.58rem;padding:.85rem 1rem}.access-info{display:grid;grid-template-columns:30px 1fr;gap:.5rem;align-items:center;padding:.62rem;border:1px solid #edf3fb;border-radius:14px;background:#fbfdff}.access-info i{width:30px;height:30px;display:grid;place-items:center;border-radius:10px;background:#edf6ff;color:#1976d2}.access-label{display:block;color:#7187a3;font-size:.68rem;font-weight:500}.access-value{display:block;margin-top:.08rem;color:#10233d;font-size:.8rem;font-weight:500;overflow-wrap:anywhere}.access-meta{display:flex;align-items:center;justify-content:space-between;gap:.8rem;margin:0 1rem;padding:.72rem;border:1px solid #edf3fb;border-radius:14px;background:#f8fbff;color:#526b87;font-size:.84rem}.access-meta strong{color:#10233d;font-weight:600}.access-action-area{padding:1rem}.access-notice{margin:0;padding:.75rem;border:1px solid #fed7aa;border-radius:14px;background:#fff7ed;color:#9a3412;font-size:.82rem;font-weight:500}.access-notice.info{border-color:#bfdbfe;background:#eff6ff;color:#1d4ed8}.access-notice.danger{border-color:#fecaca;background:#fff1f2;color:#be123c}.access-list{overflow:hidden}.access-list-head{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:.9rem 1rem;border-bottom:1px solid #eef4fb}.access-list-head h3{margin:0;color:#10233d;font-size:.98rem;font-weight:600}.access-list-head p{margin:.12rem 0 0;color:#7187a3;font-size:.76rem}.access-pill{display:inline-flex;align-items:center;gap:.25rem;padding:.22rem .58rem;border-radius:999px;background:#eaf3ff;color:#1976d2;font-size:.72rem;font-weight:500}.access-table{display:grid}.access-row,.access-row-head{display:grid;gap:.72rem;align-items:center}.access-row-head{padding:.72rem 1rem;color:#7187a3;font-size:.68rem;font-weight:600;text-transform:uppercase;border-bottom:1px solid #eef4fb}.access-row{width:100%;padding:.78rem 1rem;border:0;border-bottom:1px solid #f1f5fa;background:#fff;text-align:left;cursor:pointer;color:#10233d}.access-row:hover{background:#f6fbff}.access-code{color:#1976d2;font-weight:600}.access-main-text{font-size:.86rem;font-weight:500}.access-muted{display:block;color:#7d91a9;font-size:.72rem;font-weight:400}.access-tag{display:inline-flex;width:max-content;padding:.22rem .55rem;border-radius:999px;background:#eff6ff;color:#315b89;font-size:.72rem;font-weight:500}.access-ok{display:inline-flex;align-items:center;gap:.25rem;width:max-content;padding:.24rem .52rem;border-radius:999px;background:#ecfdf5;color:#047857;font-size:.72rem;font-weight:500}.access-late{background:#fff7ed;color:#c2410c}.checkin-grid .access-row,.checkin-grid .access-row-head{grid-template-columns:130px 92px minmax(160px,1fr) minmax(150px,1fr) 120px 120px}.checkout-grid .access-row,.checkout-grid .access-row-head{grid-template-columns:minmax(180px,1.2fr) minmax(150px,1fr) 120px 86px 100px 140px 110px}.access-list-empty{padding:2rem;text-align:center;color:#879ab2}.access-tools{display:flex;gap:.45rem;align-items:center}.access-tools input,.access-tools select{min-height:36px;border:1px solid #d8e5f2;border-radius:11px;color:#526b87;font-size:.78rem}@media(max-width:1300px){.access-workspace{grid-template-columns:1fr}.access-stats{grid-template-columns:repeat(3,minmax(0,1fr))}.access-row-head{display:none}.access-row{grid-template-columns:1fr!important;gap:.28rem}.access-tools{display:none}}@media(max-width:768px){.access-hero{align-items:stretch;flex-direction:column}.access-tabs{width:100%}.access-stats{grid-template-columns:1fr 1fr}.access-info-grid{grid-template-columns:1fr}.access-form{grid-template-columns:1fr}}
.access-workspace.has-result{grid-template-columns:1fr}.access-detail.is-result .access-card-head{align-items:center}.access-detail.is-result .access-info-grid{grid-template-columns:repeat(3,minmax(0,1fr))}.access-result-actions{display:flex;align-items:center;justify-content:flex-end;gap:.5rem;flex-wrap:wrap}.access-result-link{min-height:38px;display:inline-flex;align-items:center;justify-content:center;gap:.4rem;padding:0 .75rem;border:1px solid #d7e5f4;border-radius:12px;background:#fff;color:#315b89;text-decoration:none;font-size:.78rem;font-weight:500}.access-result-link:hover{color:#1976d2;background:#f7fbff}.access-result-link.primary{border:0;color:#fff;background:linear-gradient(135deg,#1976d2,#11a9c7);box-shadow:0 10px 18px rgba(20,107,215,.12)}.access-scanned-code{display:inline-flex;align-items:center;gap:.35rem;margin-top:.3rem;padding:.22rem .5rem;border-radius:999px;background:#f4f8fd;color:#607894;font-size:.72rem}.access-scanned-code strong{color:#10233d;font-weight:600}@media(max-width:1300px){.access-detail.is-result .access-info-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:768px){.access-detail.is-result .access-card-head{align-items:flex-start;flex-direction:column}.access-result-actions{width:100%;justify-content:stretch}.access-result-link{flex:1}.access-detail.is-result .access-info-grid{grid-template-columns:1fr}}
.access-result-scan{display:grid;grid-template-columns:minmax(0,1fr) 118px;gap:.42rem;margin:.62rem 1rem 0;padding:.42rem;border:1px solid #dce9f7;border-radius:12px;background:#f8fbff}.access-result-scan .access-input-wrap input{min-height:36px;padding:.48rem 2rem .48rem .7rem;border-radius:10px;background:#fff;font-size:.78rem}.access-result-scan .access-input-wrap i{right:.65rem;font-size:.8rem}.access-result-scan .access-btn{min-height:36px;border-radius:10px;font-size:.78rem}.access-result-hint{display:flex;align-items:center;gap:.35rem;margin:.35rem 1rem 0;color:#7187a3;font-size:.7rem}.access-result-hint i{color:#1976d2;font-size:.78rem}@media(max-width:768px){.access-result-scan{grid-template-columns:1fr}}
.access-history{border:1px solid #e2edf8;border-radius:20px;background:#fff;box-shadow:0 12px 30px rgba(17,39,68,.05);overflow:hidden}.access-history-head{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:.9rem 1rem;border-bottom:1px solid #eef4fb}.access-history-title h3{margin:0;color:#10233d;font-size:.98rem;font-weight:600}.access-history-title p{margin:.14rem 0 0;color:#7187a3;font-size:.76rem}.access-history-filter{display:flex;align-items:center;gap:.45rem;flex-wrap:wrap}.access-history-filter input,.access-history-filter select{min-height:36px;border:1px solid #d8e5f2;border-radius:11px;color:#526b87;font-size:.78rem}.access-history-filter button{min-height:36px;display:inline-flex;align-items:center;gap:.35rem;border:0;border-radius:11px;padding:0 .72rem;background:#eff6ff;color:#1976d2;font-size:.78rem;font-weight:500}.access-history-list{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:.65rem;padding:1rem}.access-history-item{display:grid;gap:.5rem;padding:.75rem;border:1px solid #edf3fb;border-radius:16px;background:linear-gradient(135deg,#fff,#f8fbff);text-decoration:none;color:#10233d}.access-history-item:hover{border-color:#b8d5f2;background:#f7fbff}.access-history-top{display:flex;align-items:center;justify-content:space-between;gap:.6rem}.access-history-code{color:#1976d2;font-size:.82rem;font-weight:600}.access-history-time{color:#10233d;font-size:.9rem;font-weight:600}.access-history-person{display:flex;align-items:center;gap:.55rem}.access-history-avatar{width:34px;height:34px;display:grid;place-items:center;border-radius:12px;background:#edf6ff;color:#1976d2;font-weight:600}.access-history-name{font-size:.86rem;font-weight:600}.access-history-company,.access-history-meta{color:#7187a3;font-size:.72rem}.access-history-badge{display:inline-flex;width:max-content;align-items:center;gap:.3rem;padding:.22rem .55rem;border-radius:999px;font-size:.72rem;font-weight:500}.access-history-badge.in{background:#ecfdf5;color:#047857}.access-history-badge.out{background:#fff7ed;color:#c2410c}.access-history-empty{padding:1.2rem;text-align:center;color:#879ab2;font-size:.84rem}@media(max-width:1300px){.access-history-list{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:768px){.access-history-head{align-items:flex-start;flex-direction:column}.access-history-filter{width:100%}.access-history-filter input,.access-history-filter select,.access-history-filter button{width:100%}.access-history-list{grid-template-columns:1fr}}
.access-stat{text-decoration:none}.access-stat:hover{transform:translateY(-1px);border-color:#bdd8f4}.access-history-close{min-height:36px;display:inline-flex;align-items:center;gap:.35rem;padding:0 .72rem;border:1px solid #d8e5f2;border-radius:11px;background:#fff;color:#526b87;text-decoration:none;font-size:.78rem;font-weight:500}.access-history-list{display:grid;grid-template-columns:1fr;gap:0;padding:0}.access-history-item{display:grid;grid-template-columns:110px 92px minmax(220px,1.2fr) minmax(180px,1fr) 120px;gap:.75rem;align-items:center;padding:.72rem 1rem;border:0;border-bottom:1px solid #eef4fb;border-radius:0;background:#fff;text-decoration:none;color:#10233d}.access-history-item:hover{background:#f7fbff}.access-history-top{display:contents}.access-history-person{min-width:0}.access-history-meta{font-size:.78rem}.access-history-badge{justify-self:start}@media(max-width:1100px){.access-history-item{grid-template-columns:1fr;gap:.35rem}.access-history-top{display:flex}.access-history-badge{justify-self:start}}@media(max-width:768px){.access-history-filter input,.access-history-filter button,.access-history-close{width:100%;justify-content:center}}
.access-hero-actions{display:flex;align-items:center;gap:.5rem}.access-settings-btn{width:42px;height:42px;display:grid;place-items:center;flex:0 0 42px;border:1px solid #d8e6f4;border-radius:13px;background:#fff;color:#42617f;font-size:1rem;transition:.18s ease}.access-settings-btn:hover{border-color:#9fc6ec;background:#edf6ff;color:#1976d2;transform:rotate(12deg)}.access-settings-btn:focus-visible{outline:0;box-shadow:0 0 0 4px rgba(25,118,210,.14)}.access-settings-modal .modal-content{border:0;border-radius:22px;box-shadow:0 24px 70px rgba(16,35,61,.2)}.access-settings-modal .modal-header{padding:1rem 1.15rem;border-bottom:1px solid #edf3fa}.access-settings-modal .modal-title{font-size:1rem;font-weight:600;color:#10233d}.access-settings-modal .modal-body{display:grid;gap:.85rem;padding:1rem 1.15rem}.access-setting-row{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:.72rem .8rem;border:1px solid #e5edf7;border-radius:15px;background:#fbfdff}.access-setting-copy label{display:block;margin:0;color:#203852;font-size:.86rem;font-weight:500}.access-setting-copy span{display:block;margin-top:.12rem;color:#788ba3;font-size:.72rem}.access-minutes{width:112px}.access-minutes .input-group-text,.access-minutes .form-control{min-height:38px;border-color:#d9e6f3;font-size:.78rem}.access-warning-field label{margin-bottom:.35rem;color:#203852;font-size:.82rem;font-weight:500}.access-warning-field textarea{min-height:82px;border-color:#d9e6f3;border-radius:13px;font-size:.82rem;resize:vertical}.access-settings-modal .modal-footer{padding:.8rem 1.15rem;border-top:1px solid #edf3fa}.access-settings-save{min-height:40px;padding:0 1rem;border:0;border-radius:12px;background:linear-gradient(135deg,#1976d2,#11a9c7);color:#fff;font-size:.82rem;font-weight:500}@media(max-width:768px){.access-hero-actions{width:100%}.access-tabs{flex:1}.access-setting-row{align-items:flex-start}.access-minutes{width:100px}}
</style>
@endpush

@section('content')
@php
    $isCheckin = $activeMode === 'checkin';
    $checkinRows = $readyToCheckin ?? [];
    $checkoutRows = $insideVisits ?? [];
    $allowEarlyCheckin = ($accessSettings['access.allow_early_checkin'] ?? '1') === '1';
    $allowLateCheckin = ($accessSettings['access.allow_late_checkin'] ?? '1') === '1';
    $warningEnabled = ($accessSettings['access.warning_enabled'] ?? '1') === '1';
@endphp

<div class="access-page" data-active-mode="{{ $activeMode }}">
    <section class="access-hero">
        <div class="access-title">
            <h3>Điều phối khách ra/vào</h3>
            <p>Chọn tác vụ, quét QR hoặc nhập mã lịch hẹn để xử lý tại quầy.</p>
        </div>
        <div class="access-hero-actions">
            <div class="access-tabs" role="tablist" aria-label="Tác vụ ra vào">
                <button class="access-tab {{ $isCheckin ? 'is-active' : '' }}" type="button" data-access-tab="checkin">
                    <i class="bi bi-box-arrow-in-right"></i> Check-in
                </button>
                <button class="access-tab {{ ! $isCheckin ? 'is-active' : '' }}" type="button" data-access-tab="checkout">
                    <i class="bi bi-box-arrow-left"></i> Check-out
                </button>
            </div>
            @if (auth()->user()?->hasPermission('system.manage'))
                <button class="access-settings-btn" type="button" data-bs-toggle="modal" data-bs-target="#accessQuickSettingsModal" aria-label="Cấu hình nhanh Check-in và Check-out" title="Cấu hình nhanh">
                    <i class="bi bi-gear"></i>
                </button>
            @endif
        </div>
    </section>

    @if (auth()->user()?->hasPermission('system.manage'))
        <div class="modal fade access-settings-modal" id="accessQuickSettingsModal" tabindex="-1" aria-labelledby="accessQuickSettingsTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form class="modal-content" method="post" action="{{ route('admin.access.quick-settings.update') }}">
                    @csrf
                    @method('put')
                    <input type="hidden" name="return_mode" value="{{ $activeMode }}">
                    <div class="modal-header">
                        <div>
                            <h2 class="modal-title" id="accessQuickSettingsTitle">Cấu hình Check-in/Check-out</h2>
                            <small class="text-secondary">Thiết lập nhanh khung giờ và nội dung cảnh báo.</small>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                    </div>
                    <div class="modal-body">
                        <div class="access-setting-row">
                            <div class="access-setting-copy">
                                <label for="allowEarlyCheckin">Cho phép check-in sớm</label>
                                <span>Cho khách vào trước giờ hẹn trong khoảng cho phép.</span>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" id="allowEarlyCheckin" type="checkbox" name="allow_early_checkin" value="1" @checked($allowEarlyCheckin)>
                            </div>
                        </div>
                        <div class="access-setting-row">
                            <div class="access-setting-copy">
                                <label for="earlyCheckinMinutes">Số phút check-in sớm</label>
                                <span>Mặc định 30 phút.</span>
                            </div>
                            <div class="input-group access-minutes">
                                <input class="form-control" id="earlyCheckinMinutes" type="number" name="early_checkin_minutes" min="0" max="1440" value="{{ old('early_checkin_minutes', $accessSettings['access.early_checkin_minutes'] ?? 30) }}" required>
                                <span class="input-group-text">phút</span>
                            </div>
                        </div>
                        <div class="access-setting-row">
                            <div class="access-setting-copy">
                                <label for="allowLateCheckin">Cho phép check-in trễ</label>
                                <span>Cho khách vào sau giờ hẹn trong khoảng cho phép.</span>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" id="allowLateCheckin" type="checkbox" name="allow_late_checkin" value="1" @checked($allowLateCheckin)>
                            </div>
                        </div>
                        <div class="access-setting-row">
                            <div class="access-setting-copy">
                                <label for="lateCheckinMinutes">Số phút check-in trễ</label>
                                <span>Mặc định 60 phút.</span>
                            </div>
                            <div class="input-group access-minutes">
                                <input class="form-control" id="lateCheckinMinutes" type="number" name="late_checkin_minutes" min="0" max="1440" value="{{ old('late_checkin_minutes', $accessSettings['access.late_checkin_minutes'] ?? 60) }}" required>
                                <span class="input-group-text">phút</span>
                            </div>
                        </div>
                        <div class="access-setting-row">
                            <div class="access-setting-copy">
                                <label for="warningEnabled">Bật cảnh báo</label>
                                <span>Hiện nội dung cảnh báo khi khách ngoài khung giờ.</span>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" id="warningEnabled" type="checkbox" name="warning_enabled" value="1" @checked($warningEnabled)>
                            </div>
                        </div>
                        <div class="access-warning-field">
                            <label for="warningMessage">Nội dung cảnh báo</label>
                            <textarea class="form-control" id="warningMessage" name="warning_message" maxlength="500" placeholder="Nhập nội dung cảnh báo...">{{ old('warning_message', $accessSettings['access.warning_message'] ?? '') }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Hủy</button>
                        <button class="access-settings-save" type="submit"><i class="bi bi-check2 me-1"></i>Lưu cấu hình</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <section class="access-stats">
        <div class="access-stat stat-waiting"><span>Chờ vào</span><strong>{{ $accessStats['waiting_in'] ?? 0 }}</strong></div>
        <div class="access-stat stat-inside"><span>Đang trong công ty</span><strong>{{ $accessStats['inside'] ?? 0 }}</strong></div>
        <a class="access-stat stat-in" href="{{ route('admin.access.lists', ['type' => 'in', 'from' => now()->toDateString(), 'to' => now()->toDateString()]) }}"><span>Vào hôm nay</span><strong>{{ $accessStats['checked_in_today'] ?? 0 }}</strong></a>
        <a class="access-stat stat-out" href="{{ route('admin.access.lists', ['type' => 'out', 'from' => now()->toDateString(), 'to' => now()->toDateString()]) }}"><span>Ra hôm nay</span><strong>{{ $accessStats['checked_out_today'] ?? 0 }}</strong></a>
        <div class="access-stat stat-late"><span>Quá giờ</span><strong>{{ $accessStats['overstay'] ?? 0 }}</strong></div>
    </section>

    @if ($accessHistoryOpen)
    <section class="access-history">
        <div class="access-history-head">
            <div class="access-history-title">
                <h3>{{ $accessHistoryType === 'out' ? 'Danh sách khách ra' : 'Danh sách khách vào' }}</h3>
                <p>Xem lại theo ngày, bấm vào một dòng để mở chi tiết.</p>
            </div>
            <form class="access-history-filter" method="get" action="{{ route('admin.access.index') }}">
                <input type="hidden" name="mode" value="{{ $activeMode }}">
                <input type="hidden" name="history" value="{{ $accessHistoryType }}">
                <input class="form-control" type="date" name="history_date" value="{{ $accessHistoryDate }}">
                <button type="submit"><i class="bi bi-funnel"></i> Xem</button>
                <a class="access-history-close" href="{{ route('admin.access.index', ['mode' => $activeMode]) }}"><i class="bi bi-x-lg"></i> Đóng</a>
            </form>
        </div>
        @if (count($accessHistory) > 0)
            <div class="access-history-list">
                @foreach ($accessHistory as $event)
                    <a class="access-history-item" href="{{ $event['detail_url'] }}">
                        <div class="access-history-top">
                            <span class="access-history-code">{{ $event['code'] }}</span>
                            <span class="access-history-time">{{ $event['time'] }}</span>
                        </div>
                        <div class="access-history-person">
                            <span class="access-history-avatar">{{ strtoupper(mb_substr($event['visitor'], 0, 1)) }}</span>
                            <span>
                                <span class="access-history-name d-block">{{ $event['visitor'] }}</span>
                                <span class="access-history-company d-block">{{ $event['company'] }}</span>
                            </span>
                        </div>
                        <div class="access-history-meta">{{ $event['host'] }} · {{ $event['department'] }}</div>
                        <span class="access-history-badge {{ $event['type'] }}"><i class="bi {{ $event['type'] === 'in' ? 'bi-box-arrow-in-right' : 'bi-box-arrow-left' }}"></i>{{ $event['label'] }}</span>
                    </a>
                @endforeach
            </div>
        @else
            <div class="access-history-empty">Chưa có lượt {{ $accessHistoryType === 'out' ? 'khách ra' : 'khách vào' }} trong ngày đã chọn.</div>
        @endif
    </section>
    @endif

    <section class="access-pane {{ $isCheckin ? 'is-active' : '' }}" data-access-pane="checkin">
        <div class="access-workspace {{ $checkinScannedVisit ? 'has-result' : '' }}">
            @unless ($checkinScannedVisit)
                <section class="access-card">
                    <div class="access-card-head">
                        <div><h3>Quét mã check-in</h3><p>Nhập mã lịch hẹn hoặc đưa QR vào đầu đọc.</p></div>
                    </div>
                    <div class="access-body access-scan">
                        <div class="access-frame">
                            <span class="access-corner tl"></span><span class="access-corner tr"></span><span class="access-corner bl"></span><span class="access-corner br"></span>
                            <div class="access-frame-copy"><i class="bi bi-upc-scan"></i><strong>Thiết bị đọc QR</strong><span>Ô nhập đang sẵn sàng nhận mã</span></div>
                        </div>
                        <form class="access-form" id="accessCheckinForm" action="{{ route('admin.checkin.scan-qr') }}" method="post">
                            @csrf
                            <div class="access-input-wrap">
                                <input type="text" name="qr_token" id="accessCheckinInput" value="{{ old('qr_token') }}" placeholder="Nhập mã lịch hẹn hoặc mã QR" autocomplete="off">
                                <i class="bi bi-upc-scan"></i>
                            </div>
                            <button class="access-btn primary" type="submit"><i class="bi bi-search"></i>Kiểm tra mã</button>
                        </form>
                    </div>
                </section>
            @endunless

            <section class="access-card access-detail {{ $checkinScannedVisit ? 'is-result' : '' }}">
                @if ($checkinScannedVisit)
                    <div class="access-card-head">
                        <div>
                            <h3>Thông tin khách</h3>
                            <p>Mã hợp lệ sẽ được hệ thống tự động cho khách vào.</p>
                            <span class="access-scanned-code">Mã vừa quét: <strong>{{ $checkinScannedVisit->code }}</strong></span>
                        </div>
                        <div class="access-result-actions">
                            <x-status-badge :status="$checkinScannedVisit->status" />
                            <a class="access-result-link" href="{{ route('admin.access.index', ['mode' => 'checkin']) }}"><i class="bi bi-arrow-clockwise"></i> Làm mới</a>
                        </div>
                    </div>
                    <div class="access-profile">
                        <div class="access-avatar">{{ strtoupper(mb_substr($checkinScannedVisit->visitor?->full_name ?? 'K', 0, 1)) }}</div>
                        <div><h3 class="access-name">{{ $checkinScannedVisit->visitor?->full_name ?? '-' }}</h3><div class="access-company">{{ $checkinScannedVisit->visitor?->company ?? 'Khách vãng lai' }}</div></div>
                    </div>
                    <div class="access-info-grid">
                        <div class="access-info"><i class="bi bi-person-badge"></i><div><span class="access-label">Người cần gặp</span><span class="access-value">{{ $checkinScannedVisit->hostEmployee?->name ?? '-' }}</span></div></div>
                        <div class="access-info"><i class="bi bi-building"></i><div><span class="access-label">Phòng ban</span><span class="access-value">{{ $checkinScannedVisit->hostEmployee?->department?->name ?? '-' }}</span></div></div>
                        <div class="access-info"><i class="bi bi-clock"></i><div><span class="access-label">Giờ hẹn</span><span class="access-value">{{ $checkinScannedVisit->scheduled_at?->format('H:i - d/m/Y') ?? '-' }}</span></div></div>
                        <div class="access-info"><i class="bi bi-telephone"></i><div><span class="access-label">Số điện thoại</span><span class="access-value">{{ $checkinScannedVisit->visitor?->phone ?? '-' }}</span></div></div>
                        <div class="access-info"><i class="bi bi-envelope"></i><div><span class="access-label">Email</span><span class="access-value">{{ $checkinScannedVisit->visitor?->email ?? '-' }}</span></div></div>
                        <div class="access-info"><i class="bi bi-chat-square-text"></i><div><span class="access-label">Mục đích đến</span><span class="access-value">{{ $checkinScannedVisit->purpose ?? '-' }}</span></div></div>
                    </div>
                    <div class="access-meta"><span>{{ $checkinScannedVisit->qr_token && ! $checkinScannedQrExpired ? 'Mã hợp lệ' : 'QR đã hết hạn' }}</span><strong>{{ $checkinScannedVisit->code }}</strong></div>
                    @php
                        $notice = match ($checkinScannedVisit->status) {
                            'checked_in' => ['class' => 'info', 'text' => 'Khách đã được tự động check-in và đang trong công ty.'],
                            'checked_out' => ['class' => 'info', 'text' => 'Khách đã check-out, không thể check-in lại từ màn hình này.'],
                            'rejected' => ['class' => 'danger', 'text' => 'Lịch đã bị từ chối, không thể cho khách vào.'],
                            'cancelled' => ['class' => 'danger', 'text' => 'Lịch đã hủy, không thể cho khách vào.'],
                            default => $checkinScannedVisit->status === 'approved'
                                ? ['class' => '', 'text' => 'QR đã hết hạn. Vui lòng tạo mã mới hoặc nhập mã lịch hẹn thủ công.']
                                : ['class' => '', 'text' => 'Lịch chưa được duyệt, vui lòng duyệt trước khi cho khách vào.'],
                        };
                    @endphp
                    <div class="access-action-area">
                        <div class="access-notice {{ $notice['class'] }}"><i class="bi bi-info-circle me-1"></i>{{ $notice['text'] }}</div>
                    </div>
                    <form class="access-result-scan" id="accessCheckinForm" action="{{ route('admin.checkin.scan-qr') }}" method="post">
                        @csrf
                        <div class="access-input-wrap">
                            <input type="text" name="qr_token" id="accessCheckinInput" placeholder="Sẵn sàng quét khách tiếp theo" autocomplete="off">
                            <i class="bi bi-upc-scan"></i>
                        </div>
                        <button class="access-btn primary" type="submit"><i class="bi bi-search"></i>Kiểm tra mã</button>
                    </form>
                    <div class="access-result-hint"><i class="bi bi-lightning-charge"></i> Nếu không có lượt quét mới, màn hình sẽ tự làm mới.</div>
                @else
                    <div class="access-empty"><div><i class="bi bi-person-bounding-box"></i><strong>Chưa có khách được chọn</strong><p>Quét QR hoặc nhập mã lịch hẹn bên trái để hiển thị thông tin khách cần làm thủ tục vào.</p></div></div>
                @endif
            </section>
        </div>

        <section class="access-card access-list checkin-grid">
            <div class="access-list-head"><div><h3>Khách chờ check-in</h3><p>Danh sách lịch đã duyệt mới nhất đang chờ làm thủ tục vào.</p></div><span class="access-pill">{{ count($checkinRows) }} khách</span></div>
            @if (count($checkinRows) > 0)
                <div class="access-row-head"><span>Mã lịch</span><span>Giờ hẹn</span><span>Khách</span><span>Người cần gặp</span><span>Phòng ban</span><span>Trạng thái</span></div>
            @endif
            @forelse ($checkinRows as $visit)
                <button class="access-row" type="button" data-checkin-code="{{ $visit['code'] }}">
                    <span class="access-code">{{ $visit['code'] }}</span>
                    <span><span class="access-main-text">{{ $visit['time'] }}</span><span class="access-muted">{{ $visit['date'] }}</span></span>
                    <span><span class="access-main-text">{{ $visit['visitor'] }}</span><span class="access-muted">Chờ làm thủ tục vào</span></span>
                    <span><span class="access-main-text">{{ $visit['host'] }}</span><span class="access-muted">Người tiếp khách</span></span>
                    <span><span class="access-tag">{{ $visit['department'] }}</span></span>
                    <span><span class="access-ok"><i class="bi bi-check-circle"></i> Đã duyệt</span></span>
                </button>
            @empty
                <div class="access-list-empty">Không có khách đang chờ check-in.</div>
            @endforelse
        </section>
    </section>

    <section class="access-pane {{ ! $isCheckin ? 'is-active' : '' }}" data-access-pane="checkout">
        <div class="access-workspace {{ $checkoutScannedVisit ? 'has-result' : '' }}">
            @unless ($checkoutScannedVisit)
                <section class="access-card">
                    <div class="access-card-head"><div><h3>Quét mã check-out</h3><p>Nhập mã lịch hẹn, QR hoặc chọn khách trong danh sách.</p></div></div>
                    <div class="access-body access-scan">
                        <div class="access-frame">
                            <span class="access-corner tl"></span><span class="access-corner tr"></span><span class="access-corner bl"></span><span class="access-corner br"></span>
                            <div class="access-frame-copy"><i class="bi bi-person-bounding-box"></i><strong>Thiết bị đọc QR</strong><span>Chỉ xử lý khách đang trong công ty</span></div>
                        </div>
                        <form class="access-form" id="accessCheckoutForm" action="{{ route('admin.checkout.scan-qr') }}" method="post">
                            @csrf
                            <div class="access-input-wrap">
                                <input type="text" name="qr_token" id="accessCheckoutInput" placeholder="Nhập mã lịch hẹn hoặc mã QR" autocomplete="off">
                                <i class="bi bi-upc-scan"></i>
                            </div>
                            <button class="access-btn primary" type="submit"><i class="bi bi-search"></i>Kiểm tra mã</button>
                        </form>
                    </div>
                </section>
            @endunless

            <section class="access-card access-detail {{ $checkoutScannedVisit ? 'is-result' : '' }}">
                @if ($checkoutScannedVisit)
                    <div class="access-card-head">
                        <div>
                            <h3>Thông tin khách</h3>
                            <p>Mã hợp lệ sẽ được hệ thống tự động cho khách ra.</p>
                            <span class="access-scanned-code">Mã vừa quét: <strong>{{ $checkoutScannedVisit->code }}</strong></span>
                        </div>
                        <div class="access-result-actions">
                            <x-status-badge :status="$checkoutScannedVisit->status" />
                            <a class="access-result-link" href="{{ route('admin.access.index', ['mode' => 'checkout']) }}"><i class="bi bi-arrow-clockwise"></i> Làm mới</a>
                        </div>
                    </div>
                    <div class="access-profile">
                        <div class="access-avatar">{{ strtoupper(mb_substr($checkoutScannedVisit->visitor?->full_name ?? 'K', 0, 1)) }}</div>
                        <div><h3 class="access-name">{{ $checkoutScannedVisit->visitor?->full_name ?? '-' }}</h3><div class="access-company">{{ $checkoutScannedVisit->visitor?->company ?? 'Khách vãng lai' }}</div></div>
                    </div>
                    <div class="access-info-grid">
                        <div class="access-info"><i class="bi bi-person-badge"></i><div><span class="access-label">Người cần gặp</span><span class="access-value">{{ $checkoutScannedVisit->hostEmployee?->name ?? '-' }}</span></div></div>
                        <div class="access-info"><i class="bi bi-building"></i><div><span class="access-label">Phòng ban</span><span class="access-value">{{ $checkoutScannedVisit->hostEmployee?->department?->name ?? '-' }}</span></div></div>
                        <div class="access-info"><i class="bi bi-box-arrow-in-right"></i><div><span class="access-label">Vào lúc</span><span class="access-value">{{ $checkoutScannedVisit->actual_checkin_at?->format('H:i - d/m/Y') ?? '-' }}</span></div></div>
                        <div class="access-info"><i class="bi bi-clock-history"></i><div><span class="access-label">Dự kiến ra</span><span class="access-value">{{ $checkoutScannedVisit->expected_checkout_at?->format('H:i - d/m/Y') ?? '-' }}</span></div></div>
                        <div class="access-info"><i class="bi bi-telephone"></i><div><span class="access-label">Số điện thoại</span><span class="access-value">{{ $checkoutScannedVisit->visitor?->phone ?? '-' }}</span></div></div>
                        <div class="access-info"><i class="bi bi-chat-square-text"></i><div><span class="access-label">Mục đích đến</span><span class="access-value">{{ $checkoutScannedVisit->purpose ?? '-' }}</span></div></div>
                    </div>
                    <div class="access-meta"><span>Mã lịch</span><strong>{{ $checkoutScannedVisit->code }}</strong></div>
                    <div class="access-action-area">
                        <div class="access-notice info"><i class="bi bi-info-circle me-1"></i>{{ $checkoutScannedVisit->status === 'checked_out' ? 'Khách đã được tự động check-out.' : 'Chỉ khách đang trong công ty mới được làm thủ tục ra.' }}</div>
                    </div>
                    <form class="access-result-scan" id="accessCheckoutForm" action="{{ route('admin.checkout.scan-qr') }}" method="post">
                        @csrf
                        <div class="access-input-wrap">
                            <input type="text" name="qr_token" id="accessCheckoutInput" placeholder="Sẵn sàng quét khách tiếp theo" autocomplete="off">
                            <i class="bi bi-upc-scan"></i>
                        </div>
                        <button class="access-btn primary" type="submit"><i class="bi bi-search"></i>Kiểm tra mã</button>
                    </form>
                    <div class="access-result-hint"><i class="bi bi-lightning-charge"></i> Nếu không có lượt quét mới, màn hình sẽ tự làm mới.</div>
                @else
                    <div class="access-empty"><div><i class="bi bi-person-bounding-box"></i><strong>Chưa có khách được chọn</strong><p>Quét QR, nhập mã lịch hẹn hoặc chọn khách trong danh sách để làm thủ tục ra.</p></div></div>
                @endif
            </section>
        </div>

        <section class="access-card access-list checkout-grid">
            <div class="access-list-head">
                <div><h3>Khách đang trong công ty</h3><p>Danh sách khách đã vào nhưng chưa làm thủ tục ra.</p></div>
                <div class="access-tools"><input class="form-control" id="accessCheckoutSearch" placeholder="Tìm khách..."><select class="form-select" id="accessCheckoutDepartment"><option value="">Tất cả phòng ban</option></select></div>
            </div>
            @if (count($checkoutRows) > 0)
                <div class="access-row-head"><span>Khách</span><span>Người cần gặp</span><span>Phòng ban</span><span>Vào lúc</span><span>Còn lại</span><span>Trạng thái</span><span></span></div>
            @endif
            @forelse ($checkoutRows as $visit)
                <button class="access-row" type="button" data-checkout-code="{{ $visit['code'] }}" data-checkout-row data-department="{{ $visit['department'] }}" data-search="{{ strtolower($visit['code'].' '.$visit['visitor'].' '.$visit['company'].' '.$visit['host'].' '.$visit['department']) }}">
                    <span><span class="access-main-text">{{ $visit['visitor'] }}</span><span class="access-muted">{{ $visit['company'] }}</span></span>
                    <span class="access-main-text">{{ $visit['host'] }}</span>
                    <span><span class="access-tag">{{ $visit['department'] }}</span></span>
                    <span class="access-main-text">{{ $visit['checkin_time'] }}</span>
                    <span class="access-main-text">{{ $visit['remaining'] }}</span>
                    <span><span class="access-ok {{ $visit['is_overstay'] ? 'access-late' : '' }}"><i class="bi {{ $visit['is_overstay'] ? 'bi-exclamation-triangle' : 'bi-check-circle' }}"></i>{{ $visit['is_overstay'] ? 'Quá giờ' : 'Đang trong công ty' }}</span></span>
                    <span><span class="access-tag">Khách ra</span></span>
                </button>
            @empty
                <div class="access-list-empty">Không có khách đang trong công ty.</div>
            @endforelse
        </section>
    </section>
</div>
@endsection

@push('scripts')
<script>
    const accessPage = document.querySelector('.access-page');
    const tabs = document.querySelectorAll('[data-access-tab]');
    const panes = document.querySelectorAll('[data-access-pane]');
    const checkinInput = document.getElementById('accessCheckinInput');
    const checkinForm = document.getElementById('accessCheckinForm');
    const checkoutInput = document.getElementById('accessCheckoutInput');
    const checkoutForm = document.getElementById('accessCheckoutForm');
    let checkinTimer = null;
    let checkoutTimer = null;
    let checkinSubmitted = false;
    let checkoutSubmitted = false;
    let resetTimer = null;

    function setAccessMode(mode, replace = false) {
        tabs.forEach((tab) => tab.classList.toggle('is-active', tab.dataset.accessTab === mode));
        panes.forEach((pane) => pane.classList.toggle('is-active', pane.dataset.accessPane === mode));
        const input = mode === 'checkout' ? checkoutInput : checkinInput;
        window.setTimeout(() => input?.focus(), 120);
        const url = new URL(window.location.href);
        url.searchParams.set('mode', mode);
        window.history[replace ? 'replaceState' : 'pushState']({}, '', url);
    }

    tabs.forEach((tab) => tab.addEventListener('click', () => setAccessMode(tab.dataset.accessTab)));
    setAccessMode(accessPage?.dataset.activeMode || 'checkin', true);

    function looksComplete(value) {
        const normalized = value.trim();
        return /^[0-9]{6,}$/.test(normalized) || /^(WK|VO|RP)-[A-Z0-9-]{6,}$/i.test(normalized);
    }

    function bindAutoSubmit(input, form, mode) {
        if (!input || !form) return;
        const submit = () => {
            if (mode === 'checkin' && checkinSubmitted) return;
            if (mode === 'checkout' && checkoutSubmitted) return;
            if (!input.value.trim()) return;
            if (mode === 'checkin') checkinSubmitted = true;
            else checkoutSubmitted = true;
            form.requestSubmit();
        };
        const schedule = () => {
            if (mode === 'checkin') clearTimeout(checkinTimer);
            else clearTimeout(checkoutTimer);
            if (!looksComplete(input.value)) return;
            const timer = window.setTimeout(submit, 280);
            if (mode === 'checkin') checkinTimer = timer;
            else checkoutTimer = timer;
        };
        input.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' && input.value.trim() !== '') {
                event.preventDefault();
                submit();
            }
        });
        input.addEventListener('input', schedule);
        input.addEventListener('paste', () => window.setTimeout(schedule, 0));
    }

    bindAutoSubmit(checkinInput, checkinForm, 'checkin');
    bindAutoSubmit(checkoutInput, checkoutForm, 'checkout');

    function scheduleResultReset() {
        const resultCard = document.querySelector('.access-pane.is-active .access-detail.is-result');
        if (!resultCard) return;
        const activeMode = accessPage?.dataset.activeMode || new URL(window.location.href).searchParams.get('mode') || 'checkin';
        const activeInput = activeMode === 'checkout' ? checkoutInput : checkinInput;
        const resetUrl = new URL(window.location.href);
        resetUrl.searchParams.set('mode', activeMode);

        clearTimeout(resetTimer);
        resetTimer = window.setTimeout(() => {
            if (activeInput?.value.trim()) return;
            window.location.href = resetUrl.toString();
        }, 7000);

        activeInput?.addEventListener('input', () => clearTimeout(resetTimer), { once: true });
        activeInput?.addEventListener('paste', () => clearTimeout(resetTimer), { once: true });
    }

    scheduleResultReset();

    document.querySelectorAll('[data-checkin-code]').forEach((row) => {
        row.addEventListener('click', () => {
            if (!checkinInput || !checkinForm) return;
            setAccessMode('checkin');
            checkinInput.value = row.dataset.checkinCode || '';
            checkinInput.focus();
            checkinInput.select();
            checkinForm.requestSubmit();
        });
    });

    document.querySelectorAll('[data-checkout-code]').forEach((row) => {
        row.addEventListener('click', () => {
            if (!checkoutInput || !checkoutForm) return;
            setAccessMode('checkout');
            checkoutInput.value = row.dataset.checkoutCode || '';
            checkoutInput.focus();
            checkoutInput.select();
            checkoutForm.requestSubmit();
        });
    });

    const checkoutRows = Array.from(document.querySelectorAll('[data-checkout-row]'));
    const checkoutSearch = document.getElementById('accessCheckoutSearch');
    const checkoutDepartment = document.getElementById('accessCheckoutDepartment');

    if (checkoutDepartment && checkoutRows.length > 0) {
        [...new Set(checkoutRows.map((row) => row.dataset.department).filter(Boolean))].sort().forEach((department) => {
            const option = document.createElement('option');
            option.value = department;
            option.textContent = department;
            checkoutDepartment.appendChild(option);
        });
    }

    function filterCheckoutRows() {
        const keyword = (checkoutSearch?.value || '').trim().toLowerCase();
        const department = checkoutDepartment?.value || '';
        checkoutRows.forEach((row) => {
            const matchKeyword = !keyword || (row.dataset.search || '').includes(keyword);
            const matchDepartment = !department || row.dataset.department === department;
            row.style.display = matchKeyword && matchDepartment ? '' : 'none';
        });
    }

    checkoutSearch?.addEventListener('input', filterCheckoutRows);
    checkoutDepartment?.addEventListener('change', filterCheckoutRows);
</script>
@endpush

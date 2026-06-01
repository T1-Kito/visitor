@extends('layouts.admin')

@section('title', 'Tạo lịch hẹn | Quản lý khách')
@section('page_title', 'Tạo lịch hẹn mới')
@section('page_subtitle', 'Tạo yêu cầu tiếp khách, gửi phê duyệt và sinh mã lịch tự động')

@push('styles')
<style>
.vc-layout{display:grid;grid-template-columns:minmax(720px,1fr) 340px;gap:1rem}.vc-form-stack{display:grid;gap:1rem}.vc-section{background:#fff;border:1px solid #e3edf8;border-radius:22px;box-shadow:0 14px 34px rgba(17,39,68,.05);overflow:hidden}.vc-section-head{display:flex;align-items:center;gap:.75rem;padding:1rem 1.15rem;border-bottom:1px solid #edf3fb;background:linear-gradient(180deg,#fff,#fbfdff)}.vc-section-icon{width:34px;height:34px;display:grid;place-items:center;border-radius:12px;background:#eaf3ff;color:#146bd7}.vc-section-title strong{display:block;color:#0b1f3a;font-size:.9rem;font-weight:900}.vc-section-title span{display:block;color:#6f88a4;font-size:.72rem}.vc-section-body{padding:1.15rem}.vc-grid{display:grid;grid-template-columns:1fr 1fr;gap:1rem}.vc-field label{display:flex;align-items:center;gap:.25rem;margin-bottom:.4rem;color:#29435f;font-size:.76rem;font-weight:900}.vc-field label em{color:#e11d48;font-style:normal}.vc-control{position:relative}.vc-control i{position:absolute;left:.85rem;top:50%;transform:translateY(-50%);color:#7a93b0;font-size:.9rem}.vc-control .form-control,.vc-control .form-select{min-height:48px;padding-left:2.35rem;border-color:#d8e5f2;border-radius:13px;color:#0b1f3a;font-size:.86rem;box-shadow:none}.vc-control textarea.form-control{min-height:92px;padding-top:.85rem}.vc-control .form-control:focus,.vc-control .form-select:focus{border-color:#8cc6ff;box-shadow:0 0 0 .22rem rgba(20,107,215,.1)}.vc-error{display:block;margin-top:.35rem;color:#dc2626;font-size:.72rem}.vc-wide{grid-column:1/-1}.vc-footer{display:flex;align-items:center;justify-content:space-between;gap:1rem;margin-top:.9rem}.vc-btn-main{min-width:260px;min-height:48px;border:0;border-radius:14px;color:#fff;font-weight:900;background:linear-gradient(135deg,#146bd7,#0cb4d8);box-shadow:0 14px 30px rgba(20,107,215,.24)}.vc-btn-cancel{min-height:48px;padding:0 1.1rem;border:1px solid #d8e5f2;border-radius:14px;background:#fff;color:#29435f;font-weight:900;text-decoration:none}
.vc-side{display:grid;gap:1rem;align-content:start}.vc-summary,.vc-flow{background:#fff;border:1px solid #e3edf8;border-radius:22px;box-shadow:0 14px 34px rgba(17,39,68,.05);overflow:hidden}.vc-side-head{padding:.9rem 1rem;border-bottom:1px solid #edf3fb;background:#f7fbff;color:#0b1f3a;font-size:.82rem;font-weight:900;text-transform:uppercase;letter-spacing:.02em}.vc-summary-body{display:grid;gap:.85rem;padding:1rem}.vc-summary-row{display:flex;align-items:center;justify-content:space-between;gap:1rem;color:#647d99;font-size:.78rem}.vc-summary-row strong{color:#0b1f3a;font-size:.8rem;text-align:right}.vc-summary-pill{display:inline-flex;padding:.28rem .6rem;border-radius:999px;background:#fff7ed;color:#d97706;font-weight:900}.vc-note{display:flex;gap:.7rem;padding:.85rem;border:1px solid #cfe5ff;border-radius:16px;background:#eef7ff;color:#29435f;font-size:.76rem;line-height:1.45}.vc-note i{color:#146bd7;font-size:1.1rem}.vc-flow-body{display:grid;gap:.85rem;padding:1rem}.vc-step{display:grid;grid-template-columns:34px 1fr;gap:.7rem;align-items:start}.vc-step-num{width:32px;height:32px;display:grid;place-items:center;border-radius:50%;background:#eaf3ff;color:#146bd7;font-size:.78rem;font-weight:900}.vc-step:nth-child(2) .vc-step-num{background:#fff7ed;color:#d97706}.vc-step:nth-child(3) .vc-step-num{background:#ecfdf5;color:#059669}.vc-step:nth-child(4) .vc-step-num{background:#eef2ff;color:#4f46e5}.vc-step:nth-child(5) .vc-step-num{background:#f1f5f9;color:#475569}.vc-step strong{display:block;color:#0b1f3a;font-size:.8rem}.vc-step span{display:block;color:#7a93b0;font-size:.72rem}.vc-preview{display:grid;gap:.7rem;padding:.9rem;border-radius:16px;background:linear-gradient(135deg,#f8fbff,#eef7ff);border:1px solid #d8e8fa}.vc-preview-title{color:#0b1f3a;font-size:.8rem;font-weight:900}.vc-preview-code{display:flex;align-items:center;justify-content:space-between;gap:.75rem;padding:.65rem .75rem;border-radius:12px;background:#fff;border:1px dashed #b8d4f3;color:#29435f;font-size:.78rem;font-weight:900}.vc-preview-code span{color:#7a93b0;font-weight:800}
@media(max-width:1200px){.vc-layout{grid-template-columns:1fr}.vc-side{grid-template-columns:1fr 1fr}}@media(max-width:768px){.vc-grid,.vc-side{grid-template-columns:1fr}.vc-footer{flex-direction:column-reverse;align-items:stretch}.vc-btn-main{width:100%;min-width:0}.vc-btn-cancel{text-align:center}}
</style>
@endpush

@section('content')
<form action="{{ route('admin.visits.store') }}" method="post">
    @csrf

    <div class="vc-layout">
        <div class="vc-form-stack">
            <section class="vc-section">
                <div class="vc-section-head">
                    <div class="vc-section-icon"><i class="bi bi-person-vcard"></i></div>
                    <div class="vc-section-title">
                        <strong>1. Thông tin khách</strong>
                        <span>Nhập thông tin cơ bản để tạo hồ sơ tiếp khách.</span>
                    </div>
                </div>
                <div class="vc-section-body">
                    <div class="vc-grid">
                        <div class="vc-field">
                            <label>Họ và tên khách <em>*</em></label>
                            <div class="vc-control">
                                <i class="bi bi-person"></i>
                                <input class="form-control" name="visitor_name" value="{{ old('visitor_name') }}" placeholder="Ví dụ: Nguyễn Văn A" required>
                            </div>
                            @error('visitor_name')<span class="vc-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="vc-field">
                            <label>Số điện thoại</label>
                            <div class="vc-control">
                                <i class="bi bi-telephone"></i>
                                <input class="form-control" name="visitor_phone" value="{{ old('visitor_phone') }}" placeholder="0909 xxx xxx">
                            </div>
                            @error('visitor_phone')<span class="vc-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="vc-field">
                            <label>Email</label>
                            <div class="vc-control">
                                <i class="bi bi-envelope"></i>
                                <input class="form-control" type="email" name="visitor_email" value="{{ old('visitor_email') }}" placeholder="visitor@company.com">
                            </div>
                            @error('visitor_email')<span class="vc-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="vc-field">
                            <label>Công ty / tổ chức</label>
                            <div class="vc-control">
                                <i class="bi bi-building"></i>
                                <input class="form-control" name="visitor_company" value="{{ old('visitor_company') }}" placeholder="Tên công ty">
                            </div>
                            @error('visitor_company')<span class="vc-error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>
            </section>

            <section class="vc-section">
                <div class="vc-section-head">
                    <div class="vc-section-icon"><i class="bi bi-calendar2-check"></i></div>
                    <div class="vc-section-title">
                        <strong>2. Lịch hẹn và người tiếp</strong>
                        <span>Chọn nhân viên tiếp khách, thời gian và mục đích làm việc.</span>
                    </div>
                </div>
                <div class="vc-section-body">
                    <div class="vc-grid">
                        <div class="vc-field">
                            <label>Người tiếp khách <em>*</em></label>
                            <div class="vc-control">
                                <i class="bi bi-person-workspace"></i>
                                <select id="hostSelect" class="form-select" name="host_employee_id" required>
                                    <option value="">Chọn người tiếp khách</option>
                                    @foreach ($hosts as $host)
                                        <option value="{{ $host['id'] }}" data-department="{{ $host['department'] }}" @selected((string) old('host_employee_id') === (string) $host['id'])>
                                            {{ $host['name'] }} - {{ $host['department'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('host_employee_id')<span class="vc-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="vc-field">
                            <label>Phòng ban</label>
                            <div class="vc-control">
                                <i class="bi bi-diagram-3"></i>
                                <input id="departmentPreview" class="form-control" value="Tự động sau khi chọn" readonly>
                            </div>
                        </div>
                        <div class="vc-field">
                            <label>Ngày hẹn <em>*</em></label>
                            <div class="vc-control">
                                <i class="bi bi-calendar-event"></i>
                                <input class="form-control" type="date" name="visit_date" value="{{ old('visit_date', now()->toDateString()) }}" required>
                            </div>
                            @error('visit_date')<span class="vc-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="vc-field">
                            <label>Giờ hẹn <em>*</em></label>
                            <div class="vc-control">
                                <i class="bi bi-clock"></i>
                                <input class="form-control" type="time" name="visit_time" value="{{ old('visit_time', '09:00') }}" required>
                            </div>
                            @error('visit_time')<span class="vc-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="vc-field">
                            <label>Giờ ra dự kiến <em>*</em></label>
                            <div class="vc-control">
                                <i class="bi bi-clock-history"></i>
                                <input class="form-control" type="time" name="expected_checkout_time" value="{{ old('expected_checkout_time', '11:00') }}" required>
                            </div>
                            @error('expected_checkout_time')<span class="vc-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="vc-field">
                            <label>Mục đích đến <em>*</em></label>
                            <div class="vc-control">
                                <i class="bi bi-bullseye"></i>
                                <input class="form-control" name="purpose" value="{{ old('purpose') }}" placeholder="Họp, bàn giao, đào tạo, tham quan..." required>
                            </div>
                            @error('purpose')<span class="vc-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="vc-field vc-wide">
                            <label>Ghi chú thêm</label>
                            <div class="vc-control">
                                <i class="bi bi-chat-left-text"></i>
                                <textarea class="form-control" name="visitor_note" rows="3" maxlength="1000" placeholder="Nhắc ghi chú cho lễ tân / bảo vệ nếu cần...">{{ old('visitor_note') }}</textarea>
                            </div>
                            @error('visitor_note')<span class="vc-error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>
            </section>

            <section class="vc-section">
                <div class="vc-section-head">
                    <div class="vc-section-icon"><i class="bi bi-shield-check"></i></div>
                    <div class="vc-section-title">
                        <strong>3. Quyền truy cập và an ninh</strong>
                        <span>Cấu hình khu vực được phép vào và hình thức làm thủ tục.</span>
                    </div>
                </div>
                <div class="vc-section-body">
                    <div class="vc-grid">
                        <div class="vc-field">
                            <label>Khu vực truy cập</label>
                            <div class="vc-control">
                                <i class="bi bi-geo-alt"></i>
                                <select class="form-select" name="access_zone">
                                    @foreach ($accessZones as $zone)
                                        <option value="{{ $zone }}" @selected(old('access_zone') === $zone)>{{ $zone }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('access_zone')<span class="vc-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="vc-field">
                            <label>Hình thức vào <em>*</em></label>
                            <div class="vc-control">
                                <i class="bi bi-qr-code-scan"></i>
                                <select class="form-select" name="checkin_method" required>
                                    <option value="qr" @selected(old('checkin_method', 'qr') === 'qr')>Mã QR</option>
                                    <option value="badge" @selected(old('checkin_method') === 'badge')>Thẻ tạm</option>
                                    <option value="manual" @selected(old('checkin_method') === 'manual')>Nhập thủ công tại quầy</option>
                                </select>
                            </div>
                            @error('checkin_method')<span class="vc-error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>
            </section>

            <div class="vc-footer">
                <a class="vc-btn-cancel" href="{{ route('admin.visits.index') }}">
                    <i class="bi bi-x-lg"></i>
                    Hủy bỏ
                </a>
                <button class="vc-btn-main" type="submit">
                    <i class="bi bi-calendar-plus"></i>
                    Tạo lịch hẹn
                </button>
            </div>
        </div>

        <aside class="vc-side">
            <section class="vc-summary">
                <div class="vc-side-head">Thông tin tổng quan</div>
                <div class="vc-summary-body">
                    <div class="vc-preview">
                        <div class="vc-preview-title">Mã lịch hẹn</div>
                        <div class="vc-preview-code">
                            <span>Tự động sinh sau khi lưu</span>
                            <i class="bi bi-magic"></i>
                        </div>
                    </div>
                    <div class="vc-summary-row"><span>Trạng thái ban đầu</span><strong><span class="vc-summary-pill">Chờ duyệt</span></strong></div>
                    <div class="vc-summary-row"><span>Mã QR</span><strong>Tự động sinh sau khi lưu</strong></div>
                    <div class="vc-summary-row"><span>Ngày tạo</span><strong>{{ now()->format('d/m/Y - H:i') }}</strong></div>
                    <div class="vc-summary-row"><span>Người tạo</span><strong>{{ auth()->user()?->name ?? 'Người dùng hệ thống' }}</strong></div>
                    <div class="vc-note">
                        <i class="bi bi-info-circle-fill"></i>
                        <div>Sau khi tạo, hệ thống tự sinh mã lịch và mã QR không trùng, sau đó gửi yêu cầu phê duyệt cho người tiếp khách.</div>
                    </div>
                </div>
            </section>

            <section class="vc-flow">
                <div class="vc-side-head">Quy trình xử lý</div>
                <div class="vc-flow-body">
                    <div class="vc-step"><div class="vc-step-num">1</div><div><strong>Tạo lịch hẹn</strong><span>Tạo yêu cầu tiếp khách cho khách.</span></div></div>
                    <div class="vc-step"><div class="vc-step-num">2</div><div><strong>Phê duyệt</strong><span>Người tiếp nhận duyệt hoặc từ chối.</span></div></div>
                    <div class="vc-step"><div class="vc-step-num">3</div><div><strong>Sinh mã QR</strong><span>Mã QR được tạo ngay khi lưu lịch hẹn.</span></div></div>
                    <div class="vc-step"><div class="vc-step-num">4</div><div><strong>Khách vào</strong><span>Lễ tân hoặc bảo vệ xác nhận khách vào.</span></div></div>
                    <div class="vc-step"><div class="vc-step-num">5</div><div><strong>Khách ra</strong><span>Xác nhận khách rời công ty khi kết thúc.</span></div></div>
                </div>
            </section>
        </aside>
    </div>
</form>
@endsection

@push('scripts')
<script>
(() => {
    const hostSelect = document.getElementById('hostSelect');
    const departmentPreview = document.getElementById('departmentPreview');

    const syncDepartment = () => {
        const option = hostSelect.options[hostSelect.selectedIndex];
        departmentPreview.value = option?.dataset?.department || 'Tự động sau khi chọn';
    };

    hostSelect.addEventListener('change', syncDepartment);
    syncDepartment();
})();
</script>
@endpush

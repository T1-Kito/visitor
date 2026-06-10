@extends('layouts.mobile')

@section('title', 'Tạo lịch hẹn')

@push('styles')
    <style>
        .m-create-form {
            display: grid;
            gap: 12px;
        }

        .m-form-section {
            display: grid;
            gap: 12px;
            padding: 14px;
            border-radius: 22px;
            background: #fff;
            box-shadow: 0 10px 26px rgba(21, 34, 54, .05);
        }

        .m-form-section-head {
            display: flex;
            align-items: center;
            gap: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #edf3f8;
        }

        .m-form-section-head i {
            width: 36px;
            height: 36px;
            display: grid;
            place-items: center;
            border-radius: 13px;
            color: var(--m-blue);
            background: #eaf4ff;
        }

        .m-form-section-head strong,
        .m-form-section-head span {
            display: block;
        }

        .m-form-section-head strong {
            color: #14243a;
            font-size: .92rem;
            font-weight: 500;
        }

        .m-form-section-head span {
            margin-top: 2px;
            color: var(--m-muted);
            font-size: .7rem;
        }

        .m-form-grid {
            display: grid;
            gap: 10px;
        }

        .m-field {
            display: grid;
            gap: 5px;
        }

        .m-field label {
            color: #30465f;
            font-size: .72rem;
            font-weight: 500;
        }

        .m-field label em {
            color: #e11d48;
            font-style: normal;
        }

        .m-control {
            position: relative;
        }

        .m-control > i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #8299b4;
            font-size: .9rem;
            pointer-events: none;
        }

        .m-control input,
        .m-control select,
        .m-control textarea {
            width: 100%;
            min-height: 44px;
            padding: 0 12px 0 38px;
            border: 1px solid #d8e5f2;
            border-radius: 15px;
            outline: 0;
            color: var(--m-text);
            background: #fbfdff;
            font: inherit;
            font-size: .82rem;
            letter-spacing: 0;
        }

        .m-control select {
            appearance: none;
            padding-right: 34px;
        }

        .m-control textarea {
            min-height: 76px;
            padding-top: 12px;
            resize: vertical;
        }

        .m-control input:focus,
        .m-control select:focus,
        .m-control textarea:focus {
            border-color: #8cc6ff;
            box-shadow: 0 0 0 4px rgba(25, 118, 210, .08);
            background: #fff;
        }

        .m-select-caret {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #8299b4;
            pointer-events: none;
        }

        .m-error {
            color: #dc2626;
            font-size: .68rem;
        }

        .m-visitor-lookup {
            position: relative;
            display: grid;
            gap: 8px;
            padding: 10px;
            border: 1px solid #dbeafa;
            border-radius: 18px;
            background: #f7fbff;
        }

        .m-lookup-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            color: #49627d;
            font-size: .72rem;
        }

        .m-lookup-title span {
            color: var(--m-muted);
        }

        .m-selected-visitor {
            display: none;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            padding: 8px 9px;
            border: 1px solid #bdebd0;
            border-radius: 14px;
            color: #087443;
            background: #ecfdf4;
            font-size: .72rem;
        }

        .m-selected-visitor.show {
            display: flex;
        }

        .m-selected-visitor button {
            appearance: none;
            border: 0;
            border-radius: 999px;
            color: #087443;
            background: #fff;
            font: inherit;
            font-size: .68rem;
            padding: 5px 8px;
        }

        .m-suggestions {
            display: none;
            position: absolute;
            left: 10px;
            right: 10px;
            top: calc(100% - 4px);
            z-index: 30;
            overflow: hidden;
            border: 1px solid #d8e5f2;
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 18px 42px rgba(15, 35, 60, .16);
        }

        .m-suggestions.show {
            display: block;
        }

        .m-suggestion {
            width: 100%;
            display: grid;
            grid-template-columns: 38px minmax(0, 1fr);
            align-items: center;
            gap: 9px;
            padding: 10px;
            border: 0;
            border-bottom: 1px solid #eef4fb;
            background: #fff;
            color: inherit;
            font: inherit;
            text-align: left;
        }

        .m-suggestion:last-child {
            border-bottom: 0;
        }

        .m-suggestion-avatar {
            width: 36px;
            height: 36px;
            display: grid;
            place-items: center;
            border-radius: 13px;
            color: var(--m-blue);
            background: #eaf4ff;
            font-size: .8rem;
            font-weight: 500;
        }

        .m-suggestion strong,
        .m-suggestion span {
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .m-suggestion strong {
            font-size: .78rem;
            font-weight: 500;
        }

        .m-suggestion span {
            margin-top: 2px;
            color: var(--m-muted);
            font-size: .66rem;
        }

        .m-form-actions {
            position: sticky;
            bottom: calc(74px + env(safe-area-inset-bottom, 0px));
            z-index: 10;
            display: grid;
            grid-template-columns: .8fr 1.2fr;
            gap: 8px;
            padding: 10px;
            margin: 0 -4px;
            border: 1px solid rgba(216, 229, 242, .86);
            border-radius: 20px;
            background: rgba(255, 255, 255, .92);
            backdrop-filter: blur(14px);
        }

        .m-form-actions a,
        .m-form-actions button {
            min-height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            border-radius: 15px;
            font: inherit;
            font-size: .8rem;
            font-weight: 500;
        }

        .m-form-actions a {
            border: 1px solid #d8e5f2;
            color: #49627d;
            background: #fff;
        }

        .m-form-actions button {
            border: 0;
            color: #fff;
            background: linear-gradient(135deg, var(--m-blue), var(--m-cyan));
        }
    </style>
@endpush

@section('content')
    <section class="m-page-head">
        <a href="{{ route('mobile.visits.index') }}" aria-label="Quay lại"><i class="bi bi-chevron-left"></i></a>
        <div>
            <h1>Tạo lịch hẹn</h1>
            <p>Nhập thông tin khách, người cần gặp và thời gian hẹn.</p>
        </div>
    </section>

    @if ($errors->any())
        <div class="m-toast danger">
            <i class="bi bi-exclamation-triangle"></i>
            <span>Vui lòng kiểm tra lại các thông tin còn thiếu hoặc chưa đúng.</span>
        </div>
    @endif

    <form class="m-create-form" action="{{ route('admin.visits.store') }}" method="post">
        @csrf
        <input type="hidden" name="mobile" value="1">
        <input id="existingVisitorId" type="hidden" name="existing_visitor_id" value="{{ old('existing_visitor_id') }}">

        <section class="m-form-section">
            <div class="m-form-section-head">
                <i class="bi bi-person-vcard"></i>
                <div>
                    <strong>Thông tin khách</strong>
                    <span>Có thể tìm khách cũ để tự điền nhanh.</span>
                </div>
            </div>

            <div class="m-visitor-lookup">
                <div class="m-lookup-title">
                    <strong>Tìm khách đã từng đến</strong>
                    <span>Mã khách, CCCD, tên, SĐT, email</span>
                </div>
                <div class="m-control">
                    <i class="bi bi-search"></i>
                    <input id="visitorLookup" type="search" autocomplete="off" placeholder="Gõ ít nhất 2 ký tự..." data-search-url="{{ route('admin.visitors.search') }}">
                </div>
                <div id="visitorSuggestions" class="m-suggestions"></div>
                <div id="selectedVisitorBox" class="m-selected-visitor">
                    <span id="selectedVisitorText">Đã chọn khách cũ.</span>
                    <button id="clearVisitorSelection" type="button">Bỏ chọn</button>
                </div>
            </div>

            <div class="m-form-grid">
                <div class="m-field">
                    <label>Họ và tên <em>*</em></label>
                    <div class="m-control">
                        <i class="bi bi-person"></i>
                        <input id="visitorName" name="visitor_name" value="{{ old('visitor_name') }}" placeholder="Nhập họ và tên" required>
                    </div>
                    @error('visitor_name')<span class="m-error">{{ $message }}</span>@enderror
                </div>

                <div class="m-field">
                    <label>Số điện thoại</label>
                    <div class="m-control">
                        <i class="bi bi-telephone"></i>
                        <input id="visitorPhone" name="visitor_phone" value="{{ old('visitor_phone') }}" inputmode="tel" placeholder="0901 234 567">
                    </div>
                    @error('visitor_phone')<span class="m-error">{{ $message }}</span>@enderror
                </div>

                <div class="m-field">
                    <label>Email</label>
                    <div class="m-control">
                        <i class="bi bi-envelope"></i>
                        <input id="visitorEmail" type="email" name="visitor_email" value="{{ old('visitor_email') }}" placeholder="email@company.com">
                    </div>
                    @error('visitor_email')<span class="m-error">{{ $message }}</span>@enderror
                </div>

                <div class="m-field">
                    <label>Công ty / tổ chức</label>
                    <div class="m-control">
                        <i class="bi bi-building"></i>
                        <input id="visitorCompany" name="visitor_company" value="{{ old('visitor_company') }}" placeholder="Tên công ty">
                    </div>
                    @error('visitor_company')<span class="m-error">{{ $message }}</span>@enderror
                </div>

                <div class="m-field">
                    <label>CCCD / giấy tờ</label>
                    <div class="m-control">
                        <i class="bi bi-card-text"></i>
                        <input id="visitorIdentityNo" name="visitor_identity_no" value="{{ old('visitor_identity_no') }}" placeholder="Nhập số CCCD / hộ chiếu">
                    </div>
                    @error('visitor_identity_no')<span class="m-error">{{ $message }}</span>@enderror
                </div>

                <div class="m-field">
                    <label>Nơi cấp</label>
                    <div class="m-control">
                        <i class="bi bi-geo-alt"></i>
                        <input id="visitorIdentityIssuedPlace" name="visitor_identity_issued_place" value="{{ old('visitor_identity_issued_place') }}" placeholder="Nhập nơi cấp">
                    </div>
                    @error('visitor_identity_issued_place')<span class="m-error">{{ $message }}</span>@enderror
                </div>

                <div class="m-field">
                    <label>Ngày cấp</label>
                    <div class="m-control">
                        <i class="bi bi-calendar3"></i>
                        <input id="visitorIdentityIssuedDate" type="date" name="visitor_identity_issued_date" value="{{ old('visitor_identity_issued_date') }}">
                    </div>
                    @error('visitor_identity_issued_date')<span class="m-error">{{ $message }}</span>@enderror
                </div>
            </div>
        </section>

        <section class="m-form-section">
            <div class="m-form-section-head">
                <i class="bi bi-person-workspace"></i>
                <div>
                    <strong>Người cần gặp</strong>
                    <span>Host sẽ duyệt trước khi khách vào.</span>
                </div>
            </div>

            <div class="m-form-grid">
                <div class="m-field">
                    <label>Người cần gặp <em>*</em></label>
                    <div class="m-control">
                        <i class="bi bi-person-badge"></i>
                        <select id="hostSelect" name="host_employee_id" required>
                            <option value="">Chọn nhân viên</option>
                            @foreach ($hosts as $host)
                                <option value="{{ $host['id'] }}" data-department="{{ $host['department'] }}" @selected((string) old('host_employee_id') === (string) $host['id'])>
                                    {{ $host['name'] }}
                                </option>
                            @endforeach
                        </select>
                        <i class="bi bi-chevron-down m-select-caret"></i>
                    </div>
                    @error('host_employee_id')<span class="m-error">{{ $message }}</span>@enderror
                </div>

                <div class="m-field">
                    <label>Phòng ban</label>
                    <div class="m-control">
                        <i class="bi bi-diagram-3"></i>
                        <input id="departmentPreview" value="Tự động sau khi chọn" readonly>
                    </div>
                </div>
            </div>
        </section>

        <section class="m-form-section">
            <div class="m-form-section-head">
                <i class="bi bi-calendar2-check"></i>
                <div>
                    <strong>Thông tin lịch hẹn</strong>
                    <span>Mã lịch và QR sẽ tự sinh sau khi lưu.</span>
                </div>
            </div>

            <div class="m-form-grid">
                <div class="m-field">
                    <label>Ngày hẹn <em>*</em></label>
                    <div class="m-control">
                        <i class="bi bi-calendar-event"></i>
                        <input type="date" name="visit_date" value="{{ old('visit_date', now()->toDateString()) }}" required>
                    </div>
                    @error('visit_date')<span class="m-error">{{ $message }}</span>@enderror
                </div>

                <div class="m-field">
                    <label>Giờ vào <em>*</em></label>
                    <div class="m-control">
                        <i class="bi bi-clock"></i>
                        <input type="time" name="visit_time" value="{{ old('visit_time', '09:00') }}" required>
                    </div>
                    @error('visit_time')<span class="m-error">{{ $message }}</span>@enderror
                </div>

                <div class="m-field">
                    <label>Giờ ra dự kiến <em>*</em></label>
                    <div class="m-control">
                        <i class="bi bi-clock-history"></i>
                        <input type="time" name="expected_checkout_time" value="{{ old('expected_checkout_time', '11:00') }}" required>
                    </div>
                    @error('expected_checkout_time')<span class="m-error">{{ $message }}</span>@enderror
                </div>

                <div class="m-field">
                    <label>Mục đích đến <em>*</em></label>
                    <div class="m-control">
                        <i class="bi bi-chat-square-text"></i>
                        <input name="purpose" value="{{ old('purpose') }}" placeholder="Họp, giao hàng, phỏng vấn..." required>
                    </div>
                    @error('purpose')<span class="m-error">{{ $message }}</span>@enderror
                </div>

                <div class="m-field">
                    <label>Khu vực</label>
                    <div class="m-control">
                        <i class="bi bi-geo-alt"></i>
                        <select name="access_zone">
                            @foreach ($accessZones as $zone)
                                <option value="{{ $zone }}" @selected(old('access_zone') === $zone)>{{ $zone }}</option>
                            @endforeach
                        </select>
                        <i class="bi bi-chevron-down m-select-caret"></i>
                    </div>
                    @error('access_zone')<span class="m-error">{{ $message }}</span>@enderror
                </div>

                <div class="m-field">
                    <label>Hình thức vào <em>*</em></label>
                    <div class="m-control">
                        <i class="bi bi-qr-code-scan"></i>
                        <select name="checkin_method" required>
                            <option value="qr" @selected(old('checkin_method', 'qr') === 'qr')>Mã QR</option>
                            <option value="badge" @selected(old('checkin_method') === 'badge')>Thẻ tạm</option>
                            <option value="manual" @selected(old('checkin_method') === 'manual')>Nhập thủ công</option>
                        </select>
                        <i class="bi bi-chevron-down m-select-caret"></i>
                    </div>
                    @error('checkin_method')<span class="m-error">{{ $message }}</span>@enderror
                </div>

                <div class="m-field">
                    <label>Ghi chú</label>
                    <div class="m-control">
                        <i class="bi bi-pencil-square"></i>
                        <textarea id="visitorNote" name="visitor_note" maxlength="1000" placeholder="Ghi chú thêm nếu cần">{{ old('visitor_note') }}</textarea>
                    </div>
                    @error('visitor_note')<span class="m-error">{{ $message }}</span>@enderror
                </div>
            </div>
        </section>

        <div class="m-form-actions">
            <a href="{{ route('mobile.visits.index') }}">
                <i class="bi bi-x-lg"></i>
                Hủy
            </a>
            <button type="submit">
                <i class="bi bi-calendar-plus"></i>
                Tạo lịch hẹn
            </button>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        (() => {
            const hostSelect = document.getElementById('hostSelect');
            const departmentPreview = document.getElementById('departmentPreview');
            const lookupInput = document.getElementById('visitorLookup');
            const suggestionsBox = document.getElementById('visitorSuggestions');
            const selectedVisitorId = document.getElementById('existingVisitorId');
            const selectedVisitorBox = document.getElementById('selectedVisitorBox');
            const selectedVisitorText = document.getElementById('selectedVisitorText');
            const clearSelectionButton = document.getElementById('clearVisitorSelection');
            const fields = {
                name: document.getElementById('visitorName'),
                phone: document.getElementById('visitorPhone'),
                email: document.getElementById('visitorEmail'),
                company: document.getElementById('visitorCompany'),
                identityNo: document.getElementById('visitorIdentityNo'),
                identityIssuedPlace: document.getElementById('visitorIdentityIssuedPlace'),
                identityIssuedDate: document.getElementById('visitorIdentityIssuedDate'),
                note: document.getElementById('visitorNote'),
            };
            let searchTimer = null;

            const syncDepartment = () => {
                const option = hostSelect.options[hostSelect.selectedIndex];
                departmentPreview.value = option?.dataset?.department || 'Tự động sau khi chọn';
            };

            const hideSuggestions = () => {
                suggestionsBox.classList.remove('show');
                suggestionsBox.innerHTML = '';
            };

            const setSelectedVisitor = (visitor) => {
                selectedVisitorId.value = visitor.id || '';
                fields.name.value = visitor.full_name || '';
                fields.phone.value = visitor.phone || '';
                fields.email.value = visitor.email || '';
                fields.company.value = visitor.company || '';
                fields.identityNo.value = visitor.identity_no || '';
                fields.identityIssuedPlace.value = visitor.identity_issued_place || '';
                fields.identityIssuedDate.value = visitor.identity_issued_date || '';
                fields.note.value = visitor.note || fields.note.value || '';
                lookupInput.value = visitor.full_name || '';
                selectedVisitorText.textContent = `Đã chọn: ${visitor.visitor_code ? visitor.visitor_code + ' - ' : ''}${visitor.full_name || 'Khách'}${visitor.phone ? ' - ' + visitor.phone : ''}`;
                selectedVisitorBox.classList.add('show');
                hideSuggestions();
            };

            const clearSelectedVisitor = () => {
                selectedVisitorId.value = '';
                selectedVisitorBox.classList.remove('show');
                selectedVisitorText.textContent = 'Đã chọn khách cũ.';
            };

            const renderSuggestions = (items) => {
                suggestionsBox.innerHTML = '';

                if (!items.length) {
                    suggestionsBox.innerHTML = '<div style="padding:12px;color:#71849b;font-size:.72rem">Không tìm thấy khách phù hợp.</div>';
                    suggestionsBox.classList.add('show');
                    return;
                }

                items.forEach((visitor) => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'm-suggestion';

                    const avatar = document.createElement('span');
                    avatar.className = 'm-suggestion-avatar';
                    avatar.textContent = (visitor.full_name || 'K').trim().charAt(0).toUpperCase();

                    const info = document.createElement('span');
                    const name = document.createElement('strong');
                    name.textContent = visitor.full_name || 'Khách chưa có tên';
                    const meta = document.createElement('span');
                    meta.textContent = [visitor.visitor_code, visitor.identity_no, visitor.phone, visitor.email, visitor.company].filter(Boolean).join(' - ') || 'Chưa có thông tin liên hệ';

                    info.appendChild(name);
                    info.appendChild(meta);
                    button.appendChild(avatar);
                    button.appendChild(info);
                    button.addEventListener('click', () => setSelectedVisitor(visitor));
                    suggestionsBox.appendChild(button);
                });

                suggestionsBox.classList.add('show');
            };

            const searchVisitors = () => {
                clearTimeout(searchTimer);
                const keyword = lookupInput.value.trim();

                if (keyword.length < 2) {
                    hideSuggestions();
                    return;
                }

                searchTimer = setTimeout(async () => {
                    try {
                        const url = `${lookupInput.dataset.searchUrl}?q=${encodeURIComponent(keyword)}`;
                        const response = await fetch(url, { headers: { Accept: 'application/json' } });
                        const payload = await response.json();
                        renderSuggestions(payload.data || []);
                    } catch (error) {
                        suggestionsBox.innerHTML = '<div style="padding:12px;color:#b42318;font-size:.72rem">Không tải được danh sách khách.</div>';
                        suggestionsBox.classList.add('show');
                    }
                }, 260);
            };

            hostSelect.addEventListener('change', syncDepartment);
            lookupInput.addEventListener('input', searchVisitors);
            clearSelectionButton.addEventListener('click', clearSelectedVisitor);
            document.addEventListener('click', (event) => {
                if (!suggestionsBox.contains(event.target) && event.target !== lookupInput) {
                    hideSuggestions();
                }
            });

            syncDepartment();
        })();
    </script>
@endpush

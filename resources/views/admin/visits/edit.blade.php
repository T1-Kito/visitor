@extends('layouts.admin')

@section('title', 'Sua lich hen | Visitor Management')
@section('page_title', 'Sua lich hen '.$visit->code)
@section('page_subtitle', 'Cap nhat lich se dua ve trang thai cho duyet lai')

@section('content')
    <form class="row g-3" action="{{ route('admin.visits.update', $visit) }}" method="post">
        @csrf
        @method('PUT')

        <div class="col-xl-8">
            <section class="panel-card mb-3">
                <div class="panel-header">
                    <div>
                        <h3>Thong tin khach</h3>
                        <p>Cap nhat thong tin ho so khach.</p>
                    </div>
                    <x-status-badge :status="$visit->status" />
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Ho va ten khach</label>
                        <input type="text" name="visitor_name" value="{{ old('visitor_name', $visit->visitor?->full_name) }}" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">So dien thoai</label>
                        <input type="text" name="visitor_phone" value="{{ old('visitor_phone', $visit->visitor?->phone) }}" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="visitor_email" value="{{ old('visitor_email', $visit->visitor?->email) }}" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Cong ty</label>
                        <input type="text" name="visitor_company" value="{{ old('visitor_company', $visit->visitor?->company) }}" class="form-control">
                    </div>
                </div>
            </section>

            <section class="panel-card mb-3">
                <div class="panel-header">
                    <div>
                        <h3>Lich hen va nguoi tiep</h3>
                        <p>Thay doi host, ngay gio va muc dich.</p>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nguoi tiep khach</label>
                        <input id="editPageHostNameInput" type="text" name="host_name" value="{{ old('host_name', $visit->host_display_name) }}" class="form-control" list="editPageHostSuggestions" required>
                        <input id="editPageHostEmployeeId" type="hidden" name="host_employee_id" value="{{ old('host_employee_id', $visit->host_employee_id) }}">
                        <datalist id="editPageHostSuggestions">
                            @foreach ($hosts as $host)
                                <option value="{{ $host['name'] }}" data-id="{{ $host['id'] }}">{{ $host['department'] }}</option>
                            @endforeach
                        </datalist>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phong ban</label>
                        <select name="department_id" class="form-select" required>
                            <option value="">Chon phong ban</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}" @selected((string) old('department_id', $visit->department_id ?: $visit->hostEmployee?->department_id) === (string) $department->id)>{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Ngay vao</label>
                        <input type="date" name="visit_date" value="{{ old('visit_date', $visit->scheduled_at?->toDateString()) }}" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Gio vao</label>
                        <input type="time" name="visit_time" value="{{ old('visit_time', $visit->scheduled_at?->format('H:i')) }}" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Gio ra du kien</label>
                        <input type="time" name="expected_checkout_time" value="{{ old('expected_checkout_time', $visit->expected_checkout_at?->format('H:i')) }}" class="form-control" required>
                    </div>
                    <div class="col-md-9">
                        <label class="form-label">Muc dich</label>
                        <input type="text" name="purpose" value="{{ old('purpose', $visit->purpose) }}" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Ghi chu tiep don</label>
                        <textarea name="visitor_note" class="form-control" rows="3">{{ old('visitor_note', $visit->visitor?->note) }}</textarea>
                    </div>
                </div>
            </section>

            <section class="panel-card">
                <div class="panel-header">
                    <div>
                        <h3>Quyen truy cap va an ninh</h3>
                        <p>Cap nhat khu vuc va hinh thuc check-in.</p>
                    </div>
                </div>
                <div class="row g-3">
                    <input type="hidden" name="access_zone" value="{{ old('access_zone', $visit->access_zone) }}">
                    <div class="col-md-6">
                        <label class="form-label">Loai check-in</label>
                        <select name="checkin_method" class="form-select" required>
                            <option value="qr" @selected(old('checkin_method', $visit->checkin_method) === 'qr')>QR Code</option>
                            <option value="badge" @selected(old('checkin_method', $visit->checkin_method) === 'badge')>Badge tam</option>
                            <option value="manual" @selected(old('checkin_method', $visit->checkin_method) === 'manual')>Manual tai quay</option>
                        </select>
                    </div>
                </div>
            </section>
        </div>

        <div class="col-xl-4">
            <section class="panel-card sticky-xl-top top-space">
                <div class="panel-header">
                    <div>
                        <h3>Thao tac</h3>
                        <p>Luu thay doi lich hen.</p>
                    </div>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-brand btn-lg">
                        <i class="bi bi-save"></i>
                        Luu va cho duyet lai
                    </button>
                    <a href="{{ route('admin.visits.show', $visit) }}" class="btn btn-light">Quay lai chi tiet</a>
                </div>
                <hr>
                <ul class="form-note-list">
                    <li>Sau khi cap nhat, lich ve pending.</li>
                    <li>Host can phe duyet lai truoc khi check-in.</li>
                    <li>QR token giu nguyen, han QR duoc cap nhat theo gio hen moi.</li>
                </ul>
            </section>
        </div>
    </form>
@endsection

@push('scripts')
<script>
(() => {
    const input = document.getElementById('editPageHostNameInput');
    const hidden = document.getElementById('editPageHostEmployeeId');
    if (!input || !hidden) return;
    const options = Array.from(document.querySelectorAll('#editPageHostSuggestions option'));
    const sync = () => {
        const selected = options.find((option) => option.value.trim().toLowerCase() === input.value.trim().toLowerCase());
        hidden.value = selected?.dataset?.id || '';
    };
    input.addEventListener('input', sync);
    input.addEventListener('change', sync);
})();
</script>
@endpush

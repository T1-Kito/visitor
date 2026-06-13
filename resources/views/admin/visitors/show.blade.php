@extends('layouts.admin')

@section('title', 'Chi tiết khách | Visitor Management')
@section('page_title', $visitor->full_name)
@section('page_subtitle', 'Hồ sơ khách và lịch sử ra vào')

@section('content')
    <section class="entity-detail">
        <header class="entity-detail-head">
            <div class="entity-detail-identity">
                <div class="entity-detail-avatar">{{ strtoupper(mb_substr($visitor->full_name, 0, 1)) }}</div>
                <div>
                    <h2 class="entity-detail-title">{{ $visitor->full_name }}</h2>
                    <p class="entity-detail-subtitle">{{ $visitor->company ?: 'Khách cá nhân' }}</p>
                </div>
            </div>

            <div class="entity-detail-actions">
                <span class="status-badge status-approved">{{ $visitor->visits_count }} lượt</span>
                <a class="btn btn-light" href="{{ route('admin.visitors.index') }}">
                    <i class="bi bi-arrow-left"></i>
                    Quay lại
                </a>
                <button class="btn btn-brand" type="button" data-bs-toggle="modal" data-bs-target="#editVisitorModal">
                    <i class="bi bi-pencil"></i>
                    Sửa
                </button>
                @if ($visitor->visits_count === 0)
                    <form method="post" action="{{ route('admin.visitors.destroy', $visitor) }}" onsubmit="return confirm('Xóa hồ sơ khách này?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-outline-danger" type="submit" title="Xóa hồ sơ">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                @endif
            </div>
        </header>

        <div class="entity-detail-fields">
            <div class="entity-detail-field">
                <span>Mã khách</span>
                <strong>{{ $visitor->visitor_code }}</strong>
            </div>
            <div class="entity-detail-field">
                <span>Số điện thoại</span>
                <strong>{{ $visitor->phone ?? '-' }}</strong>
            </div>
            <div class="entity-detail-field">
                <span>Email</span>
                <strong title="{{ $visitor->email ?? '-' }}">{{ $visitor->email ?? '-' }}</strong>
            </div>
            <div class="entity-detail-field">
                <span>Công ty</span>
                <strong title="{{ $visitor->company ?? '-' }}">{{ $visitor->company ?? '-' }}</strong>
            </div>
            <div class="entity-detail-field">
                <span>CCCD / Hộ chiếu</span>
                <strong>{{ $visitor->identity_no ?? '-' }}</strong>
            </div>
            <div class="entity-detail-field">
                <span>Nơi cấp</span>
                <strong title="{{ $visitor->identity_issued_place ?? '-' }}">{{ $visitor->identity_issued_place ?? '-' }}</strong>
            </div>
            <div class="entity-detail-field">
                <span>Ngày cấp</span>
                <strong>{{ $visitor->identity_issued_date?->format('d/m/Y') ?? '-' }}</strong>
            </div>
            <div class="entity-detail-field">
                <span>Tổng lượt đến</span>
                <strong>{{ $visitor->visits_count }}</strong>
            </div>
        </div>

        @if ($visitor->note)
            <div class="entity-detail-note">
                <span>Ghi chú</span>
                {{ $visitor->note }}
            </div>
        @endif

        <div class="entity-detail-section-head">
            <div>
                <h3>Lịch sử ra vào</h3>
                <p>Các lịch hẹn và lượt ra vào gần nhất của khách.</p>
            </div>
            <span class="entity-detail-count">{{ $visits->count() }} lịch</span>
        </div>

        <div class="table-responsive">
            <table class="entity-detail-table">
                <thead>
                <tr>
                    <th>Mã lịch</th>
                    <th>Người tiếp</th>
                    <th>Phòng ban</th>
                    <th>Giờ hẹn</th>
                    <th>Trạng thái</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($visits as $visit)
                    <tr>
                        <td>
                            <a class="entity-detail-link" href="{{ route('admin.visits.show', $visit) }}">
                                {{ $visit->code }}
                            </a>
                        </td>
                        <td>{{ $visit->hostEmployee?->name ?? '-' }}</td>
                        <td>{{ $visit->hostEmployee?->department?->name ?? '-' }}</td>
                        <td>{{ $visit->scheduled_at?->format('H:i d/m/Y') ?? '-' }}</td>
                        <td><x-status-badge :status="$visit->status" /></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-secondary py-4">Khách chưa có lịch sử ra vào.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="modal fade resource-modal" id="editVisitorModal" tabindex="-1" aria-labelledby="editVisitorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <form class="modal-content" method="post" action="{{ route('admin.visitors.update', $visitor) }}">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="editVisitorModalLabel">Sửa hồ sơ khách</h5>
                        <div class="text-secondary small">Cập nhật thông tin nhận diện và liên hệ của khách.</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <div class="entity-form-grid">
                        <div>
                            <label class="form-label">Mã khách</label>
                            <input class="form-control" value="{{ $visitor->visitor_code }}" readonly>
                        </div>
                        <div>
                            <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                            <input class="form-control" name="full_name" value="{{ old('full_name', $visitor->full_name) }}" required>
                            @error('full_name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="form-label">Số điện thoại</label>
                            <input class="form-control" name="phone" value="{{ old('phone', $visitor->phone) }}">
                            @error('phone')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="form-label">Email</label>
                            <input class="form-control" type="email" name="email" value="{{ old('email', $visitor->email) }}">
                            @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="form-label">Công ty / Tổ chức</label>
                            <input class="form-control" name="company" value="{{ old('company', $visitor->company) }}">
                            @error('company')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="form-label">CCCD / Hộ chiếu</label>
                            <input class="form-control" name="identity_no" value="{{ old('identity_no', $visitor->identity_no) }}">
                            @error('identity_no')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="form-label">Nơi cấp</label>
                            <input class="form-control" name="identity_issued_place" value="{{ old('identity_issued_place', $visitor->identity_issued_place) }}">
                            @error('identity_issued_place')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="form-label">Ngày cấp</label>
                            <input class="form-control" type="date" name="identity_issued_date" value="{{ old('identity_issued_date', $visitor->identity_issued_date?->format('Y-m-d')) }}">
                            @error('identity_issued_date')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div class="entity-form-wide">
                            <label class="form-label">Ghi chú</label>
                            <textarea class="form-control" name="note" rows="3">{{ old('note', $visitor->note) }}</textarea>
                            @error('note')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                    <button class="btn btn-brand" type="submit">
                        <i class="bi bi-check2"></i>
                        Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@if ($errors->any())
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const modalElement = document.getElementById('editVisitorModal');
                if (modalElement) {
                    bootstrap.Modal.getOrCreateInstance(modalElement).show();
                }
            });
        </script>
    @endpush
@endif

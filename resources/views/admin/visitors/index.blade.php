@extends('layouts.admin')

@section('title', 'Khách')
@section('page_title', 'Quản lý khách')
@section('page_subtitle', 'Theo dõi hồ sơ khách, thông tin liên hệ và lịch sử ra vào')

@section('content')
@php
    $visitorCollection = collect($visitors);
    $totalVisitors = $visitorCollection->count();
    $totalVisits = $visitorCollection->sum('visits_count');
    $withCompany = $visitorCollection->filter(fn ($visitor) => filled($visitor->company))->count();
@endphp

<div class="resource-shell visitor-resource-shell">
    <section class="resource-summary resource-summary-dhl">
        <div class="resource-stat">
            <div class="resource-stat-icon"><i class="bi bi-people"></i></div>
            <div><span>Tổng hồ sơ khách</span><strong>{{ $totalVisitors }}</strong></div>
        </div>
        <div class="resource-stat">
            <div class="resource-stat-icon"><i class="bi bi-door-open"></i></div>
            <div><span>Tổng lượt ra vào</span><strong>{{ $totalVisits }}</strong></div>
        </div>
        <div class="resource-stat">
            <div class="resource-stat-icon"><i class="bi bi-building"></i></div>
            <div><span>Có thông tin công ty</span><strong>{{ $withCompany }}</strong></div>
        </div>
    </section>

    <section class="resource-card">
        <div class="resource-toolbar">
            <form method="get" action="{{ route('admin.visitors.index') }}">
                <div class="resource-search">
                    <i class="bi bi-search"></i>
                    <input class="form-control" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Tìm theo tên, số điện thoại, email, công ty...">
                </div>
                <button class="btn btn-light" type="submit"><i class="bi bi-funnel"></i> Lọc</button>
            </form>
            <button class="btn btn-brand" type="button" data-bs-toggle="modal" data-bs-target="#createVisitorModal">
                <i class="bi bi-plus-circle"></i>
                Tạo khách
            </button>
        </div>

        <div class="table-responsive">
            <table class="resource-table visitor-table">
                <thead>
                <tr>
                    <th>Khách</th>
                    <th class="visitor-code-column">Mã khách hàng</th>
                    <th>Số điện thoại</th>
                    <th>Email</th>
                    <th>Công ty</th>
                    <th class="text-center">Số lượt</th>
                    <th class="text-end">Thao tác</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($visitors as $visitor)
                    <tr>
                        <td>
                            <div class="resource-person">
                                <div class="resource-avatar">{{ strtoupper(mb_substr($visitor->full_name, 0, 1)) }}</div>
                                <div>
                                    <a class="resource-name" href="{{ route('admin.visitors.show', $visitor) }}">{{ $visitor->full_name }}</a>
                                    <span class="resource-muted">{{ $visitor->note ?: 'Chưa có ghi chú' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="visitor-code-column">
                            <span class="visitor-code-badge">
                                <i class="bi bi-person-badge"></i>
                                {{ $visitor->visitor_code }}
                            </span>
                        </td>
                        <td>{{ $visitor->phone ?? '-' }}</td>
                        <td>{{ $visitor->email ?? '-' }}</td>
                        <td>{{ $visitor->company ?? '-' }}</td>
                        <td class="text-center"><span class="resource-pill">{{ $visitor->visits_count }}</span></td>
                        <td>
                            <div class="resource-actions">
                                <a class="resource-icon-btn" href="{{ route('admin.visitors.show', $visitor) }}" title="Xem chi tiết"><i class="bi bi-eye"></i></a>
                                <a class="resource-icon-btn" href="{{ route('admin.visitors.edit', $visitor) }}" title="Sửa hồ sơ"><i class="bi bi-pencil"></i></a>
                                @if ($visitor->visits_count === 0)
                                    <form method="post" action="{{ route('admin.visitors.destroy', $visitor) }}" onsubmit="return confirm('Xóa hồ sơ khách này?')">
                                        @csrf
                                        @method('delete')
                                        <button class="resource-icon-btn danger" type="submit" title="Xóa hồ sơ"><i class="bi bi-trash"></i></button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="resource-empty" colspan="7">
                            <i class="bi bi-person-lines-fill d-block fs-1 mb-2"></i>
                            Chưa có hồ sơ khách phù hợp.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="resource-footer">
            <span>Hiển thị {{ $totalVisitors }} khách</span>
            <span class="text-secondary">Dữ liệu hồ sơ khách dùng lại khi tạo lịch hẹn.</span>
        </div>
    </section>
</div>

<div class="modal fade resource-modal" id="createVisitorModal" tabindex="-1" aria-labelledby="createVisitorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form class="modal-content" method="post" action="{{ route('admin.visitors.store') }}">
            @csrf
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="createVisitorModalLabel">Tạo hồ sơ khách</h5>
                    <div class="text-secondary small">Nhập thông tin khách để dùng lại khi tạo lịch hẹn.</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <div class="resource-form-grid">
                    <div>
                        <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                        <input class="form-control" name="full_name" value="{{ old('full_name') }}" placeholder="Ví dụ: Nguyễn Văn A" required>
                    </div>
                    <div>
                        <label class="form-label">Số điện thoại</label>
                        <input class="form-control" name="phone" value="{{ old('phone') }}" placeholder="0909 xxx xxx">
                    </div>
                    <div>
                        <label class="form-label">Email</label>
                        <input class="form-control" type="email" name="email" value="{{ old('email') }}" placeholder="visitor@company.com">
                    </div>
                    <div>
                        <label class="form-label">Công ty / tổ chức</label>
                        <input class="form-control" name="company" value="{{ old('company') }}" placeholder="Tên công ty">
                    </div>
                    <div class="resource-field-wide">
                        <label class="form-label">Số giấy tờ</label>
                        <input class="form-control" name="identity_no" value="{{ old('identity_no') }}" placeholder="CCCD / hộ chiếu nếu cần">
                    </div>
                    <div class="resource-field-wide">
                        <label class="form-label">Số thẻ khách</label>
                        <input class="form-control" name="visitor_id_card_number" value="{{ old('visitor_id_card_number') }}" placeholder="Nhập số thẻ khách">
                    </div>
                    <div class="resource-field-wide">
                        <label class="form-label">Ghi chú</label>
                        <textarea class="form-control" name="note" rows="3" placeholder="Ghi chú thêm cho lễ tân / bảo vệ">{{ old('note') }}</textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                <button class="btn btn-brand" type="submit"><i class="bi bi-check2-circle"></i> Lưu hồ sơ</button>
            </div>
        </form>
    </div>
</div>
@endsection

@if ($errors->any())
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            new bootstrap.Modal(document.getElementById('createVisitorModal')).show();
        });
    </script>
    @endpush
@endif

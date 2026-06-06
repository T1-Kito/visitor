@extends('layouts.admin')

@section('title', 'Chi tiet khach | Visitor Management')
@section('page_title', $visitor->full_name)
@section('page_subtitle', 'Ho so khach va lich su ra vao')

@section('content')
    <div class="row g-3">
        <div class="col-xl-8">
            <section class="panel-card mb-3">
                <div class="panel-header">
                    <div><h3>{{ $visitor->full_name }}</h3><p>{{ $visitor->company ?? 'Khach' }}</p></div>
                    <span class="status-badge status-approved">{{ $visitor->visits_count }} luot</span>
                </div>
                <div class="detail-grid">
                    <div class="detail-item"><span>Ma khach</span><strong>{{ $visitor->visitor_code }}</strong></div>
                    <div class="detail-item"><span>So dien thoai</span><strong>{{ $visitor->phone ?? '-' }}</strong></div>
                    <div class="detail-item"><span>Email</span><strong>{{ $visitor->email ?? '-' }}</strong></div>
                    <div class="detail-item"><span>Cong ty</span><strong>{{ $visitor->company ?? '-' }}</strong></div>
                    <div class="detail-item"><span>So giay to</span><strong>{{ $visitor->identity_no ?? '-' }}</strong></div>
                    <div class="detail-item"><span>Noi cap</span><strong>{{ $visitor->identity_issued_place ?? '-' }}</strong></div>
                    <div class="detail-item"><span>Ngay cap</span><strong>{{ $visitor->identity_issued_date?->format('d/m/Y') ?? '-' }}</strong></div>
                    <div class="detail-item detail-wide"><span>Ghi chu</span><strong>{{ $visitor->note ?? '-' }}</strong></div>
                </div>
            </section>

            <section class="panel-card">
                <div class="panel-header">
                    <div><h3>Lich su ra vao</h3><p>20 lich moi nhat cua khach.</p></div>
                </div>
                <div class="table-responsive">
                    <table class="table modern-table align-middle mb-0">
                        <thead>
                        <tr>
                            <th>Ma lich</th>
                            <th>Nguoi tiep</th>
                            <th>Phong ban</th>
                            <th>Gio hen</th>
                            <th>Trang thai</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($visits as $visit)
                            <tr>
                                <td><a class="fw-bold text-decoration-none" href="{{ route('admin.visits.show', $visit) }}">{{ $visit->code }}</a></td>
                                <td>{{ $visit->hostEmployee?->name ?? '-' }}</td>
                                <td>{{ $visit->hostEmployee?->department?->name ?? '-' }}</td>
                                <td>{{ $visit->scheduled_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                <td><x-status-badge :status="$visit->status" /></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-secondary">Khach chua co lich su ra vao.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
        <div class="col-xl-4">
            <section class="panel-card">
                <div class="panel-header"><div><h3>Thao tac</h3><p>Quan tri ho so khach.</p></div></div>
                <div class="d-grid gap-2">
                    <a class="btn btn-brand" href="{{ route('admin.visitors.edit', $visitor) }}">Sua ho so</a>
                    @if ($visitor->visits_count === 0)
                        <form method="post" action="{{ route('admin.visitors.destroy', $visitor) }}">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-outline-danger w-100" type="submit">Xoa ho so</button>
                        </form>
                    @endif
                    <a class="btn btn-light" href="{{ route('admin.visitors.index') }}">Quay lai danh sach</a>
                </div>
            </section>
        </div>
    </div>
@endsection

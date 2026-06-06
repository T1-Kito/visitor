@extends('layouts.admin')

@section('title', 'Thẻ ra vào')
@section('page_title', 'Quản lý thẻ ra vào')
@section('page_subtitle', 'Theo dõi thẻ đang sử dụng, đã thu hồi và sẵn sàng cấp')

@section('content')
    <section class="panel-card">
        <div class="table-responsive">
            <table class="table modern-table align-middle mb-0">
                <thead>
                <tr>
                    <th>Mã thẻ</th>
                    <th>Trạng thái</th>
                    <th>Khách</th>
                    <th>Lịch hẹn</th>
                    <th>Khu vực</th>
                    <th>Cấp lúc</th>
                    <th>Hết hạn</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($badges as $badge)
                    <tr>
                        <td><strong>{{ $badge->badge_no }}</strong></td>
                        <td>
                            @if ($badge->status === 'active')
                                <span class="status-badge status-checked-in">Đang sử dụng</span>
                            @elseif ($badge->status === 'revoked')
                                <span class="status-badge status-checked-out">Đã thu hồi</span>
                            @else
                                <span class="status-badge status-approved">Sẵn sàng cấp</span>
                            @endif
                        </td>
                        <td>{{ $badge->visit?->visitor?->full_name ?? '-' }}</td>
                        <td>{{ $badge->visit?->code ?? '-' }}</td>
                        <td>{{ $badge->visit?->access_zone ?? '-' }}</td>
                        <td>{{ $badge->issued_at?->format('H:i - d/m/Y') ?? '-' }}</td>
                        <td>{{ $badge->valid_until?->format('H:i - d/m/Y') ?? '-' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection

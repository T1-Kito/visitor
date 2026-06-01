@extends('layouts.admin')

@section('title', 'Badges | Visitor Management')
@section('page_title', 'Quan ly badge')
@section('page_subtitle', 'Theo doi badge dang cap, da thu hoi va san sang su dung')

@section('content')
    <section class="panel-card">
        <div class="table-responsive">
            <table class="table modern-table align-middle mb-0">
                <thead>
                <tr>
                    <th>Badge</th>
                    <th>Trang thai</th>
                    <th>Khach</th>
                    <th>Lich hen</th>
                    <th>Khu vuc</th>
                    <th>Cap luc</th>
                    <th>Het han</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($badges as $badge)
                    <tr>
                        <td><strong>{{ $badge->badge_no }}</strong></td>
                        <td>
                            @if ($badge->status === 'active')
                                <span class="status-badge status-checked-in">Active</span>
                            @elseif ($badge->status === 'revoked')
                                <span class="status-badge status-checked-out">Revoked</span>
                            @else
                                <span class="status-badge status-approved">Available</span>
                            @endif
                        </td>
                        <td>{{ $badge->visit?->visitor?->full_name ?? '-' }}</td>
                        <td>{{ $badge->visit?->code ?? '-' }}</td>
                        <td>{{ $badge->visit?->access_zone ?? '-' }}</td>
                        <td>{{ $badge->issued_at?->format('Y-m-d H:i') ?? '-' }}</td>
                        <td>{{ $badge->valid_until?->format('Y-m-d H:i') ?? '-' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection

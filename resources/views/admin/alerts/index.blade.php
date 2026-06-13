@extends('layouts.admin')

@section('title', 'Cảnh báo | Visitor Management')
@section('page_title', 'Cảnh báo vận hành')
@section('page_subtitle', 'Theo dõi khách quá giờ, chưa check-in và lịch sắp đến giờ')

@push('styles')
    <style>
        .operation-alerts {
            overflow: hidden;
            border: 1px solid #dde6ef;
            border-radius: 10px;
            background: #fff;
            box-shadow: 0 8px 24px rgba(15, 23, 42, .05);
        }

        .operation-alerts-head {
            display: flex;
            min-height: 64px;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 12px 18px;
            border-bottom: 1px solid #e8edf3;
        }

        .operation-alerts-head h2 {
            margin: 0 0 2px;
            color: #172033;
            font-size: 17px;
            font-weight: 600;
        }

        .operation-alerts-head p {
            margin: 0;
            color: #7a8a9e;
            font-size: 13px;
        }

        .operation-alerts-head .btn {
            display: inline-flex;
            min-height: 34px;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 7px;
            font-size: 13px;
            font-weight: 500;
        }

        .operation-alert-list {
            display: grid;
        }

        .operation-alert-row {
            position: relative;
            display: grid;
            min-height: 60px;
            grid-template-columns: minmax(0, 1fr) auto;
            align-items: center;
            gap: 18px;
            padding: 9px 18px 9px 22px;
            border-bottom: 1px solid #edf1f5;
            background: #fff;
        }

        .operation-alert-row:last-child {
            border-bottom: 0;
        }

        .operation-alert-row::before {
            position: absolute;
            top: 10px;
            bottom: 10px;
            left: 0;
            width: 3px;
            border-radius: 0 3px 3px 0;
            background: #d99a16;
            content: "";
        }

        .operation-alert-row.danger::before {
            background: #dc3545;
        }

        .operation-alert-row:hover {
            background: #fafbfc;
        }

        .operation-alert-title {
            overflow: hidden;
            margin-bottom: 2px;
            color: #263244;
            font-size: 14px;
            font-weight: 600;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .operation-alert-message {
            overflow: hidden;
            color: #6f7f92;
            font-size: 13px;
            line-height: 1.35;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .operation-alert-time {
            color: #718096;
            font-size: 13px;
            font-variant-numeric: tabular-nums;
            white-space: nowrap;
        }

        .operation-alert-empty {
            padding: 30px 18px;
            color: #64748b;
            font-size: 14px;
            text-align: center;
        }

        @media (max-width: 767.98px) {
            .operation-alert-row {
                min-height: 66px;
                grid-template-columns: minmax(0, 1fr);
                gap: 3px;
                padding-right: 14px;
            }

            .operation-alert-title,
            .operation-alert-message {
                white-space: normal;
            }

            .operation-alert-time {
                position: absolute;
                top: 10px;
                right: 14px;
                font-size: 12px;
            }

            .operation-alert-title {
                padding-right: 52px;
            }
        }
    </style>
@endpush

@section('content')
    <section class="operation-alerts">
        <header class="operation-alerts-head">
            <div>
                <h2>Danh sách cảnh báo</h2>
                <p>{{ count($alerts) }} cảnh báo đang cần theo dõi.</p>
            </div>
            <a class="btn btn-light" href="{{ route('admin.alerts.index') }}">
                <i class="bi bi-arrow-clockwise"></i>
                Làm mới
            </a>
        </header>

        <div class="operation-alert-list">
            @forelse ($alerts as $alert)
                <article class="operation-alert-row {{ $alert['level'] === 'danger' ? 'danger' : 'warning' }}">
                    <div>
                        <div class="operation-alert-title">{{ $alert['title'] }}</div>
                        <div class="operation-alert-message">{{ $alert['message'] }}</div>
                    </div>
                    <time class="operation-alert-time">{{ $alert['time'] }}</time>
                </article>
            @empty
                <div class="operation-alert-empty">
                    <i class="bi bi-check-circle me-1"></i>
                    Không có cảnh báo bất thường trong ca trực hiện tại.
                </div>
            @endforelse
        </div>
    </section>
@endsection

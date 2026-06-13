@extends('layouts.admin')

@section('title', 'Chi tiết nhân viên | Visitor Management')
@section('page_title', $employee->name)
@section('page_subtitle', 'Hồ sơ nhân viên và lịch tiếp khách gần đây')

@section('content')
    <style>
        .employee-profile {
            overflow: hidden;
            border: 1px solid #dde6ef;
            border-radius: 10px;
            background: #fff;
            box-shadow: 0 8px 24px rgba(15, 23, 42, .05);
        }

        .employee-profile-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding: 22px 26px;
            border-bottom: 1px solid #e8edf3;
        }

        .employee-profile-person {
            display: flex;
            align-items: center;
            min-width: 0;
            gap: 14px;
        }

        .employee-profile-avatar {
            display: grid;
            width: 46px;
            height: 46px;
            flex: 0 0 46px;
            place-items: center;
            border-radius: 8px;
            background: #f3f6f9;
            color: #d40511;
            font-size: 18px;
            font-weight: 600;
        }

        .employee-profile-name {
            margin: 0 0 3px;
            color: #172033;
            font-size: 20px;
            font-weight: 600;
        }

        .employee-profile-role {
            margin: 0;
            color: #718096;
            font-size: 14px;
        }

        .employee-profile-actions {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            justify-content: flex-end;
            gap: 8px;
        }

        .employee-profile-actions .btn {
            display: inline-flex;
            min-height: 36px;
            align-items: center;
            gap: 7px;
            padding: 7px 12px;
            border-radius: 7px;
            font-size: 14px;
            font-weight: 500;
            white-space: nowrap;
        }

        .employee-profile-info {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            padding: 0 26px;
        }

        .employee-profile-field {
            min-width: 0;
            padding: 20px 20px 20px 0;
        }

        .employee-profile-field + .employee-profile-field {
            padding-left: 20px;
            border-left: 1px solid #edf1f5;
        }

        .employee-profile-field span {
            display: block;
            margin-bottom: 6px;
            color: #7a8a9e;
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
        }

        .employee-profile-field strong {
            display: block;
            overflow: hidden;
            color: #172033;
            font-size: 14px;
            font-weight: 500;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .employee-history-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 18px 26px 14px;
            border-top: 1px solid #e8edf3;
            border-bottom: 1px solid #e8edf3;
        }

        .employee-history-head h3 {
            margin: 0 0 3px;
            color: #172033;
            font-size: 17px;
            font-weight: 600;
        }

        .employee-history-head p {
            margin: 0;
            color: #7a8a9e;
            font-size: 13px;
        }

        .employee-history-count {
            padding: 5px 9px;
            border-radius: 999px;
            background: #f3f6f9;
            color: #526174;
            font-size: 12px;
            font-weight: 500;
        }

        .employee-history-table {
            width: 100%;
            border-collapse: collapse;
        }

        .employee-history-table th {
            padding: 11px 26px;
            border-bottom: 1px solid #e8edf3;
            background: #fafbfc;
            color: #78879a;
            font-size: 12px;
            font-weight: 500;
            text-align: left;
            text-transform: uppercase;
        }

        .employee-history-table td {
            height: 58px;
            padding: 10px 26px;
            border-bottom: 1px solid #edf1f5;
            color: #263244;
            font-size: 14px;
        }

        .employee-history-table tbody tr:last-child td {
            border-bottom: 0;
        }

        .employee-history-code {
            color: #1565c0;
            font-weight: 500;
            text-decoration: none;
        }

        @media (max-width: 991.98px) {
            .employee-profile-info {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .employee-profile-field:nth-child(3) {
                padding-left: 0;
                border-left: 0;
                border-top: 1px solid #edf1f5;
            }

            .employee-profile-field:nth-child(4) {
                border-top: 1px solid #edf1f5;
            }
        }

        @media (max-width: 575.98px) {
            .employee-profile-head {
                align-items: flex-start;
                flex-direction: column;
                padding: 18px;
            }

            .employee-profile-actions {
                width: 100%;
                justify-content: flex-start;
            }

            .employee-profile-info {
                grid-template-columns: 1fr;
                padding: 0 18px;
            }

            .employee-profile-field,
            .employee-profile-field + .employee-profile-field {
                padding: 15px 0;
                border-top: 1px solid #edf1f5;
                border-left: 0;
            }

            .employee-profile-field:first-child {
                border-top: 0;
            }

            .employee-history-head {
                padding: 16px 18px 12px;
            }

            .employee-history-table th,
            .employee-history-table td {
                padding-right: 18px;
                padding-left: 18px;
            }
        }
    </style>

    <section class="employee-profile">
        <header class="employee-profile-head">
            <div class="employee-profile-person">
                <div class="employee-profile-avatar">{{ strtoupper(mb_substr($employee->name, 0, 1)) }}</div>
                <div>
                    <h2 class="employee-profile-name">{{ $employee->name }}</h2>
                    <p class="employee-profile-role">{{ $employee->job_title ?: 'Nhân viên' }}</p>
                </div>
            </div>

            <div class="employee-profile-actions">
                <span class="status-badge {{ $employee->is_active ? 'status-approved' : 'status-checked-out' }}">
                    {{ $employee->is_active ? 'Đang hoạt động' : 'Ngừng hoạt động' }}
                </span>
                <a class="btn btn-light" href="{{ route('admin.employees.index') }}">
                    <i class="bi bi-arrow-left"></i>
                    Quay lại
                </a>
                <button class="btn btn-brand"
                        type="button"
                        data-bs-toggle="modal"
                        data-bs-target="#editEmployeeModal"
                        data-edit-employee
                        data-employee-id="{{ $employee->id }}"
                        data-employee-name="{{ $employee->name }}"
                        data-employee-email="{{ $employee->email }}"
                        data-employee-phone="{{ $employee->phone }}"
                        data-employee-job-title="{{ $employee->job_title }}"
                        data-department-id="{{ $employee->department_id }}"
                        data-employee-active="{{ $employee->is_active ? '1' : '0' }}"
                        data-update-url="{{ route('admin.employees.update', $employee) }}">
                    <i class="bi bi-pencil"></i>
                    Sửa
                </button>
                @if ($visits->isEmpty())
                    <form method="post" action="{{ route('admin.employees.destroy', $employee) }}" onsubmit="return confirm('Xóa nhân viên này?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-outline-danger" type="submit" title="Xóa nhân viên">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                @endif
            </div>
        </header>

        <div class="employee-profile-info">
            <div class="employee-profile-field">
                <span>Email</span>
                <strong title="{{ $employee->email ?? '-' }}">{{ $employee->email ?? '-' }}</strong>
            </div>
            <div class="employee-profile-field">
                <span>Số điện thoại</span>
                <strong>{{ $employee->phone ?? '-' }}</strong>
            </div>
            <div class="employee-profile-field">
                <span>Phòng ban</span>
                <strong>{{ $employee->department?->name ?? '-' }}</strong>
            </div>
            <div class="employee-profile-field">
                <span>Tài khoản đăng nhập</span>
                <strong title="{{ $employee->user?->email ?? '-' }}">{{ $employee->user?->email ?? '-' }}</strong>
            </div>
        </div>

        <div class="employee-history-head">
            <div>
                <h3>Lịch tiếp khách gần đây</h3>
                <p>Các lịch hẹn mới nhất do nhân viên này tiếp nhận.</p>
            </div>
            <span class="employee-history-count">{{ $visits->count() }} lịch</span>
        </div>

        <div class="table-responsive">
            <table class="employee-history-table">
                <thead>
                <tr>
                    <th>Mã lịch</th>
                    <th>Khách</th>
                    <th>Giờ hẹn</th>
                    <th>Trạng thái</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($visits as $visit)
                    <tr>
                        <td>
                            <a class="employee-history-code" href="{{ route('admin.visits.show', $visit) }}">
                                {{ $visit->code }}
                            </a>
                        </td>
                        <td>{{ $visit->visitor?->full_name ?? '-' }}</td>
                        <td>{{ $visit->scheduled_at?->format('H:i d/m/Y') ?? '-' }}</td>
                        <td><x-status-badge :status="$visit->status" /></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-secondary py-4">Chưa có lịch tiếp khách.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @include('admin.employees.partials.edit-modal')
@endsection

@if ($errors->any() && old('form_context') === 'edit_employee')
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modalElement = document.getElementById('editEmployeeModal');
            if (modalElement) {
                new bootstrap.Modal(modalElement).show();
            }
        });
    </script>
    @endpush
@endif

@extends('layouts.admin')

@section('title', 'Audit Logs | Visitor Management')
@section('page_title', 'Audit Logs')
@section('page_subtitle', 'Theo doi toan bo thao tac quan trong trong he thong')

@section('content')
    <section class="panel-card mb-3">
        <form class="row g-3" method="get" action="{{ route('admin.audit-logs.index') }}">
            <div class="col-md-5">
                <label class="form-label">Loc theo action</label>
                <input type="text" class="form-control" name="action" value="{{ $filters['action'] }}" placeholder="Vi du: visit.checked_in">
            </div>
            <div class="col-md-5">
                <label class="form-label">Loc theo user</label>
                <select name="user_id" class="form-select">
                    <option value="">Tat ca user</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" @selected($filters['user_id'] === (string) $user->id)>
                            {{ $user->name }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-brand w-100" type="submit">Loc</button>
            </div>
        </form>
    </section>

    <section class="panel-card">
        <div class="table-responsive">
            <table class="table modern-table align-middle mb-0">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Thoi gian</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Doi tuong</th>
                    <th>Meta</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($logs as $log)
                    <tr>
                        <td>{{ $log->id }}</td>
                        <td>{{ $log->created_at?->format('Y-m-d H:i:s') }}</td>
                        <td>{{ $log->user?->email ?? 'system' }}</td>
                        <td><code>{{ $log->action }}</code></td>
                        <td>{{ $log->entity_type }}#{{ $log->entity_id }}</td>
                        <td>
                            @if (!empty($log->meta))
                                <code>{{ json_encode($log->meta, JSON_UNESCAPED_UNICODE) }}</code>
                            @else
                                <span class="text-secondary">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">Chua co log nao.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $logs->links() }}
        </div>
    </section>
@endsection

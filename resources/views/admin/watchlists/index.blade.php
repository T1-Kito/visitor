@extends('layouts.admin')

@section('title', 'Watchlist | Visitor Management')
@section('page_title', 'Watchlist an ninh')
@section('page_subtitle', 'Quan ly danh sach can canh bao khi khach tao lich, walk-in hoac check-in')

@section('content')
    <div class="row g-3">
        <div class="col-xl-4">
            <section class="panel-card h-100">
                <div class="panel-header">
                    <div>
                        <h3>Them watchlist rule</h3>
                        <p>Rule co the gan thang visitor hoac match theo keyword.</p>
                    </div>
                </div>
                <form class="d-grid gap-3" method="post" action="{{ route('admin.watchlists.store') }}">
                    @csrf
                    <div>
                        <label class="form-label">Gan visitor co san</label>
                        <select class="form-select" name="visitor_id">
                            <option value="">Khong gan visitor cu the</option>
                            @foreach ($visitors as $visitor)
                                <option value="{{ $visitor->id }}" @selected((string) old('visitor_id') === (string) $visitor->id)>
                                    {{ $visitor->full_name }} - {{ $visitor->phone ?? $visitor->email ?? 'No contact' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Keyword</label>
                        <input class="form-control" name="keyword" value="{{ old('keyword') }}" placeholder="Ten, phone, email, cong ty, CCCD..." required>
                    </div>
                    <div>
                        <label class="form-label">Kieu match</label>
                        <select class="form-select" name="match_type" required>
                            @foreach ($matchTypes as $value => $label)
                                <option value="{{ $value }}" @selected(old('match_type', 'any') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label">Muc do</label>
                            <select class="form-select" name="level" required>
                                @foreach ($levels as $value => $label)
                                    <option value="{{ $value }}" @selected(old('level', 'warning') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Trang thai</label>
                            <select class="form-select" name="status" required>
                                <option value="active" @selected(old('status', 'active') === 'active')>Active</option>
                                <option value="inactive" @selected(old('status') === 'inactive')>Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Ly do</label>
                        <input class="form-control" name="reason" value="{{ old('reason') }}" placeholder="Ly do dua vao watchlist" required>
                    </div>
                    <div>
                        <label class="form-label">Ghi chu noi bo</label>
                        <textarea class="form-control" name="note" rows="3">{{ old('note') }}</textarea>
                    </div>
                    <button class="btn btn-brand" type="submit">Them watchlist</button>
                </form>
            </section>
        </div>

        <div class="col-xl-8">
            <section class="panel-card">
                <div class="panel-header">
                    <div>
                        <h3>Danh sach watchlist</h3>
                        <p>Loc rule active/inactive va xem cac visit match.</p>
                    </div>
                </div>
                <form class="row g-2 mb-3" method="get" action="{{ route('admin.watchlists.index') }}">
                    <div class="col-md-7">
                        <input class="form-control" name="q" value="{{ $filters['q'] }}" placeholder="Tim keyword, ly do, ten visitor">
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="status">
                            <option value="all" @selected($filters['status'] === 'all')>Tat ca</option>
                            <option value="active" @selected($filters['status'] === 'active')>Active</option>
                            <option value="inactive" @selected($filters['status'] === 'inactive')>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-grid">
                        <button class="btn btn-brand" type="submit">Loc</button>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table modern-table align-middle mb-0">
                        <thead>
                        <tr>
                            <th>Keyword</th>
                            <th>Match</th>
                            <th>Muc do</th>
                            <th>Trang thai</th>
                            <th class="text-end">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($watchlists as $watchlist)
                            <tr>
                                <td>
                                    <a class="fw-bold text-decoration-none" href="{{ route('admin.watchlists.show', $watchlist) }}">
                                        {{ $watchlist->keyword }}
                                    </a>
                                    <div class="text-secondary small">{{ $watchlist->reason }}</div>
                                </td>
                                <td>{{ $matchTypes[$watchlist->match_type] ?? $watchlist->match_type }}</td>
                                <td>
                                    <span class="status-badge {{ $watchlist->level === 'critical' ? 'status-rejected' : 'status-pending' }}">
                                        {{ $levels[$watchlist->level] ?? $watchlist->level }}
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge {{ $watchlist->status === 'active' ? 'status-approved' : 'status-checked-out' }}">
                                        {{ $watchlist->status }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex flex-wrap gap-2 justify-content-end">
                                        <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.watchlists.show', $watchlist) }}">Xem</a>
                                        <a class="btn btn-sm btn-light" href="{{ route('admin.watchlists.edit', $watchlist) }}">Sua</a>
                                        <form method="post" action="{{ route('admin.watchlists.destroy', $watchlist) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" type="submit">Xoa</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-secondary">Chua co watchlist rule.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $watchlists->links() }}
                </div>
            </section>
        </div>
    </div>
@endsection

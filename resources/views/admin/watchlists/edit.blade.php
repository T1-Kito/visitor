@extends('layouts.admin')

@section('title', 'Sua watchlist | Visitor Management')
@section('page_title', 'Sua watchlist '.$watchlist->keyword)
@section('page_subtitle', 'Cap nhat rule canh bao an ninh')

@section('content')
    <form class="row g-3" method="post" action="{{ route('admin.watchlists.update', $watchlist) }}">
        @csrf
        @method('PUT')
        <div class="col-xl-8">
            <section class="panel-card">
                <div class="panel-header">
                    <div>
                        <h3>Thong tin rule</h3>
                        <p>Rule active se duoc scan khi tao lich, walk-in va check-in.</p>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Gan visitor co san</label>
                        <select class="form-select" name="visitor_id">
                            <option value="">Khong gan visitor cu the</option>
                            @foreach ($visitors as $visitor)
                                <option value="{{ $visitor->id }}" @selected((string) old('visitor_id', $watchlist->visitor_id) === (string) $visitor->id)>
                                    {{ $visitor->full_name }} - {{ $visitor->phone ?? $visitor->email ?? 'No contact' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Keyword</label>
                        <input class="form-control" name="keyword" value="{{ old('keyword', $watchlist->keyword) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Kieu match</label>
                        <select class="form-select" name="match_type" required>
                            @foreach ($matchTypes as $value => $label)
                                <option value="{{ $value }}" @selected(old('match_type', $watchlist->match_type) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Muc do</label>
                        <select class="form-select" name="level" required>
                            @foreach ($levels as $value => $label)
                                <option value="{{ $value }}" @selected(old('level', $watchlist->level) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Trang thai</label>
                        <select class="form-select" name="status" required>
                            <option value="active" @selected(old('status', $watchlist->status) === 'active')>Active</option>
                            <option value="inactive" @selected(old('status', $watchlist->status) === 'inactive')>Inactive</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Ly do</label>
                        <input class="form-control" name="reason" value="{{ old('reason', $watchlist->reason) }}" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Ghi chu noi bo</label>
                        <textarea class="form-control" name="note" rows="4">{{ old('note', $watchlist->note) }}</textarea>
                    </div>
                </div>
            </section>
        </div>

        <div class="col-xl-4">
            <section class="panel-card sticky-xl-top top-space">
                <div class="d-grid gap-2">
                    <button class="btn btn-brand btn-lg" type="submit">Luu thay doi</button>
                    <a class="btn btn-light" href="{{ route('admin.watchlists.show', $watchlist) }}">Quay lai chi tiet</a>
                </div>
            </section>
        </div>
    </form>
@endsection

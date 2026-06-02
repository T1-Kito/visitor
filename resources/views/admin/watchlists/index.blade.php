@extends('layouts.admin')

@section('title', 'Danh sách cảnh báo')
@section('page_title', 'Danh sách cảnh báo an ninh')
@section('page_subtitle', 'Quản lý quy tắc cảnh báo khi khách tạo lịch, đăng ký walk-in hoặc làm thủ tục vào')

@push('styles')
<style>
.watchlist-shell{display:grid;gap:1.25rem}.watchlist-summary{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:1rem}.watchlist-stat{position:relative;overflow:hidden;padding:1.1rem;border:1px solid #dce9f8;border-radius:22px;background:#fff;box-shadow:0 14px 34px rgba(17,39,68,.05)}.watchlist-stat:after{content:"";position:absolute;right:-34px;top:-34px;width:92px;height:92px;border-radius:50%;background:var(--stat-bg,#eaf4ff)}.watchlist-stat span{display:block;color:#64748b;font-size:.78rem;font-weight:800}.watchlist-stat strong{display:block;margin:.25rem 0;color:#0b1f3a;font-size:2rem;font-weight:900;letter-spacing:-.04em}.watchlist-stat small{color:#7a93b0}.watchlist-card{border:1px solid #dce9f8;border-radius:24px;background:#fff;box-shadow:0 16px 42px rgba(17,39,68,.06);overflow:hidden}.watchlist-card-head{display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;padding:1.15rem 1.25rem;border-bottom:1px solid #edf3fb}.watchlist-card-head h2{margin:0;color:#0b1f3a;font-size:1.05rem;font-weight:900}.watchlist-card-head p{margin:.25rem 0 0;color:#64748b;font-size:.86rem}.watchlist-add-btn{min-height:44px;padding:0 1rem;border:0;border-radius:14px;background:linear-gradient(135deg,#146bd7,#0cb4d8);color:#fff;font-weight:900;box-shadow:0 12px 24px rgba(20,107,215,.18)}.watchlist-toolbar{display:grid;grid-template-columns:1fr 220px auto;gap:.75rem;padding:1.15rem 1.25rem;border-bottom:1px solid #edf3fb}.watchlist-search{position:relative}.watchlist-search i{position:absolute;left:.9rem;top:50%;transform:translateY(-50%);color:#7a93b0}.watchlist-search .form-control{padding-left:2.45rem}.watchlist-toolbar .form-control,.watchlist-toolbar .form-select{min-height:48px;border-color:#d8e5f2;border-radius:15px}.watchlist-table{width:100%;border-collapse:separate;border-spacing:0}.watchlist-table th{padding:.85rem 1.25rem;color:#6f88a4;font-size:.72rem;font-weight:900;text-transform:uppercase;border-bottom:1px solid #edf3fb;background:#fbfdff}.watchlist-table td{padding:1rem 1.25rem;border-bottom:1px solid #edf3fb;vertical-align:middle}.watchlist-table tbody tr{transition:.15s}.watchlist-table tbody tr:hover{background:#f6fbff}.watchlist-keyword{display:flex;gap:.85rem;align-items:center}.watchlist-mark{width:42px;height:42px;display:grid;place-items:center;border-radius:15px;background:#eaf4ff;color:#0b6fe8;font-weight:900}.watchlist-keyword a{display:block;color:#0b1f3a;font-weight:900;text-decoration:none}.watchlist-keyword small{display:block;color:#7a93b0;line-height:1.35}.watchlist-actions{display:flex;justify-content:flex-end;gap:.45rem}.watchlist-icon-btn{width:36px;height:36px;display:grid;place-items:center;border:1px solid #d8e5f2;border-radius:12px;background:#fff;color:#146bd7;text-decoration:none}.watchlist-icon-btn:hover{background:#eff6ff}.watchlist-icon-btn.danger{color:#dc2626;border-color:#fecaca;background:#fff7f7}.watchlist-empty{padding:3rem;text-align:center;color:#7a93b0}.watchlist-footer{padding:1rem 1.25rem}.watchlist-level-critical{background:#fff1f2;color:#be123c;border-color:#fecdd3}.watchlist-level-warning{background:#fff7ed;color:#c2410c;border-color:#fed7aa}.watchlist-level-info{background:#eff6ff;color:#1d4ed8;border-color:#bfdbfe}.watchlist-modal .modal-content{border:0;border-radius:24px;box-shadow:0 28px 80px rgba(11,31,58,.24)}.watchlist-modal .modal-header{padding:1.1rem 1.25rem;border-color:#edf3fb}.watchlist-modal .modal-title{color:#0b1f3a;font-size:1.05rem;font-weight:900}.watchlist-form{display:grid;gap:.9rem}.watchlist-form .form-label{margin-bottom:.35rem;color:#29435f;font-size:.78rem;font-weight:900}.watchlist-form .form-control,.watchlist-form .form-select{min-height:46px;border-color:#d8e5f2;border-radius:14px}.watchlist-form textarea.form-control{min-height:92px}.watchlist-soft-note{display:flex;gap:.7rem;align-items:flex-start;padding:.9rem;border:1px solid #bfdbfe;border-radius:16px;background:#eff6ff;color:#1e40af;font-size:.84rem}.watchlist-soft-note i{font-size:1.1rem}.watchlist-modal-footer{display:flex;justify-content:flex-end;gap:.7rem;padding:1rem 1.25rem;border-top:1px solid #edf3fb}
@media(max-width:1200px){.watchlist-summary{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:768px){.watchlist-toolbar{grid-template-columns:1fr}.watchlist-summary{grid-template-columns:1fr}.watchlist-table{min-width:780px}.watchlist-card-head{flex-direction:column}.watchlist-add-btn{width:100%}}
</style>
@endpush

@section('content')
@php
    $activeCount = collect($watchlists->items())->where('status', 'active')->count();
    $inactiveCount = collect($watchlists->items())->where('status', 'inactive')->count();
    $criticalCount = collect($watchlists->items())->where('level', 'critical')->count();
    $levelLabels = [
        'info' => 'Thông tin',
        'warning' => 'Cảnh báo',
        'critical' => 'Nghiêm trọng',
    ];
    $matchLabels = [
        'any' => 'Bất kỳ thông tin nào',
        'name' => 'Tên khách',
        'phone' => 'Số điện thoại',
        'email' => 'Email',
        'company' => 'Công ty',
        'identity' => 'Giấy tờ',
    ];
    $statusLabels = [
        'active' => 'Đang áp dụng',
        'inactive' => 'Tạm tắt',
    ];
@endphp

<div class="watchlist-shell">
    <section class="watchlist-summary">
        <div class="watchlist-stat" style="--stat-bg:#eaf4ff">
            <span>Tổng quy tắc</span>
            <strong>{{ $watchlists->total() }}</strong>
            <small>Đang quản lý trong hệ thống</small>
        </div>
        <div class="watchlist-stat" style="--stat-bg:#dcfce7">
            <span>Đang áp dụng</span>
            <strong>{{ $activeCount }}</strong>
            <small>Trong trang hiện tại</small>
        </div>
        <div class="watchlist-stat" style="--stat-bg:#fff7ed">
            <span>Cần theo dõi</span>
            <strong>{{ $criticalCount }}</strong>
            <small>Mức nghiêm trọng</small>
        </div>
        <div class="watchlist-stat" style="--stat-bg:#f1f5f9">
            <span>Tạm tắt</span>
            <strong>{{ $inactiveCount }}</strong>
            <small>Không cảnh báo khi khớp</small>
        </div>
    </section>

    <section class="watchlist-card">
        <div class="watchlist-card-head">
            <div>
                <h2>Danh sách cảnh báo</h2>
                <p>Lọc quy tắc đang áp dụng hoặc tạm tắt để kiểm tra nhanh.</p>
            </div>
            <button class="watchlist-add-btn" type="button" data-bs-toggle="modal" data-bs-target="#watchlistCreateModal">
                <i class="bi bi-shield-plus me-1"></i>
                Thêm quy tắc
            </button>
        </div>

        <form class="watchlist-toolbar" method="get" action="{{ route('admin.watchlists.index') }}">
            <div class="watchlist-search">
                <i class="bi bi-search"></i>
                <input class="form-control form-control-lg" name="q" value="{{ $filters['q'] }}" placeholder="Tìm từ khóa, lý do, tên khách...">
            </div>
            <select class="form-select form-select-lg" name="status">
                <option value="all" @selected($filters['status'] === 'all')>Tất cả trạng thái</option>
                <option value="active" @selected($filters['status'] === 'active')>Đang áp dụng</option>
                <option value="inactive" @selected($filters['status'] === 'inactive')>Tạm tắt</option>
            </select>
            <button class="btn btn-brand btn-lg" type="submit">
                <i class="bi bi-funnel"></i>
                Lọc
            </button>
        </form>

        <div class="table-responsive">
            <table class="watchlist-table">
                <thead>
                <tr>
                    <th>Từ khóa</th>
                    <th>Kiểu kiểm tra</th>
                    <th>Mức độ</th>
                    <th>Trạng thái</th>
                    <th class="text-end">Thao tác</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($watchlists as $watchlist)
                    <tr>
                        <td>
                            <div class="watchlist-keyword">
                                <div class="watchlist-mark">{{ strtoupper(mb_substr($watchlist->keyword, 0, 1)) }}</div>
                                <div>
                                    <a href="{{ route('admin.watchlists.show', $watchlist) }}">{{ $watchlist->keyword }}</a>
                                    <small>{{ $watchlist->reason }}</small>
                                </div>
                            </div>
                        </td>
                        <td>{{ $matchLabels[$watchlist->match_type] ?? ($matchTypes[$watchlist->match_type] ?? $watchlist->match_type) }}</td>
                        <td>
                            <span class="status-badge watchlist-level-{{ $watchlist->level }}">
                                {{ $levelLabels[$watchlist->level] ?? ($levels[$watchlist->level] ?? $watchlist->level) }}
                            </span>
                        </td>
                        <td>
                            <span class="status-badge {{ $watchlist->status === 'active' ? 'status-approved' : 'status-checked-out' }}">
                                {{ $statusLabels[$watchlist->status] ?? $watchlist->status }}
                            </span>
                        </td>
                        <td>
                            <div class="watchlist-actions">
                                <a class="watchlist-icon-btn" href="{{ route('admin.watchlists.show', $watchlist) }}" title="Xem chi tiết">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a class="watchlist-icon-btn" href="{{ route('admin.watchlists.edit', $watchlist) }}" title="Sửa quy tắc">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="post" action="{{ route('admin.watchlists.destroy', $watchlist) }}" onsubmit="return confirm('Xóa quy tắc cảnh báo này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="watchlist-icon-btn danger" type="submit" title="Xóa quy tắc">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="watchlist-empty" colspan="5">
                            <i class="bi bi-shield-exclamation d-block fs-1 mb-2"></i>
                            Chưa có quy tắc cảnh báo phù hợp.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="watchlist-footer">
            {{ $watchlists->links() }}
        </div>
    </section>
</div>

<div class="modal fade watchlist-modal" id="watchlistCreateModal" tabindex="-1" aria-labelledby="watchlistCreateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form class="modal-content" method="post" action="{{ route('admin.watchlists.store') }}">
            @csrf
            <div class="modal-header">
                <div>
                    <h2 class="modal-title" id="watchlistCreateModalLabel">Thêm quy tắc cảnh báo</h2>
                    <p class="mb-0 text-secondary small">Hệ thống sẽ kiểm tra khi khách tạo lịch, walk-in hoặc làm thủ tục vào.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <div class="watchlist-form">
                    <div class="watchlist-soft-note">
                        <i class="bi bi-shield-check"></i>
                        <span>Quy tắc này giúp lễ tân, bảo vệ và an ninh nhận biết khách cần theo dõi trước khi cho vào công ty.</span>
                    </div>

                    <div>
                        <label class="form-label">Gắn với khách có sẵn</label>
                        <select class="form-select" name="visitor_id">
                            <option value="">Không gắn khách cụ thể</option>
                            @foreach ($visitors as $visitor)
                                <option value="{{ $visitor->id }}" @selected((string) old('visitor_id') === (string) $visitor->id)>
                                    {{ $visitor->full_name }} - {{ $visitor->phone ?? $visitor->email ?? 'Chưa có liên hệ' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Từ khóa cần cảnh báo</label>
                        <input class="form-control" name="keyword" value="{{ old('keyword') }}" placeholder="Tên, SĐT, email, công ty, CCCD..." required>
                    </div>

                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label">Kiểu kiểm tra</label>
                            <select class="form-select" name="match_type" required>
                                @foreach ($matchTypes as $value => $label)
                                    <option value="{{ $value }}" @selected(old('match_type', 'any') === $value)>
                                        {{ $matchLabels[$value] ?? $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mức độ</label>
                            <select class="form-select" name="level" required>
                                @foreach ($levels as $value => $label)
                                    <option value="{{ $value }}" @selected(old('level', 'warning') === $value)>
                                        {{ $levelLabels[$value] ?? $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Trạng thái</label>
                        <select class="form-select" name="status" required>
                            <option value="active" @selected(old('status', 'active') === 'active')>Đang áp dụng</option>
                            <option value="inactive" @selected(old('status') === 'inactive')>Tạm tắt</option>
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Lý do</label>
                        <input class="form-control" name="reason" value="{{ old('reason') }}" placeholder="Vì sao cần đưa vào danh sách cảnh báo" required>
                    </div>

                    <div>
                        <label class="form-label">Ghi chú nội bộ</label>
                        <textarea class="form-control" name="note" rows="3" placeholder="Ghi chú chỉ dành cho admin/an ninh">{{ old('note') }}</textarea>
                    </div>
                </div>
            </div>
            <div class="watchlist-modal-footer">
                <button class="btn btn-light" type="button" data-bs-dismiss="modal">Hủy</button>
                <button class="btn btn-brand" type="submit">
                    <i class="bi bi-shield-plus me-1"></i>
                    Lưu quy tắc
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
@if ($errors->any())
<script>
    const watchlistModal = new bootstrap.Modal(document.getElementById('watchlistCreateModal'));
    watchlistModal.show();
</script>
@endif
@endpush

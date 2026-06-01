@extends('layouts.admin')

@section('title', 'Sua user | Visitor Management')
@section('page_title', 'Sua user '.$targetUser->name)
@section('page_subtitle', 'Cap nhat thong tin dang nhap, role va trang thai')

@section('content')
    <form class="row g-3" method="post" action="{{ route('admin.rbac.users.update', $targetUser) }}">
        @csrf
        @method('PUT')
        <div class="col-xl-8">
            <section class="panel-card">
                <div class="panel-header">
                    <div>
                        <h3>Thong tin user</h3>
                        <p>De trong mat khau neu khong muon thay doi.</p>
                    </div>
                    <span class="status-badge {{ $targetUser->is_active ? 'status-approved' : 'status-checked-out' }}">
                        {{ $targetUser->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Ho ten</label>
                        <input class="form-control" name="name" value="{{ old('name', $targetUser->name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input class="form-control" type="email" name="email" value="{{ old('email', $targetUser->email) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Mat khau moi</label>
                        <input class="form-control" type="password" name="password" placeholder="De trong neu khong doi">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Role</label>
                        <select class="form-select" name="role_id" required>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" @selected((string) old('role_id', $targetUser->roles->first()?->id) === (string) $role->id)>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <input type="hidden" name="is_active" value="0">
                        <label class="form-check d-flex align-items-center gap-2">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" @checked(old('is_active', $targetUser->is_active))>
                            <span class="form-check-label">Cho phep dang nhap</span>
                        </label>
                    </div>
                </div>
            </section>
        </div>

        <div class="col-xl-4">
            <section class="panel-card sticky-xl-top top-space">
                <div class="d-grid gap-2">
                    <button class="btn btn-brand btn-lg" type="submit">Luu thay doi</button>
                    <a class="btn btn-light" href="{{ route('admin.rbac.users.show', $targetUser) }}">Quay lai chi tiet</a>
                </div>
            </section>
        </div>
    </form>
@endsection

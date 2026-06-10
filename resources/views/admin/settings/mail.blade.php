@extends('layouts.admin')

@section('title', 'Cấu hình Gmail')
@section('page_title', 'Cấu hình Gmail')
@section('page_subtitle', 'Thiết lập tài khoản gửi mã QR và thông báo email')

@push('styles')
<style>
.mail-settings{width:min(820px,100%);margin:0 auto;display:grid;gap:1rem}.mail-head{display:flex;align-items:center;justify-content:space-between;gap:1rem}.mail-panel{padding:1.1rem;border:1px solid #e1ebf6;border-radius:8px;background:#fff;box-shadow:0 10px 26px rgba(17,39,68,.04)}.mail-panel-head{display:flex;align-items:center;gap:.75rem;margin-bottom:1rem}.mail-panel-icon{width:42px;height:42px;display:grid;place-items:center;border-radius:8px;color:#146bd7;background:#eaf4ff;font-size:1.15rem}.mail-panel h2{margin:0;color:#10233d;font-size:.95rem;font-weight:600}.mail-panel p{margin:.18rem 0 0;color:#7187a3;font-size:.75rem;line-height:1.45}.mail-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.9rem}.mail-field{display:grid;gap:.4rem}.mail-field.full{grid-column:1/-1}.mail-field label{color:#344d69;font-size:.76rem;font-weight:600}.mail-input{width:100%;min-height:42px;padding:.62rem .75rem;border:1px solid #d8e5f2;border-radius:8px;background:#fbfdff;color:#18314f;font-size:.82rem}.mail-input:focus{border-color:#68a6ec;outline:0;box-shadow:0 0 0 3px rgba(20,107,215,.1)}.mail-password{display:grid;grid-template-columns:minmax(0,1fr) 42px}.mail-password .mail-input{border-radius:8px 0 0 8px}.mail-password button{border:1px solid #d8e5f2;border-left:0;border-radius:0 8px 8px 0;background:#f7faff;color:#526b87}.mail-note{display:flex;align-items:flex-start;gap:.5rem;padding:.72rem .8rem;border:1px solid #d9e8f7;border-radius:8px;background:#f6faff;color:#526b87;font-size:.73rem;line-height:1.5}.mail-check{display:flex;align-items:center;gap:.48rem;color:#526b87;font-size:.76rem}.mail-actions{display:flex;justify-content:flex-end;margin-top:1rem}.mail-actions .btn{min-width:180px}.mail-test{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:.65rem}.mail-status{display:inline-flex;align-items:center;gap:.4rem;color:#16845f;font-size:.73rem;font-weight:600}@media(max-width:680px){.mail-head{align-items:flex-start;flex-direction:column}.mail-grid{grid-template-columns:1fr}.mail-field.full{grid-column:auto}.mail-test{grid-template-columns:1fr}.mail-actions .btn,.mail-test .btn{width:100%}}
</style>
@endpush

@section('content')
<div class="mail-settings">
    <div class="mail-head">
        <a class="btn btn-light" href="{{ route('admin.settings.index') }}"><i class="bi bi-grid me-1"></i>Tất cả cài đặt</a>
        @if ($passwordConfigured)
            <span class="mail-status"><i class="bi bi-check-circle-fill"></i>Đã lưu App Password</span>
        @endif
    </div>

    <form class="mail-panel" method="post" action="{{ route('admin.settings.mail.update') }}">
        @csrf
        @method('put')
        <div class="mail-panel-head">
            <span class="mail-panel-icon"><i class="bi bi-envelope-at"></i></span>
            <div><h2>Tài khoản gửi email</h2><p>Tên và địa chỉ này sẽ xuất hiện trong email mã QR gửi cho khách.</p></div>
        </div>
        <div class="mail-grid">
            <div class="mail-field">
                <label for="from_name">Tên người gửi</label>
                <input class="mail-input" id="from_name" name="from_name" value="{{ old('from_name', $settings['mail.from_name']) }}" maxlength="120" required placeholder="Ví dụ: Công ty ABC">
                @error('from_name')<small class="text-danger">{{ $message }}</small>@enderror
            </div>
            <div class="mail-field">
                <label for="from_address">Địa chỉ Gmail gửi QR</label>
                <input class="mail-input" id="from_address" name="from_address" type="email" value="{{ old('from_address', $settings['mail.from_address']) }}" maxlength="190" required placeholder="tencongty@gmail.com">
                @error('from_address')<small class="text-danger">{{ $message }}</small>@enderror
            </div>
            <div class="mail-field full">
                <label for="app_password">Gmail App Password</label>
                <div class="mail-password">
                    <input class="mail-input" id="app_password" name="app_password" type="password" autocomplete="new-password" placeholder="{{ $passwordConfigured ? 'Để trống để giữ mật khẩu đang dùng' : 'Nhập App Password 16 ký tự' }}">
                    <button type="button" data-toggle-password title="Hiện hoặc ẩn mật khẩu"><i class="bi bi-eye"></i></button>
                </div>
                @error('app_password')<small class="text-danger">{{ $message }}</small>@enderror
            </div>
        </div>
        <div class="mail-note mt-3">
            <i class="bi bi-info-circle"></i>
            <span>Dùng App Password của Google, không dùng mật khẩu đăng nhập Gmail. Tài khoản Google cần bật xác minh 2 bước trước khi tạo App Password.</span>
        </div>
        @if ($passwordConfigured)
            <label class="mail-check mt-3"><input type="checkbox" name="remove_password" value="1">Xóa App Password đang lưu</label>
        @endif
        <div class="mail-actions">
            <button class="btn btn-brand" type="submit"><i class="bi bi-save2 me-1"></i>Lưu cấu hình Gmail</button>
        </div>
    </form>

    <form class="mail-panel" method="post" action="{{ route('admin.settings.mail.test') }}">
        @csrf
        <div class="mail-panel-head">
            <span class="mail-panel-icon"><i class="bi bi-send-check"></i></span>
            <div><h2>Gửi email thử</h2><p>Lưu cấu hình phía trên trước, sau đó gửi thử đến một địa chỉ email.</p></div>
        </div>
        <div class="mail-test">
            <input class="mail-input" name="test_email" type="email" value="{{ old('test_email', $settings['mail.from_address']) }}" required placeholder="Email nhận thử">
            <button class="btn btn-outline-primary" type="submit"><i class="bi bi-send me-1"></i>Gửi thử</button>
        </div>
        @error('test_email')<small class="text-danger d-block mt-2">{{ $message }}</small>@enderror
    </form>
</div>
@endsection

@push('scripts')
<script>
document.querySelector('[data-toggle-password]')?.addEventListener('click', (event) => {
    const input = document.getElementById('app_password');
    const icon = event.currentTarget.querySelector('i');
    const show = input.type === 'password';
    input.type = show ? 'text' : 'password';
    icon.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
});
</script>
@endpush

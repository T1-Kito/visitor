@extends('layouts.admin')

@php
    $currentScheme = match ($settings['mail.scheme'] ?? 'tls') {
        null, '' => 'none',
        'smtps', 'ssl' => 'ssl',
        default => 'tls',
    };
@endphp

@section('title', 'Cấu hình email')
@section('page_title', 'Cấu hình email')
@section('page_subtitle', 'Thiết lập SMTP, người gửi và trigger email cho hệ thống')

@push('styles')
<style>
.mail-settings{width:min(1040px,100%);margin:0 auto;display:grid;gap:1rem}.mail-head{display:flex;align-items:center;justify-content:space-between;gap:1rem}.mail-status{display:inline-flex;align-items:center;gap:.4rem;padding:.45rem .7rem;border:1px solid #bbf7d0;border-radius:999px;background:#f0fdf4;color:#15803d;font-size:.76rem;font-weight:500}.mail-panel{border:1px solid #e1ebf6;border-radius:10px;background:#fff;box-shadow:0 10px 26px rgba(17,39,68,.04);overflow:hidden}.mail-panel-head{display:flex;align-items:center;gap:.75rem;padding:1rem 1.1rem;border-bottom:1px solid #edf3fb}.mail-panel-icon{width:38px;height:38px;display:grid;place-items:center;border-radius:9px;color:#146bd7;background:#eaf4ff;font-size:1rem}.mail-panel h2{margin:0;color:#10233d;font-size:.95rem;font-weight:600}.mail-panel p{margin:.18rem 0 0;color:#7187a3;font-size:.74rem;line-height:1.45}.mail-body{padding:1.1rem}.mail-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.9rem}.mail-grid.three{grid-template-columns:1.4fr .7fr .9fr}.mail-field{display:grid;gap:.38rem}.mail-field.full{grid-column:1/-1}.mail-field label{color:#344d69;font-size:.76rem;font-weight:600}.mail-input{width:100%;min-height:42px;padding:.62rem .75rem;border:1px solid #d8e5f2;border-radius:8px;background:#fbfdff;color:#18314f;font-size:.82rem}.mail-input:focus{border-color:#68a6ec;outline:0;box-shadow:0 0 0 3px rgba(20,107,215,.1)}.mail-password{display:grid;grid-template-columns:minmax(0,1fr) 42px}.mail-password .mail-input{border-radius:8px 0 0 8px}.mail-password button{border:1px solid #d8e5f2;border-left:0;border-radius:0 8px 8px 0;background:#f7faff;color:#526b87}.mail-note{display:flex;align-items:flex-start;gap:.5rem;padding:.72rem .8rem;border:1px solid #d9e8f7;border-radius:8px;background:#f6faff;color:#526b87;font-size:.73rem;line-height:1.5}.mail-switches{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.75rem}.mail-switch{display:flex;align-items:flex-start;gap:.65rem;padding:.8rem;border:1px solid #e3edf8;border-radius:9px;background:#fbfdff}.mail-switch input{margin-top:.18rem}.mail-switch strong{display:block;color:#10233d;font-size:.8rem;font-weight:600}.mail-switch span{display:block;margin-top:.14rem;color:#7187a3;font-size:.72rem;line-height:1.45}.mail-actions{display:flex;justify-content:flex-end;gap:.65rem;padding:1rem 1.1rem;border-top:1px solid #edf3fb;background:#fbfdff}.mail-test{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:.65rem}.mail-danger{color:#dc2626;font-size:.73rem}.mail-muted{color:#7187a3;font-size:.72rem}.mail-radio{display:flex;gap:.65rem;flex-wrap:wrap}.mail-radio label{display:inline-flex;align-items:center;gap:.4rem;min-height:42px;padding:.55rem .7rem;border:1px solid #d8e5f2;border-radius:8px;background:#fbfdff;color:#344d69;font-size:.78rem;font-weight:500}@media(max-width:780px){.mail-head{align-items:flex-start;flex-direction:column}.mail-grid,.mail-grid.three,.mail-switches,.mail-test{grid-template-columns:1fr}.mail-actions{justify-content:stretch;flex-direction:column}.mail-actions .btn,.mail-test .btn{width:100%}}
</style>
@endpush

@section('content')
<div class="mail-settings">
    <div class="mail-head">
        <a class="btn btn-light" href="{{ route('admin.settings.index') }}"><i class="bi bi-grid me-1"></i>Tất cả cài đặt</a>
        @if ($passwordConfigured)
            <span class="mail-status"><i class="bi bi-shield-check"></i>Đã lưu mật khẩu SMTP</span>
        @endif
    </div>

    <form class="mail-panel" method="post" action="{{ route('admin.settings.mail.update') }}">
        @csrf
        @method('put')

        <div class="mail-panel-head">
            <span class="mail-panel-icon"><i class="bi bi-hdd-network"></i></span>
            <div>
                <h2>Máy chủ SMTP</h2>
                <p>Hỗ trợ Gmail, Microsoft 365 hoặc máy chủ SMTP riêng của doanh nghiệp.</p>
            </div>
        </div>
        <div class="mail-body">
            <div class="mail-grid three">
                <div class="mail-field">
                    <label for="host">SMTP host</label>
                    <input class="mail-input" id="host" name="host" value="{{ old('host', $settings['mail.host']) }}" required placeholder="smtp.company.com">
                    @error('host')<small class="mail-danger">{{ $message }}</small>@enderror
                </div>
                <div class="mail-field">
                    <label for="port">Port</label>
                    <input class="mail-input" id="port" name="port" type="number" min="1" max="65535" value="{{ old('port', $settings['mail.port']) }}" required>
                    @error('port')<small class="mail-danger">{{ $message }}</small>@enderror
                </div>
                <div class="mail-field">
                    <label for="scheme">Bảo mật</label>
                    <select class="mail-input" id="scheme" name="scheme" required>
                        <option value="tls" @selected(old('scheme', $currentScheme) === 'tls')>TLS / STARTTLS</option>
                        <option value="ssl" @selected(old('scheme', $currentScheme) === 'ssl')>SSL / SMTPS</option>
                        <option value="none" @selected(old('scheme', $currentScheme) === 'none')>Không mã hóa</option>
                    </select>
                    @error('scheme')<small class="mail-danger">{{ $message }}</small>@enderror
                </div>
            </div>
            <input type="hidden" name="auth_mode" value="login">
            <div class="mail-grid mt-3">
                <div class="mail-field full">
                    <label for="timeout">Timeout gửi mail</label>
                    <input class="mail-input" id="timeout" name="timeout" type="number" min="5" max="120" value="{{ old('timeout', $settings['mail.timeout'] ?? 30) }}" required>
                    <small class="mail-muted">Đơn vị giây. Nên để 20-60 giây với mạng doanh nghiệp.</small>
                    @error('timeout')<small class="mail-danger">{{ $message }}</small>@enderror
                </div>
            </div>
        </div>

        <div class="mail-panel-head">
            <span class="mail-panel-icon"><i class="bi bi-person-badge"></i></span>
            <div>
                <h2>Người gửi và tài khoản</h2>
                <p>Mật khẩu được mã hóa trong hệ thống và không hiển thị lại trên giao diện.</p>
            </div>
        </div>
        <div class="mail-body">
            <div class="mail-grid">
                <div class="mail-field">
                    <label for="from_name">Tên người gửi</label>
                    <input class="mail-input" id="from_name" name="from_name" value="{{ old('from_name', $settings['mail.from_name']) }}" maxlength="120" required placeholder="DHL Visitor Management">
                    @error('from_name')<small class="mail-danger">{{ $message }}</small>@enderror
                </div>
                <div class="mail-field">
                    <label for="from_address">Email người gửi</label>
                    <input class="mail-input" id="from_address" name="from_address" type="email" value="{{ old('from_address', $settings['mail.from_address']) }}" maxlength="190" required placeholder="vms@company.com">
                    @error('from_address')<small class="mail-danger">{{ $message }}</small>@enderror
                </div>
                <div class="mail-field">
                    <label for="username">SMTP username</label>
                    <input class="mail-input" id="username" name="username" value="{{ old('username', $settings['mail.username']) }}" maxlength="190" placeholder="Để trống nếu giống email người gửi">
                    @error('username')<small class="mail-danger">{{ $message }}</small>@enderror
                </div>
                <div class="mail-field">
                    <label for="smtp_password">SMTP password / App password</label>
                    <div class="mail-password">
                        <input class="mail-input" id="smtp_password" name="smtp_password" type="password" autocomplete="new-password" placeholder="{{ $passwordConfigured ? 'Để trống để giữ mật khẩu đang dùng' : 'Nhập mật khẩu SMTP' }}">
                        <button type="button" data-toggle-password title="Hiện hoặc ẩn mật khẩu"><i class="bi bi-eye"></i></button>
                    </div>
                    @error('smtp_password')<small class="mail-danger">{{ $message }}</small>@enderror
                    @error('app_password')<small class="mail-danger">{{ $message }}</small>@enderror
                </div>
                <div class="mail-field">
                    <label for="reply_to">Reply-To</label>
                    <input class="mail-input" id="reply_to" name="reply_to" type="email" value="{{ old('reply_to', $settings['mail.reply_to']) }}" maxlength="190" placeholder="reception@company.com">
                    @error('reply_to')<small class="mail-danger">{{ $message }}</small>@enderror
                </div>
                <div class="mail-field">
                    <label for="local_domain">EHLO / local domain</label>
                    <input class="mail-input" id="local_domain" name="local_domain" value="{{ old('local_domain', $settings['mail.local_domain']) }}" maxlength="190" placeholder="visitor.company.com">
                    @error('local_domain')<small class="mail-danger">{{ $message }}</small>@enderror
                </div>
            </div>

            <div class="mail-note mt-3">
                <i class="bi bi-info-circle"></i>
                <span>Nếu dùng Gmail hoặc Microsoft 365, nên sử dụng App Password hay tài khoản SMTP chuyên biệt do bộ phận IT cấp.</span>
            </div>
            @if ($passwordConfigured)
                <label class="mail-switch mt-3">
                    <input type="checkbox" name="remove_password" value="1">
                    <span><strong>Xóa mật khẩu SMTP đang lưu</strong><span>Chỉ bật khi muốn xóa credential khỏi hệ thống.</span></span>
                </label>
            @endif
        </div>

        <div class="mail-panel-head">
            <span class="mail-panel-icon"><i class="bi bi-lightning-charge"></i></span>
            <div>
                <h2>Trigger gửi email</h2>
                <p>Bật/tắt từng luồng gửi email để phù hợp chính sách vận hành của khách hàng.</p>
            </div>
        </div>
        <div class="mail-body">
            <div class="mail-switches">
                <label class="mail-switch">
                    <input type="checkbox" name="trigger_qr_approved" value="1" @checked(old('trigger_qr_approved', $settings['mail.trigger_qr_approved'] ?? '1') === '1')>
                    <span><strong>Gửi QR khi duyệt lịch</strong><span>Sau khi lịch được duyệt, hệ thống gửi mã QR/check-in cho khách.</span></span>
                </label>
                <label class="mail-switch">
                    <input type="checkbox" name="trigger_host_checkin" value="1" @checked(old('trigger_host_checkin', $settings['mail.trigger_host_checkin'] ?? '1') === '1')>
                    <span><strong>Báo người tiếp khi khách vào</strong><span>Khi lễ tân/bảo vệ xác nhận khách vào, hệ thống gửi email cho người tiếp.</span></span>
                </label>
            </div>
        </div>

        <div class="mail-actions">
            <button class="btn btn-brand" type="submit" data-disable-on-submit data-loading-text="Đang lưu..."><i class="bi bi-save2 me-1"></i>Lưu cấu hình email</button>
        </div>
    </form>

    <form class="mail-panel" method="post" action="{{ route('admin.settings.mail.test') }}">
        @csrf
        <div class="mail-panel-head">
            <span class="mail-panel-icon"><i class="bi bi-send-check"></i></span>
            <div>
                <h2>Gửi email thử</h2>
                <p>Lưu cấu hình phía trên trước, sau đó gửi thử để kiểm tra kết nối SMTP thực tế.</p>
            </div>
        </div>
        <div class="mail-body">
            <div class="mail-test">
                <input class="mail-input" name="test_email" type="email" value="{{ old('test_email', $settings['mail.from_address']) }}" required placeholder="Email nhận thử">
                <button class="btn btn-outline-primary" type="submit" data-disable-on-submit data-loading-text="Đang gửi..."><i class="bi bi-send me-1"></i>Gửi thử</button>
            </div>
            @error('test_email')<small class="mail-danger d-block mt-2">{{ $message }}</small>@enderror
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.querySelector('[data-toggle-password]')?.addEventListener('click', (event) => {
    const input = document.getElementById('smtp_password');
    const icon = event.currentTarget.querySelector('i');
    const show = input.type === 'password';
    input.type = show ? 'text' : 'password';
    icon.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
});
</script>
@endpush

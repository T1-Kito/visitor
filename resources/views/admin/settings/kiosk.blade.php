@extends('layouts.admin')

@section('title', 'Cai dat kiosk')
@section('page_title', 'Cai dat kiosk')
@section('page_subtitle', 'Quan ly noi dung hien thi tren man hinh tiep don khach')

@section('content')
    @php
        $value = static fn (string $key, ?string $fallback = '') => old(str_replace('kiosk.', '', $key), $settings[$key] ?? $fallback);
    @endphp

    <div class="gate-page-head">
        <div>
            <span class="gate-eyebrow">System settings</span>
            <h1>Kiosk Visitor Interface</h1>
            <p>Cap nhat logo, noi dung, hotline, mau chu dao va anh nen cho man hinh kiosk public.</p>
        </div>
        <div class="gate-page-actions">
            <a class="btn btn-light" href="{{ route('kiosk.index') }}" target="_blank">
                <i class="bi bi-box-arrow-up-right"></i>
                Xem kiosk
            </a>
        </div>
    </div>

    <form method="post" action="{{ route('admin.settings.kiosk.update') }}" enctype="multipart/form-data">
        @csrf
        @method('put')

        <div class="row g-4">
            <div class="col-xl-8">
                <div class="gate-card mb-4">
                    <div class="gate-card-head">
                        <div>
                            <h2>Thong tin hien thi</h2>
                            <p>Noi dung nam trong panel VMS ben trai cua kiosk.</p>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Ten cong ty <span class="text-danger">*</span></label>
                            <input class="form-control" name="company_name" value="{{ $value('kiosk.company_name', 'Cong ty ABC') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ten he thong kiosk <span class="text-danger">*</span></label>
                            <input class="form-control" name="system_name" value="{{ $value('kiosk.system_name', 'VMS Kiosk') }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Subtitle kiosk <span class="text-danger">*</span></label>
                            <input class="form-control" name="subtitle" value="{{ $value('kiosk.subtitle', 'Giao dien tu dong cho khach den cong ty') }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Cau chao mung</label>
                            <input class="form-control" name="welcome_title" value="{{ $value('kiosk.welcome_title') }}" placeholder="De trong de tu dong: Chao mung ban den [Ten cong ty]">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Mo ta ngan <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="welcome_description" rows="3" required>{{ $value('kiosk.welcome_description', 'Vui long dang ky thong tin hoac check-in bang QR de duoc ho tro nhanh chong.') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="gate-card">
                    <div class="gate-card-head">
                        <div>
                            <h2>Ho tro va nhan dien</h2>
                            <p>Duong dan anh nen/logo co the dung URL tu storage hoac CDN noi bo.</p>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Hotline le tan / bao ve <span class="text-danger">*</span></label>
                            <input class="form-control" name="hotline" value="{{ $value('kiosk.hotline', '1900 0000') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Gio lam viec <span class="text-danger">*</span></label>
                            <input class="form-control" name="working_hours" value="{{ $value('kiosk.working_hours', '07:30 - 18:00') }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Upload logo cong ty</label>
                            @if ($value('kiosk.logo_url'))
                                <div class="mb-2 d-flex align-items-center gap-2">
                                    <img src="{{ $value('kiosk.logo_url') }}" alt="Logo hien tai" style="height:40px;max-width:120px;object-fit:contain;border:1px solid #dee2e6;border-radius:6px;padding:4px;">
                                    <span class="text-muted small">Logo hien tai</span>
                                </div>
                            @endif
                            <input class="form-control" type="file" name="logo_file" accept="image/png,image/jpeg,image/webp,image/svg+xml">
                            <div class="form-text">Ho tro JPG, PNG, WEBP, SVG. De trong neu muon giu logo cu.</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Upload anh nen kiosk</label>
                            @if ($value('kiosk.background_url'))
                                <div class="mb-2 d-flex align-items-center gap-2">
                                    <img src="{{ $value('kiosk.background_url') }}" alt="Anh nen hien tai" style="height:40px;width:80px;object-fit:cover;border:1px solid #dee2e6;border-radius:6px;">
                                    <span class="text-muted small">Anh nen hien tai</span>
                                </div>
                            @endif
                            <input class="form-control" type="file" name="background_file" accept="image/png,image/jpeg,image/webp">
                            <div class="form-text">Ho tro JPG, PNG, WEBP. De trong neu muon giu anh nen cu.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Mau chu dao <span class="text-danger">*</span></label>
                            <div class="d-flex gap-2">
                                <input class="form-control form-control-color" type="color" name="primary_color" value="{{ $value('kiosk.primary_color', '#146bd7') }}" title="Chon mau chu dao">
                                <input class="form-control" value="{{ $value('kiosk.primary_color', '#146bd7') }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="gate-card sticky-xl-top" style="top: 4.5rem;">
                    <div class="gate-card-head">
                        <div>
                            <h2>Preview noi dung</h2>
                            <p>Day la thong tin kiosk se doc sau khi luu.</p>
                        </div>
                    </div>

                    <div class="kiosk-settings-preview">
                        <span class="status-badge status-checked-in">Public kiosk</span>
                        @if ($value('kiosk.logo_url'))
                            <img class="kiosk-settings-preview-logo" src="{{ $value('kiosk.logo_url') }}" alt="Logo kiosk">
                        @endif
                        <h3>{{ $value('kiosk.system_name', 'VMS Kiosk') }}</h3>
                        <p>{{ $value('kiosk.subtitle', 'Giao dien tu dong cho khach den cong ty') }}</p>
                        <div class="kiosk-status-lines mt-3">
                            <div><span>Cong ty</span><strong>{{ $value('kiosk.company_name', 'Cong ty ABC') }}</strong></div>
                            <div><span>Hotline</span><strong>{{ $value('kiosk.hotline', '1900 0000') }}</strong></div>
                            <div><span>Gio</span><strong>{{ $value('kiosk.working_hours', '07:30 - 18:00') }}</strong></div>
                        </div>
                    </div>

                    <button class="btn btn-brand btn-lg w-100 mt-4" type="submit">
                        <i class="bi bi-save2"></i>
                        Luu cau hinh kiosk
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection

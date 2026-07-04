@extends('layouts.admin')

@section('title', $visit->code.' | '.html_entity_decode('Chi ti&#7871;t l&#7883;ch h&#7865;n', ENT_QUOTES, 'UTF-8'))
@section('page_title', html_entity_decode('Chi ti&#7871;t l&#7883;ch h&#7865;n', ENT_QUOTES, 'UTF-8'))
@section('page_subtitle', html_entity_decode('Theo d&otilde;i h&#7891; s&#417; kh&aacute;ch v&agrave; ti&#7871;n tr&igrave;nh x&#7917; l&yacute;', ENT_QUOTES, 'UTF-8'))
@push('styles')
<style>
.visit-app{display:grid;gap:1rem;color:#10233d}.va-back{display:inline-flex;align-items:center;gap:.35rem;width:max-content;color:#7187a3;text-decoration:none;font-size:.78rem;font-weight:500}.va-back:hover{color:#146bd7}.va-profile-card{display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;padding:1rem 1.1rem;border:1px solid #e2edf8;border-radius:18px;background:#fff;box-shadow:0 12px 30px rgba(17,39,68,.055)}.va-profile-main{min-width:0}.va-profile-title{display:flex;align-items:center;gap:.65rem;flex-wrap:wrap}.va-profile-title h1{margin:0;color:#10233d;font-size:1.16rem;font-weight:600;line-height:1.2}.va-company-line{display:flex;align-items:center;gap:.45rem;margin-top:.45rem;color:#253a54;font-size:.84rem;font-weight:500}.va-company-line i{color:#d40511}.va-profile-tags{display:flex;align-items:center;gap:.45rem;flex-wrap:wrap;margin-top:.65rem}.va-chip{display:inline-flex;align-items:center;gap:.35rem;min-height:30px;padding:.3rem .58rem;border:1px solid #e2edf8;border-radius:9px;background:#fbfdff;color:#10233d;font-size:.72rem;font-weight:500}.va-chip i{color:#526b87}.va-created{display:flex;align-items:center;gap:.35rem;color:#7187a3;font-size:.75rem;white-space:nowrap}.va-created i{color:#526b87}.va-kpi-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:.75rem}.va-kpi{display:flex;align-items:center;gap:.85rem;padding:.85rem;border:1px solid #e8f0f8;border-radius:17px;background:#fff;box-shadow:0 10px 22px rgba(17,39,68,.04)}.va-kpi-icon{width:42px;height:42px;display:grid;place-items:center;border-radius:13px;font-size:1.05rem}.va-kpi:nth-child(1) .va-kpi-icon{background:#eaf4ff;color:#1976d2}.va-kpi:nth-child(2) .va-kpi-icon{background:#f3ecff;color:#7c3aed}.va-kpi:nth-child(3) .va-kpi-icon{background:#fff0f0;color:#d40511}.va-kpi:nth-child(4) .va-kpi-icon{background:#eafaf0;color:#16a34a}.va-kpi span{display:block;color:#526b87;font-size:.75rem}.va-kpi strong{display:block;margin-top:.06rem;color:#10233d;font-size:1.28rem;font-weight:600;line-height:1}.va-kpi small{display:block;margin-top:.2rem;color:#8aa0ba;font-size:.66rem}.va-detail-grid{display:grid;grid-template-columns:minmax(0,1fr) 310px;gap:.9rem;align-items:start}.va-main{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.9rem;min-width:0}.va-side{display:grid;gap:.9rem;position:sticky;top:.9rem}.va-panel{border:1px solid #e2edf8;border-radius:18px;background:#fff;box-shadow:0 12px 28px rgba(17,39,68,.04);overflow:hidden;min-width:0}.va-wide{grid-column:1/-1}.va-panel-head{display:flex;align-items:center;justify-content:space-between;gap:.8rem;padding:.85rem 1rem;border-bottom:1px solid #eef4fb}.va-panel-head h2{display:flex;align-items:center;gap:.42rem;margin:0;color:#10233d;font-size:.95rem;font-weight:600}.va-panel-head h2 i{color:#d40511}.va-panel-head p{margin:.16rem 0 0;color:#7187a3;font-size:.74rem}.va-body{padding:1rem}.va-info-list{display:grid}.va-row{display:grid;grid-template-columns:24px minmax(110px,.55fr) minmax(0,1fr);gap:.55rem;align-items:center;padding:.55rem 0;border-bottom:1px solid #eef4fb}.va-row:last-child{border-bottom:0}.va-row i{width:24px;height:24px;display:grid;place-items:center;border-radius:7px;background:#f3f8ff;color:#526b87;font-size:.8rem}.va-label{color:#7187a3;font-size:.74rem}.va-value{color:#10233d;font-size:.78rem;font-weight:500;overflow-wrap:anywhere}.va-btn{min-height:40px;display:inline-flex;align-items:center;justify-content:center;gap:.42rem;padding:.55rem .85rem;border:1px solid #dbe7f4;border-radius:13px;background:#fff;color:#2c4967;font-size:.82rem;font-weight:500;text-decoration:none}.va-btn.primary{border:0;color:#fff;background:linear-gradient(135deg,#1976d2,#11a9c7)}.va-btn.success{border:0;color:#fff;background:#16a34a}.va-btn.danger{border-color:#fecaca;background:#fff7f7;color:#dc2626}.va-btn.soft{background:#f8fbff}.va-btn:disabled,.va-btn[aria-disabled=true]{opacity:.55;pointer-events:none}.va-action-stack,.va-share-actions{display:grid;gap:.5rem}.va-share-actions{grid-template-columns:1fr 1fr}.va-share-actions form{margin:0}.va-qr-card{text-align:center}.va-qr-visual{display:grid;place-items:center;width:min(210px,100%);margin:0 auto;padding:.75rem;border:1px solid #dbe7f4;border-radius:18px;background:#fff}.va-qr-visual svg{width:100%;height:auto}.va-empty-qr{display:grid;place-items:center;min-height:188px;border:1px dashed #bdd6ee;border-radius:18px;background:#f8fbff;color:#7187a3;font-size:.84rem}.va-token{display:flex;align-items:center;justify-content:space-between;gap:.5rem;margin-top:.7rem;padding:.58rem .7rem;border-radius:13px;background:#edf5ff;color:#1976d2;font-size:.76rem;font-weight:600;word-break:break-all}.va-mini-grid{display:grid;grid-template-columns:1fr 1fr;gap:.5rem;margin-top:.65rem}.va-mini{padding:.55rem;border:1px solid #edf3fb;border-radius:13px;background:#fbfdff;text-align:left}.va-mini span{display:block;color:#7187a3;font-size:.66rem}.va-mini strong{display:block;margin-top:.08rem;color:#10233d;font-size:.74rem;font-weight:500}.va-flow{display:grid;grid-template-columns:repeat(5,minmax(96px,1fr));gap:.45rem;padding:1rem;overflow-x:auto}.va-step{position:relative;display:grid;justify-items:center;gap:.35rem;text-align:center;color:#7187a3}.va-step:before{content:"";position:absolute;top:15px;left:-50%;right:50%;height:2px;background:#dce8f5}.va-step:first-child:before{display:none}.va-step.done:before{background:#22c55e}.va-dot{width:30px;height:30px;display:grid;place-items:center;border-radius:999px;background:#f3f7fb;color:#8aa0ba;border:1px solid #dce8f5;z-index:1}.va-step.done .va-dot{background:#22c55e;color:#fff;border-color:#22c55e}.va-step strong{color:#253a54;font-size:.72rem;font-weight:600}.va-step span{font-size:.66rem;line-height:1.3}.va-status-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:.65rem}.va-status-card{padding:.8rem;border:1px solid #edf3fb;border-radius:16px;background:#fff}.va-status-card h3{margin:0 0 .5rem;color:#10233d;font-size:.86rem;font-weight:600}.va-status-card div{display:flex;justify-content:space-between;gap:.6rem;color:#7187a3;font-size:.75rem}.va-status-card strong{color:#10233d;font-weight:500;text-align:right}.va-log-table{width:100%;border-collapse:separate;border-spacing:0}.va-log-table th{padding:.7rem;color:#7187a3;font-size:.68rem;font-weight:600;text-align:left;border-bottom:1px solid #eef4fb}.va-log-table td{padding:.72rem;color:#34506c;font-size:.76rem;border-bottom:1px solid #f1f5fa}@media(max-width:1300px){.va-detail-grid{grid-template-columns:1fr}.va-side{position:static;grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:900px){.va-profile-card{flex-direction:column}.va-kpi-grid,.va-main,.va-side,.va-status-grid{grid-template-columns:1fr}.va-row{grid-template-columns:24px 1fr}.va-row .va-value{grid-column:2}.va-flow{grid-template-columns:repeat(5,120px)}}
</style>
@endpush

@section('content')
@php
    $stepByStatus = ['pending' => 1, 'approved' => 2, 'checked_in' => 4, 'checked_out' => 5, 'rejected' => 2, 'cancelled' => 2];
    $currentStep = $stepByStatus[$visit->status] ?? 1;
    $qrIsValid = $visit->qr_token && (! $visit->qr_expires_at || $visit->qr_expires_at->isFuture());
    $statusTextHtml = [
        'pending' => 'Ch&#7901; duy&#7879;t',
        'approved' => '&#272;&atilde; duy&#7879;t',
        'rejected' => 'T&#7915; ch&#7889;i',
        'checked_in' => '&#272;ang trong c&ocirc;ng ty',
        'checked_out' => '&#272;&atilde; r&#7901;i c&ocirc;ng ty',
        'cancelled' => '&#272;&atilde; h&#7911;y',
        'waiting' => 'Y&ecirc;u c&#7847;u ch&#7901;',
    ][$visit->status] ?? e($visit->status);
    $statusText = html_entity_decode(strip_tags($statusTextHtml), ENT_QUOTES, 'UTF-8');
    $methodText = [
        'qr' => 'M&#227; QR',
        'badge' => 'Th&#7867; t&#7841;m',
        'manual' => 'Nh&#7853;p th&#7911; c&ocirc;ng',
    ][$visit->checkin_method] ?? strtoupper((string) $visit->checkin_method);
    $auditActionLabels = array_map(fn ($label) => html_entity_decode($label, ENT_QUOTES, 'UTF-8'), [
        'kiosk.walk_in_created' => 'Kh&aacute;ch &#273;&atilde; &#273;&#259;ng k&yacute; t&#7841;i kiosk',
        'visit.created' => '&#272;&atilde; t&#7841;o l&#7883;ch h&#7865;n',
        'visit.updated' => '&#272;&atilde; c&#7853;p nh&#7853;t l&#7883;ch h&#7865;n',
        'visit.cancelled' => '&#272;&atilde; h&#7911;y l&#7883;ch h&#7865;n',
        'approval.approved' => '&#272;&atilde; duy&#7879;t l&#7883;ch h&#7865;n',
        'approval.approved_and_checked_in' => '&#272;&atilde; duy&#7879;t v&agrave; cho kh&aacute;ch v&agrave;o',
        'approval.rejected' => '&#272;&atilde; t&#7915; ch&#7889;i l&#7883;ch h&#7865;n',
        'approval.wait' => 'Y&ecirc;u c&#7847;u kh&aacute;ch ch&#7901; x&aacute;c nh&#7853;n',
        'visit.checked_in' => '&#272;&atilde; x&aacute;c nh&#7853;n kh&aacute;ch v&agrave;o',
        'visit.checked_out' => '&#272;&atilde; x&aacute;c nh&#7853;n kh&aacute;ch ra',
        'visit.qr_generated' => '&#272;&atilde; t&#7841;o m&atilde; QR',
        'visit.qr_emailed' => '&#272;&atilde; g&#7917;i m&atilde; QR qua email',
        'visit.qr_scanned_for_checkin' => '&#272;&atilde; qu&eacute;t m&atilde; &#273;&#7875; check-in',
        'visit.qr_scanned_for_checkout' => '&#272;&atilde; qu&eacute;t m&atilde; &#273;&#7875; check-out',
        'visit.badge_scanned_for_checkout' => '&#272;&atilde; qu&eacute;t th&#7867; &#273;&#7875; check-out',
        'visit.host_checkin_email_sent' => '&#272;&atilde; g&#7917;i email b&aacute;o ng&#432;&#7901;i ti&#7871;p',
        'visit.host_checkin_email_failed' => 'G&#7917;i email b&aacute;o ng&#432;&#7901;i ti&#7871;p th&#7845;t b&#7841;i',
    ]);    $hideQrWorkflow = $kioskLobbyModeEnabled ?? false;
    $canEditInCurrentMode = $canEdit && (! $hideQrWorkflow || $visit->status === 'pending');
    $canCancelInCurrentMode = $canCancel && (! $hideQrWorkflow || $visit->status === 'pending');
    $printQrSvg = (! $hideQrWorkflow && $visit->qr_token)
        ? (string) \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(220)->margin(1)->errorCorrection('M')->generate($visit->qr_token)
        : null;
    $printTicket = [
        'code' => $visit->code,
        'qrToken' => $visit->qr_token,
        'qrSvg' => $printQrSvg,
        'visitorName' => $visit->visitor?->full_name ?? '-',
        'visitorCompany' => $visit->visitor?->company ?? '-',
        'hostName' => $visit->host_display_name,
        'scheduledAt' => $visit->scheduled_at?->format('d/m/Y H:i') ?? '-',
        'status' => $statusText,
    ];
    $sharePhone = preg_replace('/\D+/', '', (string) ($visit->visitor?->phone ?? ''));
    $zaloPhone = $sharePhone && str_starts_with($sharePhone, '0') ? '84'.substr($sharePhone, 1) : $sharePhone;
    $shareEmail = trim((string) ($visit->visitor?->email ?? ''));
    $shareStatusUrl = route('kiosk.checkin.status', $visit);
    $shareMessage = implode("\n", array_filter([
        'VMS KIOSK gửi thông tin lịch hẹn của bạn:',
        'Mã lịch hẹn: '.$visit->code,
        $visit->qr_token ? 'Mã QR/check-in: '.$visit->qr_token : null,
        'Khách: '.($visit->visitor?->full_name ?? '-'),
        'Công ty: '.($visit->visitor?->company ?? '-'),
        'Người tiếp: '.($visit->host_display_name),
        'Giờ hẹn: '.($visit->scheduled_at?->format('d/m/Y H:i') ?? '-'),
        'Link tra cứu/check-in: '.$shareStatusUrl,
        'Vui lòng xuất trình mã này tại quầy lễ tân.',
    ]));
    $zaloUrl = $zaloPhone ? 'https://zalo.me/'.$zaloPhone : null;
    $shareQrPayload = [
        'message' => $shareMessage,
        'zaloUrl' => $zaloUrl,
    ];
@endphp

<div class="visit-app">
    <a class="va-back" href="{{ route('admin.visits.index') }}"><i class="bi bi-arrow-left"></i>Danh s&aacute;ch l&#7883;ch h&#7865;n</a>

    <section class="va-profile-card">
        <div class="va-profile-main">
            <div class="va-profile-title">
                <h1>{{ $visit->visitor?->full_name ?? 'Kh&aacute;ch' }}</h1>
                <span class="status-badge status-{{ $visit->status === 'checked_in' ? 'info' : ($visit->status === 'rejected' ? 'rejected' : ($visit->status === 'pending' ? 'pending' : 'approved')) }}">{!! $statusTextHtml !!}</span>
            </div>
            <div class="va-company-line"><i class="bi bi-building"></i>{{ $visit->visitor?->company ?: '-' }}</div>
            <div class="va-profile-tags">
                <span class="va-chip"><i class="bi bi-telephone"></i>{{ $visit->visitor?->phone ?: '-' }}</span>
                <span class="va-chip"><i class="bi bi-envelope"></i>{{ $visit->visitor?->email ?: '-' }}</span>
                <span class="va-chip"><i class="bi bi-person-vcard"></i>M&atilde; KH: {{ $visit->visitor?->visitor_id_card_number ?: '-' }}</span>
            </div>
        </div>
        <div class="va-created"><i class="bi bi-calendar2-week"></i>T&#7841;o l&uacute;c {{ $visit->created_at?->format('d/m/Y H:i') }}</div>
    </section>

    <section class="va-kpi-grid">
        <div class="va-kpi"><div class="va-kpi-icon"><i class="bi bi-box-arrow-in-right"></i></div><div><span>L&#432;&#7907;t ra/v&agrave;o</span><strong>{{ $visit->actual_checkin_at ? '01' : '00' }}</strong><small>{!! $visit->actual_checkout_at ? '&#272;&atilde; ho&agrave;n t&#7845;t' : 'Theo l&#7883;ch hi&#7879;n t&#7841;i' !!}</small></div></div>
        <div class="va-kpi"><div class="va-kpi-icon"><i class="bi bi-calendar-check"></i></div><div><span>L&#7883;ch h&#7865;n</span><strong>01</strong><small>{!! $statusTextHtml !!}</small></div></div>
        <div class="va-kpi"><div class="va-kpi-icon"><i class="bi bi-exclamation-triangle"></i></div><div><span>C&#7843;nh b&aacute;o</span><strong>00</strong><small>Ch&#432;a ghi nh&#7853;n c&#7843;nh b&aacute;o</small></div></div>
        <div class="va-kpi"><div class="va-kpi-icon"><i class="bi bi-clock-history"></i></div><div><span>L&#7847;n v&agrave;o g&#7847;n nh&#7845;t</span><strong>{{ ($visit->actual_checkin_at ?? $visit->scheduled_at)?->format('H:i') ?? '-' }}</strong><small>{{ ($visit->actual_checkin_at ?? $visit->scheduled_at)?->format('d/m/Y') ?? '-' }}</small></div></div>
    </section>

    <div class="va-detail-grid">
        <main class="va-main">
            <section class="va-panel">
                <div class="va-panel-head"><h2><i class="bi bi-person"></i>Th&ocirc;ng tin c&aacute; nh&acirc;n</h2></div>
                <div class="va-body va-info-list">
                    <div class="va-row"><i class="bi bi-person"></i><span class="va-label">H&#7885; t&ecirc;n</span><span class="va-value">{{ $visit->visitor?->full_name ?? '-' }}</span></div>
                    <div class="va-row"><i class="bi bi-telephone"></i><span class="va-label">S&#7889; &#273;i&#7879;n tho&#7841;i</span><span class="va-value">{{ $visit->visitor?->phone ?: '-' }}</span></div>
                    <div class="va-row"><i class="bi bi-envelope"></i><span class="va-label">Email</span><span class="va-value">{{ $visit->visitor?->email ?: '-' }}</span></div>
                    <div class="va-row"><i class="bi bi-card-text"></i><span class="va-label">CCCD / H&#7897; chi&#7871;u</span><span class="va-value">{{ $visit->visitor?->identity_no ?: '-' }}</span></div>
                    <div class="va-row"><i class="bi bi-person-vcard"></i><span class="va-label">S&#7889; th&#7867; kh&aacute;ch</span><span class="va-value">{{ $visit->visitor?->visitor_id_card_number ?: '-' }}</span></div>
                    <div class="va-row"><i class="bi bi-chat-left-text"></i><span class="va-label">Ghi ch&uacute;</span><span class="va-value">{{ $visit->notes ?: '-' }}</span></div>
                </div>
            </section>

            <section class="va-panel">
                <div class="va-panel-head"><h2><i class="bi bi-building"></i>Th&ocirc;ng tin c&ocirc;ng ty</h2></div>
                <div class="va-body va-info-list">
                    <div class="va-row"><i class="bi bi-building"></i><span class="va-label">C&ocirc;ng ty</span><span class="va-value">{{ $visit->visitor?->company ?: '-' }}</span></div>
                    <div class="va-row"><i class="bi bi-person-workspace"></i><span class="va-label">Ng&#432;&#7901;i ti&#7871;p</span><span class="va-value">{{ $visit->host_display_name ?: '-' }}</span></div>
                    <div class="va-row"><i class="bi bi-diagram-3"></i><span class="va-label">Ph&ograve;ng ban</span><span class="va-value">{{ $visit->department?->name ?? '-' }}</span></div>
                    <div class="va-row"><i class="bi bi-box-arrow-in-right"></i><span class="va-label">Check-in</span><span class="va-value">{{ $visit->actual_checkin_at?->format('d/m/Y H:i') ?? $visit->scheduled_at?->format('d/m/Y H:i') ?? '-' }}</span></div>
                    <div class="va-row"><i class="bi bi-box-arrow-left"></i><span class="va-label">Check-out</span><span class="va-value">{{ $visit->actual_checkout_at?->format('d/m/Y H:i') ?? $visit->expected_checkout_at?->format('d/m/Y H:i') ?? '-' }}</span></div>
                </div>
            </section>

        </main>

        <aside class="va-side">
            @unless ($hideQrWorkflow)
            <section class="va-panel va-qr-card">
                <div class="va-panel-head">
                    <h2>QR & thao t&aacute;c</h2>
                    @if ($qrIsValid)
                        <span class="status-badge status-approved">Hi&#7879;u l&#7921;c</span>
                    @else
                        <span class="status-badge status-pending">Ch&#432;a c&oacute; QR</span>
                    @endif
                </div>
                <div class="va-body">
                    @if ($visit->qr_token)
                        <div class="va-qr-visual">{!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(210)->margin(1)->errorCorrection('M')->generate($visit->qr_token) !!}</div>
                        <div class="va-token"><span>{{ $visit->qr_token }}</span><i class="bi bi-copy"></i></div>
                    @else
                        <div class="va-empty-qr"><div><i class="bi bi-qr-code d-block fs-1 mb-2"></i>QR s&#7869; &#273;&#432;&#7907;c sinh khi l&#7883;ch s&#7861;n s&agrave;ng.</div></div>
                    @endif
                    <div class="va-mini-grid">
                        <div class="va-mini"><span>Hi&#7879;u l&#7921;c t&#7915;</span><strong>{{ $visit->scheduled_at?->format('d/m/Y H:i') ?? '-' }}</strong></div>
                        <div class="va-mini"><span>H&#7871;t h&#7841;n</span><strong>{{ $visit->qr_expires_at?->format('d/m/Y H:i') ?? '-' }}</strong></div>
                    </div>
                    <div class="va-action-stack mt-3">
                        <button class="va-btn soft" type="button" onclick="printAdminQrTicket()" @disabled(! $visit->qr_token)><i class="bi bi-printer"></i>In QR</button>
                        @if ($canGenerateQr)
                            <form action="{{ route('admin.visits.generate-qr', $visit) }}" method="post">@csrf<button class="va-btn primary w-100" type="submit"><i class="bi bi-qr-code"></i>Sinh l&#7841;i QR</button></form>
                        @else
                            <button class="va-btn soft" type="button" disabled><i class="bi bi-qr-code"></i>Ch&#432;a th&#7875; sinh</button>
                        @endif
                    </div>
                    @if ($visit->qr_token)
                        <div class="va-share-actions mt-2">
                            @if ($shareEmail)
                                <form action="{{ route('admin.visits.send-qr-email', $visit) }}" method="post" data-disable-on-submit>@csrf<button class="va-btn danger w-100" type="submit" data-loading-text="&#272;ang g&#7917;i..."><i class="bi bi-envelope"></i>Gmail</button></form>
                            @else
                                <span class="va-btn danger" aria-disabled="true"><i class="bi bi-envelope"></i>Gmail</span>
                            @endif
                            <button class="va-btn primary" id="adminQrCopyShareBtn" type="button" onclick="copyAdminQrMessage()"><i class="bi bi-clipboard"></i>Sao ch&eacute;p</button>
                        </div>
                    @endif
                </div>
            </section>
            @endunless

            <section class="va-panel">
                <div class="va-panel-head"><h2>X&#7917; l&yacute;</h2></div>
                <div class="va-body va-action-stack">
                    @if ($visit->status === 'pending')
                        @if ($hideQrWorkflow)
                            <form action="{{ route('admin.approvals.approve-checkin', $visit) }}" method="post" data-disable-on-submit>@csrf<button class="va-btn success w-100" type="submit" data-loading-text="&#272;ang x&#7917; l&yacute;..."><i class="bi bi-door-open"></i>Duy&#7879;t & cho kh&aacute;ch v&agrave;o</button></form>
                        @else
                            <form action="{{ route('admin.approvals.approve', $visit) }}" method="post" data-disable-on-submit>@csrf<button class="va-btn success w-100" type="submit" data-loading-text="&#272;ang duy&#7879;t..."><i class="bi bi-check-circle"></i>Duy&#7879;t l&#7883;ch</button></form>
                        @endif
                        <form action="{{ route('admin.approvals.reject', $visit) }}" method="post" data-disable-on-submit>@csrf<input type="hidden" name="reason" value="Tu choi tu trang chi tiet."><button class="va-btn danger w-100" type="submit" data-loading-text="&#272;ang t&#7915; ch&#7889;i..."><i class="bi bi-x-circle"></i>T&#7915; ch&#7889;i</button></form>
                    @elseif ($visit->status === 'approved')
                        <span class="va-btn soft w-100"><i class="bi bi-check-circle"></i>&#272;&atilde; duy&#7879;t</span>
                        @unless ($hideQrWorkflow)
                            <form action="{{ route('admin.approvals.reject', $visit) }}" method="post" data-disable-on-submit>@csrf<input type="hidden" name="reason" value="Tu choi tu trang chi tiet."><button class="va-btn danger w-100" type="submit" data-loading-text="&#272;ang t&#7915; ch&#7889;i..."><i class="bi bi-x-circle"></i>T&#7915; ch&#7889;i</button></form>
                            <form action="{{ route('admin.checkin.confirm', $visit) }}" method="post" data-disable-on-submit>@csrf<button class="va-btn primary w-100" type="submit" data-loading-text="&#272;ang x&#7917; l&yacute;..."><i class="bi bi-box-arrow-in-right"></i>Cho kh&aacute;ch v&agrave;o</button></form>
                        @endunless
                    @elseif ($visit->status === 'checked_in')
                        <form action="{{ route('admin.checkout.confirm', $visit) }}" method="post" data-disable-on-submit>@csrf<button class="va-btn danger w-100" type="submit" data-loading-text="&#272;ang x&#7917; l&yacute;..."><i class="bi bi-box-arrow-left"></i>Cho kh&aacute;ch ra</button></form>
                    @else
                        <span class="va-btn soft w-100">{!! $statusTextHtml !!}</span>
                    @endif
                    @if ($canEditInCurrentMode)
                        <div style="height:1px;background:#eef4fb;margin:.2rem 0;"></div>
                        <button class="va-btn primary w-100" type="button" data-bs-toggle="modal" data-bs-target="#visitEditModal"><i class="bi bi-pencil-square"></i>S&#7917;a l&#7883;ch h&#7865;n</button>
                    @endif
                </div>
            </section>
        </aside>
    </div>
</div>
@if ($canEditInCurrentMode)
<div class="modal fade" id="visitEditModal" tabindex="-1" aria-labelledby="visitEditModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <form class="modal-content" action="{{ route('admin.visits.update', $visit) }}" method="post" data-disable-on-submit>
            @csrf
            @method('PUT')
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="visitEditModalLabel">S&#7917;a l&#7883;ch h&#7865;n {{ $visit->code }}</h5>
                    <div class="text-muted small">C&#7853;p nh&#7853;t nhanh th&#244;ng tin, l&#7883;ch s&#7869; chuy&#7875;n v&#7873; ch&#7901; duy&#7879;t.</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-12"><div class="fw-semibold text-danger small text-uppercase">Th&#244;ng tin kh&#225;ch</div></div>
                    <div class="col-md-6"><label class="form-label">H&#7885; v&#224; t&#234;n *</label><input type="text" name="visitor_name" value="{{ old('visitor_name', $visit->visitor?->full_name) }}" class="form-control" required></div>
                    <div class="col-md-6"><label class="form-label">S&#7889; &#273;i&#7879;n tho&#7841;i</label><input type="text" name="visitor_phone" value="{{ old('visitor_phone', $visit->visitor?->phone) }}" class="form-control"></div>
                    <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="visitor_email" value="{{ old('visitor_email', $visit->visitor?->email) }}" class="form-control"></div>
                    <div class="col-md-6"><label class="form-label">C&#244;ng ty / T&#7893; ch&#7913;c</label><input type="text" name="visitor_company" value="{{ old('visitor_company', $visit->visitor?->company) }}" class="form-control"></div>
                    <div class="col-md-6"><label class="form-label">CCCD / H&#7897; chi&#7871;u</label><input type="text" name="visitor_identity_no" value="{{ old('visitor_identity_no', $visit->visitor?->identity_no) }}" class="form-control"></div>
                    <div class="col-md-6"><label class="form-label">S&#7889; th&#7867; kh&#225;ch</label><input type="text" name="visitor_id_card_number" value="{{ old('visitor_id_card_number', $visit->visitor?->visitor_id_card_number) }}" class="form-control"></div>

                    <div class="col-12 pt-2"><div class="fw-semibold text-danger small text-uppercase">Th&#244;ng tin v&#224;o / ra</div></div>
                    <div class="col-md-4"><label class="form-label">Ng&#224;y v&#224;o *</label><input type="date" name="visit_date" value="{{ old('visit_date', $visit->scheduled_at?->toDateString()) }}" class="form-control" required></div>
                    <div class="col-md-4"><label class="form-label">Gi&#7901; v&#224;o *</label><input type="time" name="visit_time" value="{{ old('visit_time', $visit->scheduled_at?->format('H:i')) }}" class="form-control" required></div>
                    <div class="col-md-4"><label class="form-label">Gi&#7901; ra d&#7921; ki&#7871;n *</label><input type="time" name="expected_checkout_time" value="{{ old('expected_checkout_time', $visit->expected_checkout_at?->format('H:i') ?? $visit->scheduled_at?->copy()->addHours(2)->format('H:i')) }}" class="form-control" required></div>

                    <div class="col-12 pt-2"><div class="fw-semibold text-danger small text-uppercase">Th&#244;ng tin g&#7863;p</div></div>
                    <div class="col-md-6">
                        <label class="form-label">Ng&#432;&#7901;i c&#7847;n g&#7863;p *</label>
                        <input type="text" id="editHostNameInput" name="host_name" value="{{ old('host_name', $visit->host_display_name) }}" class="form-control" list="editHostSuggestions" required>
                        <input id="editHostEmployeeId" type="hidden" name="host_employee_id" value="{{ old('host_employee_id', $visit->host_employee_id) }}">
                        <datalist id="editHostSuggestions">
                            @foreach ($hosts as $host)
                                <option value="{{ $host['name'] }}" data-id="{{ $host['id'] }}">{{ $host['department'] }}</option>
                            @endforeach
                        </datalist>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Ph&#242;ng ban *</label>
                        <select name="department_id" class="form-select" required>
                            <option value="">Ch&#7885;n ph&#242;ng ban</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}" @selected((string) old('department_id', $visit->department_id ?: $visit->hostEmployee?->department_id) === (string) $department->id)>{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <input type="hidden" name="access_zone" value="{{ old('access_zone', $visit->access_zone) }}">

                    <div class="col-12 pt-2"><div class="fw-semibold text-danger small text-uppercase">Th&#244;ng tin chuy&#7871;n th&#259;m</div></div>
                    <div class="col-12"><label class="form-label">M&#7909;c &#273;&#237;ch &#273;&#7871;n *</label><input type="text" name="purpose" value="{{ old('purpose', $visit->purpose) }}" class="form-control" required></div>
                    <div class="col-12"><label class="form-label">Ghi ch&#250; ti&#7871;p &#273;&#243;n</label><textarea name="visitor_note" class="form-control" rows="3">{{ old('visitor_note', $visit->visitor?->note) }}</textarea></div>
                </div>
                <input type="hidden" name="checkin_method" value="{{ old('checkin_method', $visit->checkin_method ?: 'manual') }}">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">&#272;&#243;ng</button>
                <button type="submit" class="btn btn-brand" data-loading-text="&#272;ang l&#432;u..."><i class="bi bi-save"></i> L&#432;u thay &#273;&#7893;i</button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection
@push('scripts')
<script>
(() => {
    const editHostNameInput = document.getElementById('editHostNameInput');
    const editHostEmployeeId = document.getElementById('editHostEmployeeId');
    if (!editHostNameInput || !editHostEmployeeId) return;
    const options = Array.from(document.querySelectorAll('#editHostSuggestions option'));
    const sync = () => {
        const selected = options.find((option) => option.value.trim().toLowerCase() === editHostNameInput.value.trim().toLowerCase());
        editHostEmployeeId.value = selected?.dataset?.id || '';
    };
    editHostNameInput.addEventListener('input', sync);
    editHostNameInput.addEventListener('change', sync);
})();
</script>
@endpush
@unless ($hideQrWorkflow)
@push('scripts')
<script>
    const adminQrTicket = @json($printTicket, JSON_UNESCAPED_UNICODE);
    const adminQrShare = @json($shareQrPayload, JSON_UNESCAPED_UNICODE);

    async function copyAdminQrMessage() {
        if (!adminQrShare.message) {
            return false;
        }

        try {
            await navigator.clipboard.writeText(adminQrShare.message);
        } catch (error) {
            const helper = document.createElement('textarea');
            helper.value = adminQrShare.message;
            helper.setAttribute('readonly', '');
            helper.style.position = 'fixed';
            helper.style.left = '-9999px';
            document.body.appendChild(helper);
            helper.select();
            document.execCommand('copy');
            helper.remove();
        }

        const button = document.getElementById('adminQrCopyShareBtn');
        if (button) {
            const oldText = button.innerHTML;
            button.innerHTML = '<i class="bi bi-check-circle"></i>Đã sao chép';
            window.setTimeout(() => {
                button.innerHTML = oldText;
            }, 1800);
        }

        return true;
    }

    async function printAdminQrTicket() {
        if (!adminQrTicket.qrSvg) {
            return;
        }

        const bridgeUrl = localStorage.getItem('gatehouse_printer_bridge_url') || 'http://127.0.0.1:9191';

        try {
            const response = await fetch(`${bridgeUrl}/print`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                },
                body: JSON.stringify(adminQrTicket),
            });
            const payload = await response.json();

            if (response.ok && payload.ok) {
                return;
            }
        } catch (error) {
            // Nếu Printer Bridge chưa chạy thì dùng hộp thoại in của trình duyệt.
        }

        const printWindow = window.open('', '_blank', 'width=420,height=640');
        if (!printWindow) {
            alert('Trình duyệt đang chặn cửa sổ in. Vui lòng cho phép popup rồi thử lại.');
            return;
        }

        const safe = (value) => String(value ?? '-')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');

        printWindow.document.write(`
            <!doctype html>
            <html lang="vi">
            <head>
                <meta charset="utf-8">
                <title>In QR ${safe(adminQrTicket.code)}</title>
                <style>
                    @page { size: 80mm auto; margin: 2.5mm; }
                    * { box-sizing: border-box; }
                    html, body { width: 80mm; margin: 0; padding: 0; background: #fff; color: #0b1f3a; font-family: Arial, sans-serif; }
                    .ticket { width: 74mm; margin: 0 auto; text-align: center; }
                    .brand { margin-bottom: 1.2mm; font-size: 11px; font-weight: 700; text-transform: uppercase; }
                    h1 { margin: 0 0 1.2mm; font-size: 14px; line-height: 1.15; }
                    .muted { margin-bottom: 1.8mm; color: #64748b; font-size: 9px; line-height: 1.3; }
                    .qr { display: grid; place-items: center; margin: 1mm auto 1.5mm; }
                    .qr svg { width: 64mm; height: 64mm; display: block; }
                    .code { margin: 2mm 0; padding: 1.6mm; border: 1px dashed #94a3b8; border-radius: 2.5mm; font-size: 13px; font-weight: 700; }
                    .row { display: flex; justify-content: space-between; gap: 3mm; margin: 1.35mm 0; font-size: 10px; text-align: left; }
                    .row span { color: #64748b; }
                    .row strong { max-width: 42mm; text-align: right; word-break: break-word; }
                    .note { margin-top: 2mm; padding-top: 1.5mm; border-top: 1px solid #e2e8f0; color: #64748b; font-size: 8.8px; line-height: 1.3; }
                </style>
            </head>
            <body>
                <section class="ticket">
                    <div class="brand">Gatehouse Pro</div>
                    <h1>Phiếu mã QR</h1>
                    <div class="muted">Vui lòng xuất trình mã này tại quầy lễ tân.</div>
                    <div class="qr">${adminQrTicket.qrSvg}</div>
                    <div class="code">${safe(adminQrTicket.code)}</div>
                    <div class="row"><span>Khách</span><strong>${safe(adminQrTicket.visitorName)}</strong></div>
                    <div class="row"><span>Công ty</span><strong>${safe(adminQrTicket.visitorCompany)}</strong></div>
                    <div class="row"><span>Người tiếp</span><strong>${safe(adminQrTicket.hostName)}</strong></div>
                    <div class="row"><span>Giờ hẹn</span><strong>${safe(adminQrTicket.scheduledAt)}</strong></div>
                    <div class="row"><span>Trạng thái</span><strong>${safe(adminQrTicket.status)}</strong></div>
                    <div class="note">Mã QR chỉ dùng cho lượt khách này. Không chia sẻ mã cho người khác.</div>
                </section>
                <script>
                    window.onload = () => {
                        window.focus();
                        window.print();
                    };
                <\/script>
            </body>
            </html>
        `);
        printWindow.document.close();
    }
</script>
@endpush
@endunless

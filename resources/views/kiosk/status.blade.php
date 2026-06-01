<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Trạng thái yêu cầu | VMS Kiosk</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --ink: #0b1f3a;
            --muted: #64748b;
            --line: #dbe7f5;
            --blue: #146bd7;
            --green: #22c55e;
            --amber: #d97706;
            --red: #dc2626;
        }

        * { box-sizing: border-box; }

        body {
            min-height: 100vh;
            margin: 0;
            display: grid;
            place-items: center;
            padding: 2rem 1rem;
            background:
                radial-gradient(circle at 50% 8%, rgba(34, 197, 94, 0.08), transparent 26%),
                linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            color: var(--ink);
            font-family: "Manrope", sans-serif;
        }

        .status-page {
            width: min(520px, 100%);
            text-align: center;
        }

        .status-icon-wrap {
            position: relative;
            width: 108px;
            height: 108px;
            margin: 0 auto 1.35rem;
            display: grid;
            place-items: center;
            border-radius: 50%;
            background: rgba(34, 197, 94, 0.12);
        }

        .status-icon-wrap::before,
        .status-icon-wrap::after {
            content: "";
            position: absolute;
            inset: 14px;
            border-radius: 50%;
            background: rgba(34, 197, 94, 0.14);
        }

        .status-icon-wrap::after {
            inset: 28px;
            background: rgba(34, 197, 94, 0.18);
        }

        .status-icon {
            position: relative;
            z-index: 1;
            width: 70px;
            height: 70px;
            display: grid;
            place-items: center;
            border-radius: 50%;
            color: #fff;
            background: linear-gradient(135deg, #16a34a, #22c55e);
            box-shadow: 0 18px 34px rgba(34, 197, 94, 0.25);
            font-size: 2.2rem;
        }

        .status-icon.pending {
            background: linear-gradient(135deg, #f59e0b, #fbbf24);
            box-shadow: 0 18px 34px rgba(245, 158, 11, 0.22);
        }

        .status-icon.rejected,
        .status-icon.cancelled {
            background: linear-gradient(135deg, #dc2626, #ef4444);
            box-shadow: 0 18px 34px rgba(220, 38, 38, 0.22);
        }

        .status-page h1 {
            margin: 0;
            font-family: "Plus Jakarta Sans", sans-serif;
            font-size: clamp(2rem, 5vw, 3rem);
            font-weight: 600;
            letter-spacing: -0.06em;
            line-height: 1.05;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            margin-top: 1rem;
            padding: 0.48rem 0.95rem;
            border-radius: 999px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .status-badge.pending { color: #92400e; background: #fef3c7; }
        .status-badge.approved,
        .status-badge.checked_in { color: #065f46; background: #dcfce7; }
        .status-badge.rejected,
        .status-badge.cancelled { color: #991b1b; background: #fee2e2; }

        .status-message {
            max-width: 420px;
            margin: 1.3rem auto 1.7rem;
            color: var(--muted);
            font-size: 1rem;
            line-height: 1.65;
        }

        .status-info {
            display: grid;
            gap: 0.9rem;
            margin: 0 auto 1.9rem;
            padding: 1.1rem;
            border: 1px solid var(--line);
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.86);
            box-shadow: 0 16px 42px rgba(15, 38, 66, 0.07);
            text-align: left;
        }

        .status-row {
            display: grid;
            grid-template-columns: 32px 1fr auto;
            gap: 0.75rem;
            align-items: center;
        }

        .status-row i {
            width: 32px;
            height: 32px;
            display: grid;
            place-items: center;
            border-radius: 10px;
            background: #eff6ff;
            color: var(--blue);
        }

        .status-row span {
            color: var(--muted);
            font-size: 0.85rem;
        }

        .status-row strong {
            color: var(--ink);
            font-size: 0.9rem;
            font-weight: 500;
            text-align: right;
        }

        .status-actions {
            display: grid;
            gap: 0.75rem;
            justify-content: center;
        }
        .status-btn {
            min-width: 220px;
            min-height: 48px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            border: 1px solid #bfdbfe;
            border-radius: 14px;
            background: #fff;
            color: var(--blue);
            font-size: 0.95rem;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
        }

        .status-btn-primary {
            border: 0;
            color: #fff;
            background: linear-gradient(135deg, #146bd7, #0cb4d8);
            box-shadow: 0 14px 28px rgba(20, 107, 215, 0.16);
        }

        .status-form {
            display: contents;
        }

        .status-footer {
            margin-top: 1.7rem;
            color: #7f94ad;
            font-size: 0.95rem;
        }

        .status-alert {
            margin: 0 auto 1rem;
            padding: 0.8rem 1rem;
            border-radius: 14px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .status-alert.success { color: #047857; background: #ecfdf5; border: 1px solid #bbf7d0; }
        .status-alert.danger { color: #b91c1c; background: #fef2f2; border: 1px solid #fecaca; }

        @media (max-width: 560px) {
            .status-row {
                grid-template-columns: 32px 1fr;
            }

            .status-row strong {
                grid-column: 2;
                text-align: left;
            }
        }
    </style>
</head>
@php
    $statusMap = [
        'pending' => [
            'title' => 'Yêu cầu đã được gửi',
            'label' => 'Đang chờ phê duyệt',
            'message' => 'Yêu cầu của bạn đã được gửi đến người tiếp khách. Vui lòng chờ xác nhận tại quầy.',
            'icon' => 'bi-hourglass-split',
            'tone' => 'pending',
        ],
        'approved' => [
            'title' => 'Yêu cầu đã được duyệt',
            'label' => 'Đã được phê duyệt',
            'message' => 'Bạn có thể xác nhận check-in để hoàn tất thủ tục vào công ty.',
            'icon' => 'bi-check-lg',
            'tone' => 'approved',
        ],
        'checked_in' => [
            'title' => 'Check-in thành công',
            'label' => 'Đã check-in',
            'message' => 'Vui lòng nhận badge tại quầy lễ tân và làm theo hướng dẫn ra/vào.',
            'icon' => 'bi-check-lg',
            'tone' => 'checked_in',
        ],
        'rejected' => [
            'title' => 'Yêu cầu bị từ chối',
            'label' => 'Bị từ chối',
            'message' => 'Rất tiếc, yêu cầu của bạn chưa được chấp thuận. Vui lòng liên hệ lễ tân để được hỗ trợ.',
            'icon' => 'bi-x-lg',
            'tone' => 'rejected',
        ],
        'cancelled' => [
            'title' => 'Yêu cầu đã hủy',
            'label' => 'Đã hủy',
            'message' => 'Yêu cầu này đã được hủy. Vui lòng tạo yêu cầu mới nếu cần tiếp tục.',
            'icon' => 'bi-x-lg',
            'tone' => 'cancelled',
        ],
    ];

    $status = $statusMap[$visit->status] ?? $statusMap['pending'];
@endphp
<body>
    <main class="status-page">
        @if (session('status'))
            <div class="status-alert success">{{ session('status') }}</div>
        @endif
        @if (session('error'))
            <div class="status-alert danger">{{ session('error') }}</div>
        @endif

        <div class="status-icon-wrap">
            <div class="status-icon {{ $status['tone'] }}">
                <i class="bi {{ $status['icon'] }}"></i>
            </div>
        </div>

        <h1>{{ $status['title'] }}</h1>

        <div class="status-badge {{ $status['tone'] }}">
            <i class="bi {{ $status['icon'] }}"></i>
            {{ $status['label'] }}
        </div>

        <p class="status-message">{{ $status['message'] }}</p>

        <section class="status-info">
            <div class="status-row">
                <i class="bi bi-calendar2-check"></i>
                <span>Mã lịch hẹn</span>
                <strong>{{ $visit->code }}</strong>
            </div>
            <div class="status-row">
                <i class="bi bi-person-badge"></i>
                <span>Người tiếp khách</span>
                <strong>{{ $visit->hostEmployee?->name ?? '-' }}</strong>
            </div>
            <div class="status-row">
                <i class="bi bi-clock"></i>
                <span>Giờ hẹn</span>
                <strong>{{ $visit->scheduled_at?->format('d/m/Y H:i') ?? '-' }}</strong>
            </div>
        </section>
<div class="status-actions">
            @if ($visit->status === 'approved' && $canConfirm)
                <form class="status-form" method="post" action="{{ route('kiosk.checkin.confirm', $visit) }}">
                    @csrf
                    <button class="status-btn status-btn-primary" type="submit">
                        <i class="bi bi-box-arrow-in-right"></i>
                        Xác nhận check-in
                    </button>
                </form>
            @else
                <a class="status-btn status-btn-primary" href="{{ route('kiosk.checkin.status', $visit) }}">
                    <i class="bi bi-arrow-clockwise"></i>
                    Kiểm tra trạng thái
                </a>
            @endif

            <a class="status-btn" href="{{ route('kiosk.index') }}">
                <i class="bi bi-house"></i>
                Về màn hình kiosk
            </a>
        </div>

        <div class="status-footer">Cảm ơn bạn!</div>
    </main>

    @if ($visit->status === 'pending')
        <script>
            setTimeout(() => window.location.reload(), 30000);
        </script>
    @endif
</body>
</html>

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HasAdminLayoutData;
use App\Models\Approval;
use App\Models\AccessControlLog;
use App\Models\AuditLog;
use App\Models\Badge;
use App\Models\Employee;
use App\Models\Notification;
use App\Models\User;
use App\Models\Visit;
use App\Models\Visitor;
use App\Models\Watchlist;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class AdminUiController extends Controller
{
    use HasAdminLayoutData;

    public function dashboard(): View
    {
        $todayVisits = $this->todayVisitsForDashboard()->get();
        $summary = $this->dashboardSummaryData();

        $alertsData = collect($this->dashboardAlertsData())->take(5)->values()->all();

        return view('admin.dashboard', $this->withBase([
            'stats' => [
                'today'        => $summary['today_visits'],
                'in_company'   => $summary['in_company'],
                'pending'      => $summary['pending'],
                'checked_out'  => $summary['checked_out_today'],
                'overstay'     => $summary['overstay'] ?? 0,
            ],
            'visits' => $this->mapVisitRows($todayVisits),
            'alerts' => $alertsData,
        ]));
    }

    public function dashboardSummary(): JsonResponse
    {
        return response()->json($this->dashboardSummaryData());
    }

    public function dashboardLiveVisitors(): JsonResponse
    {
        return response()->json([
            'data' => $this->liveVisitorsData(),
            'updated_at' => now()->toIso8601String(),
        ]);
    }

    public function dashboardAlerts(): JsonResponse
    {
        return response()->json([
            'data' => $this->dashboardAlertsData(),
            'updated_at' => now()->toIso8601String(),
        ]);
    }

    public function dashboardEvents(): StreamedResponse
    {
        return response()->stream(function (): void {
            $payload = [
                'summary' => $this->dashboardSummaryData(),
                'live_visitors' => $this->liveVisitorsData(),
                'alerts' => $this->dashboardAlertsData(),
                'updated_at' => now()->toIso8601String(),
            ];

            echo "event: dashboard.snapshot\n";
            echo 'data: '.json_encode($payload, JSON_UNESCAPED_UNICODE)."\n\n";

            if (ob_get_level() > 0) {
                ob_flush();
            }

            flush();
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    public function visitsIndex(): View
    {
        $visitModels = $this->baseVisitQuery()
            ->orderByDesc('scheduled_at')
            ->limit(120)
            ->get();
        $today = now()->toDateString();
        $statusCounts = [
            'all' => $visitModels->count(),
            'pending' => $visitModels->where('status', 'pending')->count(),
            'approved' => $visitModels->where('status', 'approved')->count(),
            'checked_in' => $visitModels->where('status', 'checked_in')->count(),
            'checked_out' => $visitModels->where('status', 'checked_out')->count(),
            'rejected' => $visitModels->where('status', 'rejected')->count(),
        ];
        $stats = [
            'today' => Visit::query()->whereDate('scheduled_at', $today)->count(),
            'pending' => Visit::query()->where('status', 'pending')->count(),
            'approved' => Visit::query()->where('status', 'approved')->count(),
            'checked_in' => Visit::query()->where('status', 'checked_in')->count(),
            'checked_out' => Visit::query()->where('status', 'checked_out')->count(),
            'overstay' => Visit::query()
                ->where('status', 'checked_in')
                ->whereNotNull('expected_checkout_at')
                ->where('expected_checkout_at', '<', now())
                ->count(),
        ];
        $upcoming = $visitModels
            ->where('status', 'approved')
            ->where('scheduled_at', '>=', now())
            ->sortBy('scheduled_at')
            ->take(3)
            ->values();

        return view('admin.visits.index', $this->withBase([
            'visits' => $this->mapVisitRows($visitModels),
            'visitStats' => $stats,
            'statusCounts' => $statusCounts,
            'upcomingVisits' => $this->mapVisitRows($upcoming),
            'statusFilters' => [
                'all' => 'Tất cả',
                'pending' => 'Chờ duyệt',
                'approved' => 'Đã duyệt',
                'checked_in' => 'Đang trong công ty',
                'checked_out' => 'Đã rời công ty',
                'rejected' => 'Từ chối',
            ],
        ]));
    }

    public function visitsCreate(): View
    {
        return view('admin.visits.create', $this->withBase([
            'hosts' => $this->hostsForSelect(),
            'accessZones' => $this->accessZones(),
        ]));
    }

    public function visitsStore(Request $request): RedirectResponse
    {
        $validated = $this->validateVisitPayload($request);
        [$scheduledAt, $expectedCheckoutAt] = $this->parseVisitTimes($validated);

        if ($expectedCheckoutAt->lessThanOrEqualTo($scheduledAt)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gio ra du kien phai lon hon gio vao.');
        }

        $visitor = $this->firstOrCreateVisitor($validated);

        $qrBaseTime = $scheduledAt->lt(now()) ? now() : $scheduledAt;

        $visit = Visit::query()->create([
            'code' => $this->generateVisitCode(),
            'visitor_id' => $visitor->id,
            'host_employee_id' => (int) $validated['host_employee_id'],
            'scheduled_at' => $scheduledAt,
            'expected_checkout_at' => $expectedCheckoutAt,
            'status' => 'pending',
            'purpose' => $validated['purpose'],
            'access_zone' => $validated['access_zone'] ?? null,
            'checkin_method' => $validated['checkin_method'],
            'qr_token' => $this->generateQrToken(),
            'qr_expires_at' => $qrBaseTime->copy()->addDay(),
            'rejection_reason' => null,
        ]);

        Approval::query()->updateOrCreate(
            ['visit_id' => $visit->id],
            [
                'approver_user_id' => null,
                'status' => 'pending',
                'note' => null,
                'acted_at' => null,
            ]
        );

        $this->logAudit('visit.created', 'visit', (string) $visit->id, [
            'code' => $visit->code,
            'status' => $visit->status,
            'qr_expires_at' => $visit->qr_expires_at?->toDateTimeString(),
        ]);
        $this->notifyHost($visit, 'visit.pending', 'Lich hen can phe duyet', "Lich {$visit->code} dang cho ban phe duyet va da co ma QR.", 'warning');
        $this->notifyUsersWithPermission('approvals.manage', 'visit.pending', 'Co lich hen moi can duyet', "Lich {$visit->code} vua duoc tao, da sinh QR va dang cho phe duyet.", 'warning', $visit);
        $this->scanWatchlistForVisit($visit, 'visit.created');

        return redirect()
            ->route('admin.visits.show', $visit)
            ->with('status', 'Da tao lich hen '.$visit->code.' va sinh QR thanh cong. Lich dang cho phe duyet.');
    }

    public function visitsShow(Visit $visit): View
    {
        $visit->load([
            'visitor',
            'hostEmployee.department',
            'approval.approver',
            'activeBadge',
            'badges',
        ]);

        return view('admin.visits.show', $this->withBase([
            'visit' => $visit,
            'canEdit' => ! in_array($visit->status, ['checked_in', 'checked_out', 'cancelled'], true),
            'canCancel' => ! in_array($visit->status, ['checked_in', 'checked_out', 'cancelled'], true),
            'canGenerateQr' => $visit->status === 'approved',
            'activityLogs' => AuditLog::query()
                ->where('entity_type', 'visit')
                ->where('entity_id', (string) $visit->id)
                ->latest()
                ->limit(8)
                ->get(),
        ]));
    }

    public function visitsEdit(Visit $visit): View|RedirectResponse
    {
        if (in_array($visit->status, ['checked_in', 'checked_out', 'cancelled'], true)) {
            return redirect()
                ->route('admin.visits.show', $visit)
                ->with('error', "Khong the sua lich {$visit->code} trong trang thai {$visit->status}.");
        }

        $visit->load(['visitor', 'hostEmployee.department']);

        return view('admin.visits.edit', $this->withBase([
            'visit' => $visit,
            'hosts' => $this->hostsForSelect(),
            'accessZones' => $this->accessZones(),
        ]));
    }

    public function visitsUpdate(Request $request, Visit $visit): RedirectResponse
    {
        if (in_array($visit->status, ['checked_in', 'checked_out', 'cancelled'], true)) {
            return redirect()
                ->route('admin.visits.show', $visit)
                ->with('error', "Khong the cap nhat lich {$visit->code} trong trang thai {$visit->status}.");
        }

        $validated = $this->validateVisitPayload($request);
        [$scheduledAt, $expectedCheckoutAt] = $this->parseVisitTimes($validated);

        if ($expectedCheckoutAt->lessThanOrEqualTo($scheduledAt)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gio ra du kien phai lon hon gio vao.');
        }

        $oldStatus = $visit->status;
        $visitor = $this->updateVisitVisitor($visit, $validated);

        $qrBaseTime = $scheduledAt->lt(now()) ? now() : $scheduledAt;

        $visit->update([
            'visitor_id' => $visitor->id,
            'host_employee_id' => (int) $validated['host_employee_id'],
            'scheduled_at' => $scheduledAt,
            'expected_checkout_at' => $expectedCheckoutAt,
            'status' => 'pending',
            'purpose' => $validated['purpose'],
            'access_zone' => $validated['access_zone'] ?? null,
            'checkin_method' => $validated['checkin_method'],
            'qr_token' => $visit->qr_token ?: $this->generateQrToken(),
            'qr_expires_at' => $qrBaseTime->copy()->addDay(),
            'rejection_reason' => null,
        ]);

        Approval::query()->updateOrCreate(
            ['visit_id' => $visit->id],
            [
                'approver_user_id' => null,
                'status' => 'pending',
                'note' => 'Lich da duoc cap nhat va can phe duyet lai.',
                'acted_at' => null,
            ]
        );

        $this->logAudit('visit.updated', 'visit', (string) $visit->id, [
            'code' => $visit->code,
            'old_status' => $oldStatus,
            'new_status' => 'pending',
        ]);
        $this->notifyHost($visit, 'visit.updated', 'Lich hen da cap nhat', "Lich {$visit->code} da cap nhat va can phe duyet lai.", 'warning');
        $this->notifyUsersWithPermission('approvals.manage', 'visit.updated', 'Lich hen can duyet lai', "Lich {$visit->code} da cap nhat va quay ve cho duyet.", 'warning', $visit);

        return redirect()
            ->route('admin.visits.show', $visit)
            ->with('status', "Da cap nhat lich {$visit->code} va dua ve trang thai cho duyet.");
    }

    public function cancelVisit(Request $request, Visit $visit): RedirectResponse
    {
        if (in_array($visit->status, ['checked_in', 'checked_out', 'cancelled'], true)) {
            return redirect()
                ->back()
                ->with('error', "Khong the huy lich {$visit->code} trong trang thai {$visit->status}.");
        }

        $reason = trim((string) $request->input('reason', 'Huy lich hen theo yeu cau van hanh.'));

        $visit->update([
            'status' => 'cancelled',
            'rejection_reason' => $reason,
        ]);

        Approval::query()->updateOrCreate(
            ['visit_id' => $visit->id],
            [
                'approver_user_id' => $this->actingUserId(),
                'status' => 'cancelled',
                'note' => $reason,
                'acted_at' => now(),
            ]
        );

        $this->logAudit('visit.cancelled', 'visit', (string) $visit->id, [
            'code' => $visit->code,
            'reason' => $reason,
        ]);
        $this->notifyHost($visit, 'visit.cancelled', 'Lich hen da huy', "Lich {$visit->code} da bi huy. Ly do: {$reason}", 'danger');

        return redirect()
            ->route('admin.visits.show', $visit)
            ->with('status', "Da huy lich {$visit->code}.");
    }

    public function generateVisitQr(Visit $visit): RedirectResponse
    {
        if ($visit->status !== 'approved') {
            return redirect()
                ->back()
                ->with('error', "Chỉ có thể sinh QR cho lịch đã được duyệt. Trạng thái hiện tại: {$visit->status}.");
        }

        $baseTime = $visit->scheduled_at ?? now();
        if ($baseTime->lt(now())) {
            $baseTime = now();
        }

        $visit->update([
            'qr_token' => $this->generateQrToken(),
            'qr_expires_at' => $baseTime->copy()->addDay(),
        ]);

        $this->logAudit('visit.qr_generated', 'visit', (string) $visit->id, [
            'code' => $visit->code,
            'expires_at' => $visit->qr_expires_at?->toDateTimeString(),
        ]);
        $this->notifyHost($visit, 'visit.qr_generated', 'Đã sinh mã QR', "Lịch {$visit->code} đã có mã QR mới.", 'info');

        return redirect()
            ->route('admin.visits.show', $visit)
            ->with('status', "Đã sinh mã QR cho lịch {$visit->code}.");
    }

    public function approvalsIndex(): View
    {
        $today = now()->toDateString();

        $pendingVisits = $this->scopeVisitsForDepartmentManager(
            $this->baseVisitQuery()->where('status', 'pending')
        )
            ->orderBy('scheduled_at')
            ->get();

        $approvedVisits = $this->scopeVisitsForDepartmentManager(
            $this->baseVisitQuery()->where('status', 'approved')
        )
            ->orderByDesc('updated_at')
            ->limit(30)
            ->get();

        $rejectedVisits = $this->scopeVisitsForDepartmentManager(
            $this->baseVisitQuery()->where('status', 'rejected')
        )
            ->orderByDesc('updated_at')
            ->limit(20)
            ->get();

        $approvedToday = $this->scopeVisitsForDepartmentManager(
            $this->baseVisitQuery()
                ->where('status', 'approved')
                ->whereDate('updated_at', $today)
        )->count();

        $rejectedToday = $this->scopeVisitsForDepartmentManager(
            $this->baseVisitQuery()
                ->where('status', 'rejected')
                ->whereDate('updated_at', $today)
        )->count();

        $todayVisits = $this->scopeVisitsForDepartmentManager(
            $this->baseVisitQuery()->whereDate('scheduled_at', $today)
        )->count();

        $mapApprovalRows = function (Collection $visits): Collection {
            return $visits->map(function (Visit $visit): array {
            $createdAt = $visit->created_at instanceof Carbon ? $visit->created_at : null;
            $waitingMinutes = $createdAt ? max(0, (int) $createdAt->diffInMinutes(now())) : 0;

            return [
                'id' => $visit->id,
                'code' => $visit->code,
                'visitor' => $visit->visitor?->full_name ?? '-',
                'company' => $visit->visitor?->company ?? '-',
                'host' => $visit->hostEmployee?->name ?? '-',
                'department' => $visit->hostEmployee?->department?->name ?? '-',
                'time' => $visit->scheduled_at?->format('H:i') ?? '-',
                'date' => $visit->scheduled_at?->format('d/m/Y') ?? '-',
                'created_time' => $createdAt?->format('H:i - d/m/Y') ?? '-',
                'waiting_minutes' => $waitingMinutes,
                'status' => $visit->status,
                'purpose' => $visit->purpose,
                'note' => $visit->visitor?->note ?? '-',
            ];
            })->values();
        };

        $pendingRows = $mapApprovalRows($pendingVisits);
        $approvedRows = $mapApprovalRows($approvedVisits);
        $rejectedRows = $mapApprovalRows($rejectedVisits);
        $approvalRows = $pendingRows
            ->concat($approvedRows)
            ->concat($rejectedRows)
            ->values();

        return view('admin.approvals.index', $this->withBase([
            'pendingVisits' => $pendingRows->all(),
            'approvalVisits' => $approvalRows->all(),
            'approvalStats' => [
                'pending' => $pendingRows->count(),
                'approved' => $approvedRows->count(),
                'rejected' => $rejectedRows->count(),
                'all' => $approvalRows->count(),
                'approved_today' => $approvedToday,
                'rejected_today' => $rejectedToday,
                'today' => $todayVisits,
                'urgent' => $pendingRows->where('waiting_minutes', '>=', 15)->count(),
            ],
            'urgentApprovals' => $pendingRows
                ->sortByDesc('waiting_minutes')
                ->take(4)
                ->values()
                ->all(),
        ]));
    }

    public function approveVisit(Visit $visit): RedirectResponse
    {
        if (! $this->canActOnVisit($visit)) {
            return redirect()->back()->with('error', "Bạn không có quyền xử lý lịch {$visit->code}.");
        }

        if ($visit->status !== 'pending') {
            return redirect()->back()->with('error', "Lịch {$visit->code} không ở trạng thái chờ duyệt.");
        }

        $visit->update([
            'status' => 'approved',
            'rejection_reason' => null,
            'qr_token' => $visit->qr_token ?: $this->generateQrToken(),
            'qr_expires_at' => $visit->qr_expires_at ?? ($visit->scheduled_at?->lt(now()) ? now() : ($visit->scheduled_at ?? now()))->copy()->addDay(),
        ]);

        Approval::query()->updateOrCreate(
            ['visit_id' => $visit->id],
            [
                'approver_user_id' => $this->actingUserId(),
                'status' => 'approved',
                'note' => 'Đã duyệt lịch tiếp đón. Mã QR đã sẵn sàng.',
                'acted_at' => now(),
            ]
        );

        $this->logAudit('approval.approved', 'visit', (string) $visit->id, [
            'code' => $visit->code,
            'qr_expires_at' => $visit->qr_expires_at?->toDateTimeString(),
        ]);
        $this->notifyUsersWithPermission('checkin.manage', 'approval.approved', 'Lịch đã duyệt, sẵn sàng làm thủ tục vào', "Lịch {$visit->code} đã được duyệt và mã QR đã sẵn sàng.", 'success', $visit);

        return redirect()->back()->with('status', "Đã duyệt lịch {$visit->code}. Mã QR đã sẵn sàng.");
    }

    public function rejectVisit(Request $request, Visit $visit): RedirectResponse
    {
        if (! $this->canActOnVisit($visit)) {
            return redirect()->back()->with('error', "Ban khong co quyen xu ly lich {$visit->code}.");
        }

        if (! in_array($visit->status, ['pending', 'approved'], true)) {
            return redirect()->back()->with('error', "Khong the tu choi lich {$visit->code} trong trang thai hien tai.");
        }

        $reason = trim((string) $request->input('reason', 'Khong phu hop lich tiep khach.'));

        $visit->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
        ]);

        Approval::query()->updateOrCreate(
            ['visit_id' => $visit->id],
            [
                'approver_user_id' => $this->actingUserId(),
                'status' => 'rejected',
                'note' => $reason,
                'acted_at' => now(),
            ]
        );

        $this->logAudit('approval.rejected', 'visit', (string) $visit->id, [
            'code' => $visit->code,
            'reason' => $reason,
        ]);
        $this->notifyUsersWithPermission('visits.manage', 'approval.rejected', 'Lich hen bi tu choi', "Lich {$visit->code} bi tu choi. Ly do: {$reason}", 'danger', $visit);

        return redirect()->back()->with('status', "Da tu choi lich {$visit->code}.");
    }

    public function waitVisit(Visit $visit): RedirectResponse
    {
        if (! $this->canActOnVisit($visit)) {
            return redirect()->back()->with('error', "Ban khong co quyen xu ly lich {$visit->code}.");
        }

        if ($visit->status !== 'pending') {
            return redirect()->back()->with('error', "Lich {$visit->code} khong o trang thai pending.");
        }

        Approval::query()->updateOrCreate(
            ['visit_id' => $visit->id],
            [
                'approver_user_id' => $this->actingUserId(),
                'status' => 'pending',
                'note' => 'Yeu cau khach cho them de xac nhan host.',
                'acted_at' => now(),
            ]
        );

        $this->logAudit('approval.wait', 'visit', (string) $visit->id, [
            'code' => $visit->code,
        ]);
        $this->notifyUsersWithPermission('visits.manage', 'approval.wait', 'Host yeu cau khach cho', "Lich {$visit->code} can cho them de xac nhan host.", 'warning', $visit);

        return redirect()->back()->with('status', "Da danh dau lich {$visit->code} la cho khach doi.");
    }

    public function checkinIndex(Request $request): View
    {
        $today = now()->toDateString();

        $readyToCheckin = $this->baseVisitQuery()
            ->where('status', 'approved')
            ->orderBy('scheduled_at')
            ->get();

        $approvedWaitingCheckin = $this->baseVisitQuery()
            ->where('status', 'approved')
            ->orderByRaw('CASE WHEN scheduled_at >= ? THEN 0 ELSE 1 END', [now()])
            ->orderBy('scheduled_at')
            ->limit(8)
            ->get();

        $scannedVisit = null;
        $scannedVisitId = $request->session()->get('checkin_scanned_visit_id');
        $scannedByQr = (bool) $request->session()->get('checkin_scanned_by_qr', false);
        if (is_numeric($scannedVisitId)) {
            $scannedVisit = $this->baseVisitQuery()->find((int) $scannedVisitId);
        }

        $inCompany      = Visit::query()->where('status', 'checked_in')->count();
        $checkedOutToday = Visit::query()->where('status', 'checked_out')->whereDate('actual_checkout_at', $today)->count();
        $checkedInToday  = Visit::query()->where('status', 'checked_in')->whereDate('actual_checkin_at', $today)->count();
        $totalToday      = $inCompany + $checkedOutToday;

        return view('admin.checkin.index', $this->withBase([
            'readyToCheckin'  => $this->mapVisitRows($readyToCheckin),
            'upcomingToday'   => $this->mapVisitRows($approvedWaitingCheckin),
            'scannedVisit'    => $scannedVisit,
            'scannedQrExpired'=> $scannedByQr && ($scannedVisit?->qr_expires_at?->lt(now()) ?? false),
            'todayStats'      => [
                'total'        => $totalToday,
                'in_company'   => $inCompany,
                'checked_out'  => $checkedOutToday,
                'pct_in'       => $totalToday > 0 ? round($inCompany / $totalToday * 100) : 0,
                'pct_out'      => $totalToday > 0 ? round($checkedOutToday / $totalToday * 100) : 0,
            ],
        ]));
    }

    public function confirmCheckin(Visit $visit): RedirectResponse
    {
        $error = $this->performCheckin($visit);
        if ($error !== null) {
            return redirect()->back()->with('error', $error);
        }

        $this->logAudit('visit.checked_in', 'visit', (string) $visit->id, [
            'code' => $visit->code,
        ]);
        $this->notifyHost($visit, 'visit.checked_in', 'Khách đã check-in', "Khách của lịch {$visit->code} đã vào công ty.", 'success');
        $this->notifyUsersWithPermission('alerts.view', 'visit.checked_in', 'Khách đang trong công ty', "Lịch {$visit->code} đã check-in.", 'info', $visit);
        $this->scanWatchlistForVisit($visit, 'visit.checked_in');

        return redirect()->back()->with('status', "Đã xác nhận khách vào cho lịch {$visit->code}.");
    }

    public function scanCheckinQr(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'qr_token' => ['required', 'string', 'max:80'],
        ]);

        $lookup = trim($validated['qr_token']);
        $visit = Visit::query()->where('qr_token', $lookup)->first();
        $scannedByQr = $visit !== null;

        if ($visit === null) {
            $visit = Visit::query()->where('code', $lookup)->first();
        }

        if ($visit === null) {
            return redirect()->back()->with('error', 'Không tìm thấy lịch hẹn với mã QR hoặc mã lịch hẹn này.');
        }

        $redirect = redirect()
            ->route('admin.checkin.index')
            ->with('checkin_scanned_visit_id', $visit->id)
            ->with('checkin_scanned_by_qr', $scannedByQr);

        if ($scannedByQr && $visit->qr_expires_at !== null && $visit->qr_expires_at->lt(now())) {
            return $redirect->with('error', "QR của lịch {$visit->code} đã hết hạn. Vui lòng tạo QR mới hoặc liên hệ quản trị.");
        }

        if ($visit->status !== 'approved') {
            return $redirect->with('error', "Đã tìm thấy lịch {$visit->code}, nhưng trạng thái hiện tại là {$this->visitStatusLabel($visit->status)}. Chưa thể check-in.");
        }

        $this->logAudit('visit.qr_scanned_for_checkin', 'visit', (string) $visit->id, [
            'code' => $visit->code,
        ]);

        return $redirect->with('status', "Mã hợp lệ cho lịch {$visit->code}. Vui lòng kiểm tra thông tin và bấm xác nhận khách vào.");
    }
    public function checkoutIndex(): View
    {
        $insideVisits = $this->baseVisitQuery()
            ->where('status', 'checked_in')
            ->orderBy('scheduled_at')
            ->get();

        $scannedVisit = null;
        $scannedVisitId = session('checkout_scanned_visit_id');
        if (is_numeric($scannedVisitId)) {
            $scannedVisit = $this->baseVisitQuery()->find((int) $scannedVisitId);
        }

        $checkedOutToday = Visit::query()
            ->where('status', 'checked_out')
            ->whereDate('actual_checkout_at', now()->toDateString())
            ->count();
        $overstayInside = Visit::query()
            ->where('status', 'checked_in')
            ->whereNotNull('expected_checkout_at')
            ->where('expected_checkout_at', '<', now())
            ->count();
        $onTimeRate = $checkedOutToday > 0
            ? Visit::query()
                ->where('status', 'checked_out')
                ->whereDate('actual_checkout_at', now()->toDateString())
                ->whereColumn('actual_checkout_at', '<=', 'expected_checkout_at')
                ->count()
            : 0;

        return view('admin.checkout.index', $this->withBase([
            'insideVisits' => $insideVisits->map(function (Visit $visit): array {
                $isOverstay = $visit->expected_checkout_at?->lt(now()) ?? false;

                return [
                    'id' => $visit->id,
                    'code' => $visit->code,
                    'visitor' => $visit->visitor?->full_name ?? '-',
                    'company' => $visit->visitor?->company ?? '-',
                    'host' => $visit->hostEmployee?->name ?? '-',
                    'department' => $visit->hostEmployee?->department?->name ?? '-',
                    'checkin_time' => $visit->actual_checkin_at?->format('H:i') ?? '-',
                    'remaining' => $visit->expected_checkout_at === null
                        ? '-'
                        : ($isOverstay ? 'Quá giờ' : $this->humanDuration(now(), $visit->expected_checkout_at)),
                    'is_overstay' => $isOverstay,
                ];
            })->all(),
            'scannedVisit' => $scannedVisit,
            'checkoutStats' => [
                'checked_out_today' => $checkedOutToday,
                'inside' => $insideVisits->count(),
                'overstay' => $overstayInside,
                'on_time_rate' => $checkedOutToday > 0 ? round($onTimeRate / $checkedOutToday * 100) : 0,
            ],
        ]));
    }

    public function badgesIndex(): View
    {
        $badges = Badge::query()
            ->with(['visit.visitor', 'visit.hostEmployee.department'])
            ->orderBy('badge_no')
            ->get();

        return view('admin.badges.index', $this->withBase([
            'badges' => $badges,
        ]));
    }

    public function confirmCheckout(Visit $visit): RedirectResponse
    {
        $error = $this->performCheckout($visit);
        if ($error !== null) {
            return redirect()->back()->with('error', $error);
        }

        $this->logAudit('visit.checked_out', 'visit', (string) $visit->id, [
            'code' => $visit->code,
        ]);
        $this->notifyHost($visit, 'visit.checked_out', 'Khách đã rời công ty', "Khách của lịch {$visit->code} đã ra khỏi công ty.", 'info');

        return redirect()->back()->with('status', "Đã xác nhận khách ra cho lịch {$visit->code}.");
    }
    public function scanCheckoutQr(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'qr_token' => ['required', 'string', 'max:80'],
        ]);

        $lookup = trim($validated['qr_token']);
        $visit = Visit::query()
            ->where('qr_token', $lookup)
            ->orWhere('code', $lookup)
            ->first();

        if ($visit === null) {
            return redirect()->back()->with('error', 'Không tìm thấy lịch hẹn với mã QR hoặc mã lịch hẹn này.');
        }

        if ($visit->status !== 'checked_in') {
            return redirect()
                ->route('admin.checkout.index')
                ->with('checkout_scanned_visit_id', $visit->id)
                ->with('error', "Đã tìm thấy lịch {$visit->code}, nhưng trạng thái hiện tại là {$this->visitStatusLabel($visit->status)}. Chỉ khách đang trong công ty mới được làm thủ tục ra.");
        }

        $this->logAudit('visit.qr_scanned_for_checkout', 'visit', (string) $visit->id, [
            'code' => $visit->code,
            'qr_token' => $validated['qr_token'],
        ]);

        return redirect()
            ->route('admin.checkout.index')
            ->with('checkout_scanned_visit_id', $visit->id)
            ->with('status', "Đã tìm thấy lịch {$visit->code}. Vui lòng kiểm tra thông tin trước khi xác nhận khách ra.");
    }
    public function scanCheckoutBadge(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'badge_no' => ['required', 'string', 'max:40'],
        ]);

        $badge = Badge::query()
            ->with('visit')
            ->where('badge_no', $validated['badge_no'])
            ->where('status', 'active')
            ->first();

        if ($badge === null || $badge->visit === null) {
            return redirect()->back()->with('error', 'Không tìm thấy thẻ đang hoạt động.');
        }

        if ($badge->visit->status !== 'checked_in') {
            return redirect()->back()->with('error', "Thẻ {$badge->badge_no} không gắn với khách đang trong công ty.");
        }

        $this->logAudit('visit.badge_scanned_for_checkout', 'visit', (string) $badge->visit->id, [
            'code' => $badge->visit->code,
            'badge_no' => $badge->badge_no,
        ]);

        return redirect()
            ->route('admin.checkout.index')
            ->with('checkout_scanned_visit_id', $badge->visit->id)
            ->with('status', "Đã tìm thấy thẻ {$badge->badge_no}. Vui lòng kiểm tra thông tin trước khi xác nhận khách ra.");
    }
    public function alertsIndex(): View
    {
        $visits = $this->baseVisitQuery()
            ->whereIn('status', ['pending', 'approved', 'checked_in'])
            ->orderBy('scheduled_at')
            ->get();

        return view('admin.alerts.index', $this->withBase([
            'alerts' => $this->buildOperationalAlerts($visits),
        ]));
    }

    public function watchlistsIndex(Request $request): View
    {
        $keyword = trim((string) $request->input('q', ''));
        $status = $request->input('status', 'active');

        $query = Watchlist::query()
            ->with(['visitor', 'creator'])
            ->orderByDesc('id');

        if ($keyword !== '') {
            $query->where(function (Builder $builder) use ($keyword): void {
                $builder
                    ->where('keyword', 'like', '%'.$keyword.'%')
                    ->orWhere('reason', 'like', '%'.$keyword.'%')
                    ->orWhereHas('visitor', fn (Builder $visitorQuery): Builder => $visitorQuery->where('full_name', 'like', '%'.$keyword.'%'));
            });
        }

        if (in_array($status, ['active', 'inactive'], true)) {
            $query->where('status', $status);
        }

        return view('admin.watchlists.index', $this->withBase([
            'watchlists' => $query->paginate(15)->withQueryString(),
            'visitors' => Visitor::query()->orderBy('full_name')->limit(200)->get(),
            'filters' => [
                'q' => $keyword,
                'status' => $status,
            ],
            'matchTypes' => $this->watchlistMatchTypes(),
            'levels' => $this->watchlistLevels(),
        ]));
    }

    public function watchlistsStore(Request $request): RedirectResponse
    {
        $validated = $this->validateWatchlistPayload($request);

        $watchlist = Watchlist::query()->create([
            ...$validated,
            'created_by_user_id' => $this->actingUserId(),
        ]);

        $this->logAudit('watchlist.created', 'watchlist', (string) $watchlist->id, [
            'keyword' => $watchlist->keyword,
            'level' => $watchlist->level,
        ]);

        return redirect()
            ->route('admin.watchlists.show', $watchlist)
            ->with('status', 'Da tao watchlist rule.');
    }

    public function watchlistsShow(Watchlist $watchlist): View
    {
        $watchlist->load(['visitor', 'creator']);

        return view('admin.watchlists.show', $this->withBase([
            'watchlist' => $watchlist,
            'matches' => $this->visitsMatchingWatchlist($watchlist),
        ]));
    }

    public function watchlistsEdit(Watchlist $watchlist): View
    {
        $watchlist->load('visitor');

        return view('admin.watchlists.edit', $this->withBase([
            'watchlist' => $watchlist,
            'visitors' => Visitor::query()->orderBy('full_name')->limit(200)->get(),
            'matchTypes' => $this->watchlistMatchTypes(),
            'levels' => $this->watchlistLevels(),
        ]));
    }

    public function watchlistsUpdate(Request $request, Watchlist $watchlist): RedirectResponse
    {
        $validated = $this->validateWatchlistPayload($request);
        $watchlist->update($validated);

        $this->logAudit('watchlist.updated', 'watchlist', (string) $watchlist->id, [
            'keyword' => $watchlist->keyword,
            'level' => $watchlist->level,
            'status' => $watchlist->status,
        ]);

        return redirect()
            ->route('admin.watchlists.show', $watchlist)
            ->with('status', 'Da cap nhat watchlist rule.');
    }

    public function watchlistsDestroy(Watchlist $watchlist): RedirectResponse
    {
        $id = (string) $watchlist->id;
        $keyword = $watchlist->keyword;
        $watchlist->delete();

        $this->logAudit('watchlist.deleted', 'watchlist', $id, [
            'keyword' => $keyword,
        ]);

        return redirect()
            ->route('admin.watchlists.index')
            ->with('status', 'Da xoa watchlist rule.');
    }

    public function notificationsIndex(Request $request): View
    {
        $status = $request->input('status', 'all');

        $query = Notification::query()
            ->where('user_id', auth()->id())
            ->orderByDesc('id');

        if ($status === 'unread') {
            $query->whereNull('read_at');
        }

        if ($status === 'read') {
            $query->whereNotNull('read_at');
        }

        return view('admin.notifications.index', $this->withBase([
            'notifications' => $query->paginate(15)->withQueryString(),
            'filters' => ['status' => $status],
            'unreadCount' => $this->unreadNotificationCount(),
        ]));
    }

    public function notificationsUnreadCount(): JsonResponse
    {
        return response()->json([
            'unread_count' => $this->unreadNotificationCount(),
            'updated_at' => now()->toIso8601String(),
        ]);
    }

    public function markNotificationRead(Notification $notification): RedirectResponse
    {
        if ((int) $notification->user_id !== (int) auth()->id()) {
            abort(404);
        }

        if ($notification->read_at === null) {
            $notification->update(['read_at' => now()]);
        }

        return redirect()
            ->to($notification->action_url ?: route('admin.notifications.index'))
            ->with('status', 'Da danh dau thong bao la da doc.');
    }

    public function markAllNotificationsRead(): RedirectResponse
    {
        Notification::query()
            ->where('user_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return redirect()
            ->route('admin.notifications.index')
            ->with('status', 'Da danh dau tat ca thong bao la da doc.');
    }

    public function reportsIndex(Request $request): View
    {
        $filters = $this->reportFilters($request);
        $departmentFilter = (string) $request->input('department', 'all');
        $typeFilter = (string) $request->input('type', 'all');
        $from = Carbon::parse($filters['from_date'])->startOfDay();
        $to = Carbon::parse($filters['to_date'])->endOfDay();

        $rangeVisits = $this->baseVisitQuery()
            ->whereBetween('scheduled_at', [$from, $to])
            ->orderByDesc('scheduled_at')
            ->get();

        $visits = $rangeVisits
            ->when($filters['status'] !== 'all', fn (Collection $items) => $items->where('status', $filters['status']))
            ->when($departmentFilter !== 'all', fn (Collection $items) => $items->filter(
                fn (Visit $visit) => (string) ($visit->hostEmployee?->department?->id ?? '') === $departmentFilter
            ))
            ->when($typeFilter === 'company', fn (Collection $items) => $items->filter(
                fn (Visit $visit) => filled($visit->visitor?->company)
            ))
            ->when($typeFilter === 'walkin', fn (Collection $items) => $items->filter(
                fn (Visit $visit) => blank($visit->visitor?->company)
            ))
            ->values();

        $previousFrom = $from->copy()->subDays(max(1, $from->diffInDays($to) + 1));
        $previousTo = $from->copy()->subSecond();
        $previousTotal = $this->baseVisitQuery()
            ->whereBetween('scheduled_at', [$previousFrom, $previousTo])
            ->count();

        $total = $visits->count();
        $checkedIn = $visits->where('status', 'checked_in')->count();
        $checkedOut = $visits->where('status', 'checked_out')->count();
        $pendingCheckin = $visits->where('status', 'approved')->count();
        $overstay = $visits->filter(
            fn (Visit $visit) => $visit->status === 'checked_in'
                && $visit->expected_checkout_at !== null
                && $visit->expected_checkout_at->lt(now())
        )->count();

        $chartDays = [];
        for ($day = $from->copy(); $day->lte($to); $day->addDay()) {
            $daily = $rangeVisits->filter(fn (Visit $visit) => $visit->scheduled_at?->isSameDay($day) ?? false);
            $chartDays[] = [
                'label' => $day->format('d/m'),
                'checkin' => $daily->filter(fn (Visit $visit) => $visit->actual_checkin_at !== null)->count(),
                'checkout' => $daily->filter(fn (Visit $visit) => $visit->actual_checkout_at !== null)->count(),
            ];
        }

        $topHosts = $visits
            ->groupBy(fn (Visit $visit) => $visit->hostEmployee?->id ?? 0)
            ->map(function (Collection $items): array {
                $visit = $items->first();

                return [
                    'name' => $visit?->hostEmployee?->name ?? 'Chưa có người tiếp',
                    'department' => $visit?->hostEmployee?->department?->name ?? '-',
                    'total' => $items->count(),
                ];
            })
            ->sortByDesc('total')
            ->take(5)
            ->values();

        $topDepartments = $visits
            ->groupBy(fn (Visit $visit) => $visit->hostEmployee?->department?->name ?? 'Chưa có phòng ban')
            ->map(fn (Collection $items, string $name): array => [
                'name' => $name,
                'total' => $items->count(),
                'percent' => $total > 0 ? round($items->count() / $total * 100) : 0,
            ])
            ->sortByDesc('total')
            ->take(5)
            ->values();

        $departments = $rangeVisits
            ->map(fn (Visit $visit) => $visit->hostEmployee?->department)
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->values();

        $statusLabels = [
            'pending' => 'Chờ duyệt',
            'approved' => 'Chờ check-in',
            'rejected' => 'Từ chối',
            'checked_in' => 'Đang trong công ty',
            'checked_out' => 'Đã check-out',
            'cancelled' => 'Đã hủy',
        ];

        return view('admin.reports.index', $this->withBase([
            'reportStats' => [
                'total' => $total,
                'checked_in' => $checkedIn,
                'checked_out' => $checkedOut,
                'pending_checkin' => $pendingCheckin,
                'overstay' => $overstay,
                'growth' => $previousTotal > 0 ? round(($total - $previousTotal) / $previousTotal * 100) : ($total > 0 ? 100 : 0),
            ],
            'chartDays' => $chartDays,
            'topHosts' => $topHosts,
            'topDepartments' => $topDepartments,
            'reportVisits' => $visits->take(12)->values(),
            'departments' => $departments,
            'statuses' => [
                'all' => 'Tất cả trạng thái',
                ...$statusLabels,
            ],
            'filters' => [
                ...$filters,
                'department' => $departmentFilter,
                'type' => $typeFilter,
            ],
            'statusLabels' => $statusLabels,
        ]));
    }
    public function reportsVisits(Request $request): JsonResponse
    {
        $filters = $this->reportFilters($request);
        $query = $this->filteredVisitsQuery($filters);

        return response()->json([
            'data' => $this->mapReportVisits($query->orderByDesc('scheduled_at')->limit(300)->get()),
            'filters' => $filters,
            'updated_at' => now()->toIso8601String(),
        ]);
    }

    public function reportsByDepartment(Request $request): JsonResponse
    {
        $filters = $this->reportFilters($request);

        $rows = $this->filteredVisitsQuery($filters)
            ->leftJoin('employees', 'visits.host_employee_id', '=', 'employees.id')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->selectRaw('COALESCE(departments.name, "No department") as department')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN visits.status = 'checked_in' THEN 1 ELSE 0 END) as checked_in")
            ->selectRaw("SUM(CASE WHEN visits.status = 'checked_out' THEN 1 ELSE 0 END) as checked_out")
            ->selectRaw("SUM(CASE WHEN visits.status = 'rejected' THEN 1 ELSE 0 END) as rejected")
            ->selectRaw("SUM(CASE WHEN visits.status = 'pending' THEN 1 ELSE 0 END) as pending")
            ->groupBy('department')
            ->orderByDesc('total')
            ->get();

        return response()->json([
            'data' => $rows,
            'filters' => $filters,
            'updated_at' => now()->toIso8601String(),
        ]);
    }

    public function reportsByHost(Request $request): JsonResponse
    {
        $filters = $this->reportFilters($request);

        $rows = $this->filteredVisitsQuery($filters)
            ->leftJoin('employees', 'visits.host_employee_id', '=', 'employees.id')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->selectRaw('employees.id as host_id')
            ->selectRaw('COALESCE(employees.name, "No host") as host')
            ->selectRaw('COALESCE(departments.name, "No department") as department')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN visits.status = 'checked_in' THEN 1 ELSE 0 END) as checked_in")
            ->selectRaw("SUM(CASE WHEN visits.status = 'checked_out' THEN 1 ELSE 0 END) as checked_out")
            ->selectRaw("SUM(CASE WHEN visits.status = 'rejected' THEN 1 ELSE 0 END) as rejected")
            ->groupBy('host_id', 'host', 'department')
            ->orderByDesc('total')
            ->get();

        return response()->json([
            'data' => $rows,
            'filters' => $filters,
            'updated_at' => now()->toIso8601String(),
        ]);
    }

    public function reportsCurrentVisitors(): JsonResponse
    {
        $visits = $this->baseVisitQuery()
            ->where('status', 'checked_in')
            ->orderBy('actual_checkin_at')
            ->get();

        return response()->json([
            'data' => $this->mapReportVisits($visits),
            'updated_at' => now()->toIso8601String(),
        ]);
    }

    public function reportsOverstay(): JsonResponse
    {
        $visits = $this->baseVisitQuery()
            ->where('status', 'checked_in')
            ->whereNotNull('expected_checkout_at')
            ->where('expected_checkout_at', '<', now())
            ->orderBy('expected_checkout_at')
            ->get();

        return response()->json([
            'data' => $this->mapReportVisits($visits),
            'updated_at' => now()->toIso8601String(),
        ]);
    }

    public function reportsRejected(Request $request): JsonResponse
    {
        $filters = $this->reportFilters($request);
        $filters['status'] = 'rejected';

        $visits = $this->filteredVisitsQuery($filters)
            ->orderByDesc('updated_at')
            ->limit(300)
            ->get();

        return response()->json([
            'data' => $this->mapReportVisits($visits),
            'filters' => $filters,
            'updated_at' => now()->toIso8601String(),
        ]);
    }

    public function reportsEmergencyCurrentVisitors(): JsonResponse
    {
        $visits = $this->baseVisitQuery()
            ->where('status', 'checked_in')
            ->orderBy('actual_checkin_at')
            ->get();

        return response()->json([
            'generated_at' => now()->toIso8601String(),
            'total' => $visits->count(),
            'data' => $visits->map(fn (Visit $visit): array => [
                'visit_code' => $visit->code,
                'visitor' => $visit->visitor?->full_name ?? '-',
                'visitor_phone' => $visit->visitor?->phone,
                'company' => $visit->visitor?->company,
                'host' => $visit->hostEmployee?->name ?? '-',
                'department' => $visit->hostEmployee?->department?->name ?? '-',
                'access_zone' => $visit->access_zone,
                'badge_no' => $visit->activeBadge?->badge_no,
                'checkin_at' => $visit->actual_checkin_at?->toIso8601String(),
                'expected_checkout_at' => $visit->expected_checkout_at?->toIso8601String(),
            ])->all(),
        ]);
    }

    public function exportReportCsv(Request $request): StreamedResponse
    {
        $validated = $request->validate([
            'type' => ['nullable', 'in:visits,by-department,by-host,current-visitors,overstay,rejected,emergency-current-visitors'],
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date'],
            'status' => ['nullable', 'in:all,pending,approved,rejected,checked_in,checked_out,cancelled'],
        ]);

        $type = $validated['type'] ?? 'visits';
        $filters = [
            'from_date' => $validated['from_date'] ?? now()->startOfMonth()->toDateString(),
            'to_date' => $validated['to_date'] ?? now()->toDateString(),
            'status' => $validated['status'] ?? 'all',
        ];

        $rows = $this->reportRowsForExport($type, $filters);
        $fileName = 'vms-'.$type.'-'.now()->format('Ymd-His').'.csv';

        $this->logAudit('report.export_csv', 'report', $type, [
            'type' => $type,
            'filters' => $filters,
            'total_rows' => count($rows),
        ]);

        return response()->streamDownload(function () use ($rows): void {
            $handle = fopen('php://output', 'w');
            if ($handle === false) {
                return;
            }

            if ($rows === []) {
                fputcsv($handle, ['No data']);
                fclose($handle);

                return;
            }

            fputcsv($handle, array_keys($rows[0]));
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function exportReportXlsx(Request $request): BinaryFileResponse
    {
        $validated = $request->validate([
            'type' => ['nullable', 'in:visits,by-department,by-host,current-visitors,overstay,rejected,emergency-current-visitors'],
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date'],
            'status' => ['nullable', 'in:all,pending,approved,rejected,checked_in,checked_out,cancelled'],
        ]);

        $type = $validated['type'] ?? 'visits';
        $filters = [
            'from_date' => $validated['from_date'] ?? now()->startOfMonth()->toDateString(),
            'to_date' => $validated['to_date'] ?? now()->toDateString(),
            'status' => $validated['status'] ?? 'all',
        ];

        $rows = $this->reportRowsForExport($type, $filters);
        $fileName = 'vms-'.$type.'-'.now()->format('Ymd-His').'.xlsx';
        $filePath = $this->makeReportXlsx($rows, $type);

        $this->logAudit('report.export_xlsx', 'report', $type, [
            'type' => $type,
            'filters' => $filters,
            'total_rows' => count($rows),
        ]);

        return response()
            ->download($filePath, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])
            ->deleteFileAfterSend(true);
    }

    public function exportVisitsCsv(Request $request): StreamedResponse
    {
        $validated = $request->validate([
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date'],
            'status' => ['nullable', 'in:all,pending,approved,rejected,checked_in,checked_out,cancelled'],
        ]);

        $from = Carbon::parse($validated['from_date'] ?? now()->startOfMonth()->toDateString())->startOfDay();
        $to = Carbon::parse($validated['to_date'] ?? now()->toDateString())->endOfDay();
        $status = $validated['status'] ?? 'all';

        $query = $this->baseVisitQuery()
            ->whereBetween('scheduled_at', [$from, $to])
            ->orderBy('scheduled_at');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $visits = $query->get();
        $fileName = 'visits-report-'.$from->format('Ymd').'-'.$to->format('Ymd').'.csv';

        $this->logAudit('report.export_csv', 'report', 'visits', [
            'from_date' => $from->toDateString(),
            'to_date' => $to->toDateString(),
            'status' => $status,
            'total_rows' => $visits->count(),
        ]);

        return response()->streamDownload(function () use ($visits): void {
            $handle = fopen('php://output', 'w');
            if ($handle === false) {
                return;
            }

            fputcsv($handle, [
                'Mã lịch',
                'Thời gian hẹn',
                'Khách',
                'Người tiếp',
                'Phòng ban',
                'Mục đích',
                'Trạng thái',
                'Thời gian vào',
                'Thời gian ra',
            ]);

            $statusLabels = [
                'pending' => 'Chờ duyệt',
                'approved' => 'Đã duyệt',
                'rejected' => 'Từ chối',
                'checked_in' => 'Đang trong công ty',
                'checked_out' => 'Đã rời công ty',
                'cancelled' => 'Đã hủy',
            ];

            foreach ($visits as $visit) {
                fputcsv($handle, [
                    $visit->code,
                    $visit->scheduled_at?->format('Y-m-d H:i:s'),
                    $visit->visitor?->full_name,
                    $visit->hostEmployee?->name,
                    $visit->hostEmployee?->department?->name,
                    $visit->purpose,
                    $statusLabels[$visit->status] ?? $visit->status,
                    $visit->actual_checkin_at?->format('Y-m-d H:i:s'),
                    $visit->actual_checkout_at?->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * @return array{from_date: string, to_date: string, status: string}
     */
    private function reportFilters(Request $request): array
    {
        $validated = $request->validate([
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date'],
            'status' => ['nullable', 'in:all,pending,approved,rejected,checked_in,checked_out,cancelled'],
        ]);

        return [
            'from_date' => $validated['from_date'] ?? now()->startOfMonth()->toDateString(),
            'to_date' => $validated['to_date'] ?? now()->toDateString(),
            'status' => $validated['status'] ?? 'all',
        ];
    }

    /**
     * @param  array{from_date: string, to_date: string, status: string}  $filters
     */
    private function filteredVisitsQuery(array $filters): Builder
    {
        $from = Carbon::parse($filters['from_date'])->startOfDay();
        $to = Carbon::parse($filters['to_date'])->endOfDay();

        $query = $this->baseVisitQuery()
            ->whereBetween('scheduled_at', [$from, $to]);

        if ($filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        return $query;
    }

    /**
     * @param  Collection<int, Visit>  $visits
     * @return array<int, array<string, mixed>>
     */
    private function mapReportVisits(Collection $visits): array
    {
        return $visits->map(fn (Visit $visit): array => [
            'id' => $visit->id,
            'visit_code' => $visit->code,
            'scheduled_at' => $visit->scheduled_at?->toIso8601String(),
            'visitor' => $visit->visitor?->full_name ?? '-',
            'visitor_phone' => $visit->visitor?->phone,
            'company' => $visit->visitor?->company,
            'host' => $visit->hostEmployee?->name ?? '-',
            'department' => $visit->hostEmployee?->department?->name ?? '-',
            'purpose' => $visit->purpose,
            'status' => $visit->status,
            'access_zone' => $visit->access_zone,
            'badge_no' => $visit->activeBadge?->badge_no,
            'actual_checkin_at' => $visit->actual_checkin_at?->toIso8601String(),
            'actual_checkout_at' => $visit->actual_checkout_at?->toIso8601String(),
            'expected_checkout_at' => $visit->expected_checkout_at?->toIso8601String(),
            'is_overstay' => $visit->status === 'checked_in'
                && $visit->expected_checkout_at !== null
                && $visit->expected_checkout_at->lt(now()),
            'rejection_reason' => $visit->rejection_reason,
        ])->all();
    }

    /**
     * @param  array{from_date: string, to_date: string, status: string}  $filters
     * @return array<int, array<string, mixed>>
     */
    private function reportRowsForExport(string $type, array $filters): array
    {
        if ($type === 'by-department') {
            return $this->filteredVisitsQuery($filters)
                ->leftJoin('employees', 'visits.host_employee_id', '=', 'employees.id')
                ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
                ->selectRaw('COALESCE(departments.name, "No department") as department')
                ->selectRaw('COUNT(*) as total')
                ->selectRaw("SUM(CASE WHEN visits.status = 'checked_in' THEN 1 ELSE 0 END) as checked_in")
                ->selectRaw("SUM(CASE WHEN visits.status = 'checked_out' THEN 1 ELSE 0 END) as checked_out")
                ->selectRaw("SUM(CASE WHEN visits.status = 'rejected' THEN 1 ELSE 0 END) as rejected")
                ->selectRaw("SUM(CASE WHEN visits.status = 'pending' THEN 1 ELSE 0 END) as pending")
                ->groupBy('department')
                ->orderByDesc('total')
                ->get()
                ->map(fn ($row): array => (array) $row->getAttributes())
                ->all();
        }

        if ($type === 'by-host') {
            return $this->filteredVisitsQuery($filters)
                ->leftJoin('employees', 'visits.host_employee_id', '=', 'employees.id')
                ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
                ->selectRaw('COALESCE(employees.name, "No host") as host')
                ->selectRaw('COALESCE(departments.name, "No department") as department')
                ->selectRaw('COUNT(*) as total')
                ->selectRaw("SUM(CASE WHEN visits.status = 'checked_in' THEN 1 ELSE 0 END) as checked_in")
                ->selectRaw("SUM(CASE WHEN visits.status = 'checked_out' THEN 1 ELSE 0 END) as checked_out")
                ->selectRaw("SUM(CASE WHEN visits.status = 'rejected' THEN 1 ELSE 0 END) as rejected")
                ->groupBy('host', 'department')
                ->orderByDesc('total')
                ->get()
                ->map(fn ($row): array => (array) $row->getAttributes())
                ->all();
        }

        $query = match ($type) {
            'current-visitors', 'emergency-current-visitors' => $this->baseVisitQuery()->where('status', 'checked_in')->orderBy('actual_checkin_at'),
            'overstay' => $this->baseVisitQuery()
                ->where('status', 'checked_in')
                ->whereNotNull('expected_checkout_at')
                ->where('expected_checkout_at', '<', now())
                ->orderBy('expected_checkout_at'),
            'rejected' => $this->filteredVisitsQuery(array_merge($filters, ['status' => 'rejected']))->orderByDesc('updated_at'),
            default => $this->filteredVisitsQuery($filters)->orderByDesc('scheduled_at'),
        };

        return collect($this->mapReportVisits($query->get()))
            ->map(fn (array $row): array => [
                'visit_code' => $row['visit_code'],
                'scheduled_at' => $row['scheduled_at'],
                'visitor' => $row['visitor'],
                'visitor_phone' => $row['visitor_phone'],
                'company' => $row['company'],
                'host' => $row['host'],
                'department' => $row['department'],
                'purpose' => $row['purpose'],
                'status' => $row['status'],
                'access_zone' => $row['access_zone'],
                'badge_no' => $row['badge_no'],
                'actual_checkin_at' => $row['actual_checkin_at'],
                'actual_checkout_at' => $row['actual_checkout_at'],
                'expected_checkout_at' => $row['expected_checkout_at'],
                'is_overstay' => $row['is_overstay'] ? 'yes' : 'no',
                'rejection_reason' => $row['rejection_reason'],
            ])
            ->all();
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    private function makeReportXlsx(array $rows, string $sheetName): string
    {
        $filePath = tempnam(storage_path('app'), 'vms-report-');
        if ($filePath === false) {
            abort(500, 'Khong the tao file XLSX tam.');
        }

        $zip = new ZipArchive();
        if ($zip->open($filePath, ZipArchive::OVERWRITE) !== true) {
            abort(500, 'Khong the tao workbook XLSX.');
        }

        $zip->addFromString('[Content_Types].xml', $this->xlsxContentTypesXml());
        $zip->addFromString('_rels/.rels', $this->xlsxRootRelsXml());
        $zip->addFromString('xl/workbook.xml', $this->xlsxWorkbookXml($sheetName));
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->xlsxWorkbookRelsXml());
        $zip->addFromString('xl/styles.xml', $this->xlsxStylesXml());
        $zip->addFromString('xl/worksheets/sheet1.xml', $this->xlsxWorksheetXml($rows));
        $zip->close();

        return $filePath;
    }

    private function xlsxContentTypesXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
  <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
</Types>
XML;
    }

    private function xlsxRootRelsXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>
XML;
    }

    private function xlsxWorkbookXml(string $sheetName): string
    {
        $safeSheetName = $this->xlsxEscape(substr(str_replace([':', '\\', '/', '?', '*', '[', ']'], '-', $sheetName), 0, 31));

        return <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheets>
    <sheet name="{$safeSheetName}" sheetId="1" r:id="rId1"/>
  </sheets>
</workbook>
XML;
    }

    private function xlsxWorkbookRelsXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
</Relationships>
XML;
    }

    private function xlsxStylesXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <fonts count="2"><font><sz val="11"/><name val="Calibri"/></font><font><b/><sz val="11"/><name val="Calibri"/></font></fonts>
  <fills count="1"><fill><patternFill patternType="none"/></fill></fills>
  <borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>
  <cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>
  <cellXfs count="2"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/><xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0"/></cellXfs>
</styleSheet>
XML;
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    private function xlsxWorksheetXml(array $rows): string
    {
        $headers = $rows === [] ? ['No data'] : array_keys($rows[0]);
        $allRows = [$headers];
        foreach ($rows as $row) {
            $allRows[] = array_values($row);
        }

        $xmlRows = [];
        foreach ($allRows as $rowIndex => $row) {
            $excelRow = $rowIndex + 1;
            $cells = [];
            foreach ($row as $columnIndex => $value) {
                $cellRef = $this->xlsxColumnName($columnIndex + 1).$excelRow;
                $style = $excelRow === 1 ? ' s="1"' : '';
                $cells[] = '<c r="'.$cellRef.'" t="inlineStr"'.$style.'><is><t>'.$this->xlsxEscape($this->xlsxCellValue($value)).'</t></is></c>';
            }

            $xmlRows[] = '<row r="'.$excelRow.'">'.implode('', $cells).'</row>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<sheetData>'.implode('', $xmlRows).'</sheetData>'
            .'</worksheet>';
    }

    private function xlsxColumnName(int $index): string
    {
        $name = '';
        while ($index > 0) {
            $index--;
            $name = chr(65 + ($index % 26)).$name;
            $index = intdiv($index, 26);
        }

        return $name;
    }

    private function xlsxCellValue(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        if (is_bool($value)) {
            return $value ? 'yes' : 'no';
        }

        return (string) $value;
    }

    private function xlsxEscape(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_COMPAT, 'UTF-8');
    }

    /**
     * @param  Collection<int, Visit>  $visits
     * @return array<int, array<string, mixed>>
     */
    private function mapVisitRows(Collection $visits): array
    {
        return $visits->map(function (Visit $visit): array {
            return [
                'id' => $visit->id,
                'code' => $visit->code,
                'visitor' => $visit->visitor?->full_name ?? '-',
                'host' => $visit->hostEmployee?->name ?? '-',
                'department' => $visit->hostEmployee?->department?->name ?? '-',
                'time' => $visit->scheduled_at?->format('H:i') ?? '-',
                'date' => $visit->scheduled_at?->format('d/m/Y') ?? '-',
                'status' => $visit->status,
                'purpose' => $visit->purpose,
            ];
        })->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function dashboardSummaryData(): array
    {
        $today = now()->toDateString();

        return [
            'date' => $today,
            'today_visits' => Visit::query()->whereDate('scheduled_at', $today)->count(),
            'in_company' => Visit::query()->where('status', 'checked_in')->count(),
            'pending' => Visit::query()->where('status', 'pending')->count(),
            'approved' => Visit::query()->where('status', 'approved')->count(),
            'checked_out_today' => Visit::query()
                ->where('status', 'checked_out')
                ->whereDate('actual_checkout_at', $today)
                ->count(),
            'rejected_today' => Visit::query()
                ->where('status', 'rejected')
                ->whereDate('updated_at', $today)
                ->count(),
            'overstay' => Visit::query()
                ->where('status', 'checked_in')
                ->whereNotNull('expected_checkout_at')
                ->where('expected_checkout_at', '<', now())
                ->count(),
            'updated_at' => now()->toIso8601String(),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function liveVisitorsData(): array
    {
        return $this->baseVisitQuery()
            ->where('status', 'checked_in')
            ->orderBy('actual_checkin_at')
            ->get()
            ->map(function (Visit $visit): array {
                $checkinAt = $visit->actual_checkin_at ?? $visit->scheduled_at;
                $expectedCheckoutAt = $visit->expected_checkout_at;

                return [
                    'id' => $visit->id,
                    'code' => $visit->code,
                    'visitor' => $visit->visitor?->full_name ?? '-',
                    'visitor_phone' => $visit->visitor?->phone,
                    'company' => $visit->visitor?->company,
                    'host' => $visit->hostEmployee?->name ?? '-',
                    'department' => $visit->hostEmployee?->department?->name ?? '-',
                    'purpose' => $visit->purpose,
                    'access_zone' => $visit->access_zone,
                    'badge_no' => $visit->activeBadge?->badge_no,
                    'actual_checkin_at' => $checkinAt?->toIso8601String(),
                    'expected_checkout_at' => $expectedCheckoutAt?->toIso8601String(),
                    'duration_minutes' => $checkinAt !== null ? (int) $checkinAt->diffInMinutes(now()) : null,
                    'is_overstay' => $expectedCheckoutAt !== null && $expectedCheckoutAt->lt(now()),
                    'overstay_minutes' => $expectedCheckoutAt !== null && $expectedCheckoutAt->lt(now())
                        ? (int) $expectedCheckoutAt->diffInMinutes(now())
                        : 0,
                ];
            })
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function dashboardAlertsData(): array
    {
        $visits = $this->baseVisitQuery()
            ->whereIn('status', ['pending', 'approved', 'checked_in'])
            ->orderBy('scheduled_at')
            ->get();

        return $this->buildOperationalAlerts($visits);
    }

    /**
     * @return array<int, string>
     */
    private function dashboardAlertTexts(): array
    {
        $alerts = collect($this->dashboardAlertsData())
            ->take(5)
            ->map(fn (array $alert): string => "{$alert['title']} - {$alert['message']}")
            ->all();

        if ($alerts === []) {
            $alerts[] = 'Khong co canh bao bat thuong trong ca truc hien tai.';
        }

        return $alerts;
    }

    private function todayVisitsForDashboard(): Builder
    {
        return $this->baseVisitQuery()
            ->whereDate('scheduled_at', now()->toDateString())
            ->orderBy('scheduled_at');
    }

    private function baseVisitQuery(): Builder
    {
        return Visit::query()->with(['visitor', 'hostEmployee.department', 'activeBadge']);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function hostsForSelect(): array
    {
        return Employee::query()
            ->with('department')
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn (Employee $employee): array => [
                'id' => $employee->id,
                'name' => $employee->name,
                'department' => $employee->department->name ?? 'No department',
            ])
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private function accessZones(): array
    {
        return [
            'Tang 1 - Le tan',
            'Tang 2 - Van phong kinh doanh',
            'Tang 3 - Khu ky thuat',
            'Phong hop Skyline',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function validateVisitPayload(Request $request): array
    {
        return $request->validate([
            'visitor_name' => ['required', 'string', 'max:120'],
            'visitor_phone' => ['nullable', 'string', 'max:30'],
            'visitor_email' => ['nullable', 'email', 'max:160'],
            'visitor_company' => ['nullable', 'string', 'max:160'],
            'visitor_note' => ['nullable', 'string', 'max:1000'],
            'host_employee_id' => ['required', 'exists:employees,id'],
            'visit_date' => ['required', 'date'],
            'visit_time' => ['required', 'date_format:H:i'],
            'expected_checkout_time' => ['required', 'date_format:H:i'],
            'purpose' => ['required', 'string', 'max:255'],
            'access_zone' => ['nullable', 'string', 'max:120'],
            'checkin_method' => ['required', 'in:qr,badge,manual'],
        ]);
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array{0: Carbon, 1: Carbon}
     */
    private function parseVisitTimes(array $validated): array
    {
        return [
            Carbon::createFromFormat('Y-m-d H:i', $validated['visit_date'].' '.$validated['visit_time']),
            Carbon::createFromFormat('Y-m-d H:i', $validated['visit_date'].' '.$validated['expected_checkout_time']),
        ];
    }

    private function scopeVisitsForDepartmentManager(Builder $query): Builder
    {
        /** @var User|null $user */
        $user = auth()->user();
        if ($user === null || ! $user->roles()->where('slug', 'department_manager')->exists()) {
            return $query;
        }

        $departmentId = $user->employeeProfile?->department_id;
        if ($departmentId === null) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereHas('hostEmployee', fn (Builder $employeeQuery): Builder => $employeeQuery->where('department_id', $departmentId));
    }

    private function canActOnVisit(Visit $visit): bool
    {
        /** @var User|null $user */
        $user = auth()->user();
        if ($user === null || ! $user->roles()->where('slug', 'department_manager')->exists()) {
            return true;
        }

        $visit->loadMissing('hostEmployee');

        return $user->employeeProfile?->department_id === $visit->hostEmployee?->department_id;
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function firstOrCreateVisitor(array $validated): Visitor
    {
        $visitor = null;

        if (! empty($validated['visitor_phone'])) {
            $visitor = Visitor::query()->where('phone', $validated['visitor_phone'])->first();
        }

        if ($visitor === null && ! empty($validated['visitor_email'])) {
            $visitor = Visitor::query()->where('email', $validated['visitor_email'])->first();
        }

        if ($visitor === null) {
            return Visitor::query()->create([
                'full_name' => $validated['visitor_name'],
                'phone' => $validated['visitor_phone'] ?? null,
                'email' => $validated['visitor_email'] ?? null,
                'company' => $validated['visitor_company'] ?? null,
                'note' => $validated['visitor_note'] ?? null,
            ]);
        }

        $visitor->update([
            'full_name' => $validated['visitor_name'],
            'phone' => $validated['visitor_phone'] ?? null,
            'email' => $validated['visitor_email'] ?? null,
            'company' => $validated['visitor_company'] ?? null,
            'note' => $validated['visitor_note'] ?? null,
        ]);

        return $visitor;
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function updateVisitVisitor(Visit $visit, array $validated): Visitor
    {
        $visit->loadMissing('visitor');

        $visitor = $visit->visitor;
        if ($visitor === null) {
            $visitor = Visitor::query()->create([
                'full_name' => $validated['visitor_name'],
            ]);
        }

        $visitor->update([
            'full_name' => $validated['visitor_name'],
            'phone' => $validated['visitor_phone'] ?? null,
            'email' => $validated['visitor_email'] ?? null,
            'company' => $validated['visitor_company'] ?? null,
            'note' => $validated['visitor_note'] ?? null,
        ]);

        return $visitor;
    }

    private function generateVisitCode(): string
    {
        $prefix = 'VO-'.now()->format('ymd');
        $lastCode = Visit::query()
            ->where('code', 'like', $prefix.'-%')
            ->orderByDesc('code')
            ->value('code');

        $next = 1;
        if (is_string($lastCode)) {
            $parts = explode('-', $lastCode);
            $next = (int) end($parts) + 1;
        }

        do {
            $code = sprintf('%s-%03d', $prefix, $next);
            $next++;
        } while (Visit::query()->where('code', $code)->exists());

        return $code;
    }

    private function generateQrToken(): string
    {
        do {
            $token = 'vms-'.Str::lower(Str::random(20));
        } while (Visit::query()->where('qr_token', $token)->exists());

        return $token;
    }

    private function visitStatusLabel(?string $status): string
    {
        return match ($status) {
            'pending' => 'Chờ duyệt',
            'approved' => 'Đã duyệt',
            'rejected' => 'Từ chối',
            'checked_in' => 'Đang trong công ty',
            'checked_out' => 'Đã rời công ty',
            'cancelled' => 'Đã hủy',
            'waiting' => 'Yêu cầu chờ',
            default => $status ?: 'Không xác định',
        };
    }

    private function humanDuration(Carbon $from, Carbon $to): string
    {
        $minutes = max(0, (int) $from->diffInMinutes($to));

        if ($minutes < 60) {
            return $minutes.' phút';
        }

        $hours = intdiv($minutes, 60);
        $remainingMinutes = $minutes % 60;

        if ($remainingMinutes === 0) {
            return $hours.' giờ';
        }

        return $hours.' giờ '.$remainingMinutes.' phút';
    }

    private function performCheckin(Visit $visit): ?string
    {
        if ($visit->status === 'checked_in') {
            return "Lịch {$visit->code} đã được check-in trước đó.";
        }

        if ($visit->status !== 'approved') {
            return match ($visit->status) {
                'pending' => "Lịch {$visit->code} đang chờ duyệt, chưa thể check-in.",
                'checked_out' => "Lịch {$visit->code} đã check-out, không thể check-in lại.",
                'rejected' => "Lịch {$visit->code} đã bị từ chối, không thể check-in.",
                'cancelled' => "Lịch {$visit->code} đã hủy, không thể check-in.",
                default => "Không thể check-in lịch {$visit->code} ở trạng thái {$this->visitStatusLabel($visit->status)}.",
            };
        }

        $visit->update([
            'status' => 'checked_in',
            'actual_checkin_at' => $visit->actual_checkin_at ?? now(),
        ]);

        $badge = $this->issueBadgeForVisit($visit);

        AccessControlLog::query()->create([
            'visit_id' => $visit->id,
            'badge_id' => $badge?->id,
            'event' => 'CHECK_IN',
            'source' => 'web',
            'meta' => [
                'visit_code' => $visit->code,
                'badge_no' => $badge?->badge_no,
            ],
        ]);

        return null;
    }

    private function performCheckout(Visit $visit): ?string
    {
        if ($visit->status !== 'checked_in') {
            return match ($visit->status) {
                'pending' => "Lịch {$visit->code} đang chờ duyệt, khách chưa được check-in.",
                'approved' => "Lịch {$visit->code} đã duyệt nhưng chưa check-in, không thể check-out.",
                'checked_out' => "Lịch {$visit->code} đã được check-out trước đó.",
                'rejected' => "Lịch {$visit->code} đã bị từ chối, không thể check-out.",
                'cancelled' => "Lịch {$visit->code} đã hủy, không thể check-out.",
                default => "Chỉ khách đang trong công ty mới được check-out. Trạng thái hiện tại: {$this->visitStatusLabel($visit->status)}.",
            };
        }

        $visit->update([
            'status' => 'checked_out',
            'actual_checkout_at' => now(),
        ]);

        $badge = Badge::query()
            ->where('visit_id', $visit->id)
            ->where('status', 'active')
            ->first();

        if ($badge !== null) {
            $badge->update([
                'status' => 'revoked',
                'revoked_at' => now(),
            ]);
        }

        AccessControlLog::query()->create([
            'visit_id' => $visit->id,
            'badge_id' => $badge?->id,
            'event' => 'CHECK_OUT',
            'source' => 'web',
            'meta' => [
                'visit_code' => $visit->code,
                'badge_no' => $badge?->badge_no,
            ],
        ]);

        return null;
    }

    private function issueBadgeForVisit(Visit $visit): ?Badge
    {
        $existingBadge = Badge::query()
            ->where('visit_id', $visit->id)
            ->where('status', 'active')
            ->first();

        if ($existingBadge !== null) {
            return $existingBadge;
        }

        $badge = Badge::query()
            ->where('status', 'available')
            ->orderBy('badge_no')
            ->first();

        if ($badge === null) {
            return null;
        }

        $badge->update([
            'visit_id' => $visit->id,
            'status' => 'active',
            'issued_at' => now(),
            'revoked_at' => null,
            'valid_until' => $visit->expected_checkout_at,
        ]);

        return $badge;
    }

    /**
     * @param  Collection<int, Visit>  $visits
     * @return array<int, array<string, mixed>>
     */
    private function buildOperationalAlerts(Collection $visits): array
    {
        $watchlistAlerts = Watchlist::query()
            ->where('status', 'active')
            ->orderByDesc('id')
            ->limit(10)
            ->get()
            ->flatMap(function (Watchlist $watchlist): array {
                return $this->visitsMatchingWatchlist($watchlist)
                    ->take(5)
                    ->map(fn (Visit $visit): array => [
                        'type' => 'watchlist_match',
                        'level' => $watchlist->level === 'critical' ? 'danger' : 'warning',
                        'title' => "Watchlist: {$visit->code}",
                        'message' => ($visit->visitor?->full_name ?? 'Khach').' match rule '.$watchlist->keyword.'.',
                        'time' => $visit->scheduled_at?->format('H:i') ?? '-',
                        'visit_id' => $visit->id,
                        'visit_code' => $visit->code,
                        'status' => $visit->status,
                    ])
                    ->all();
            });

        $operationalAlerts = $visits->map(function (Visit $visit): ?array {
            if ($visit->status === 'checked_in' && $visit->expected_checkout_at?->lt(now())) {
                return [
                    'type' => 'overstay',
                    'level' => 'danger',
                    'title' => "Qua gio: {$visit->code}",
                    'message' => ($visit->visitor?->full_name ?? 'Khach').' chua check-out sau gio du kien.',
                    'time' => $visit->expected_checkout_at->format('H:i'),
                    'visit_id' => $visit->id,
                    'visit_code' => $visit->code,
                    'status' => $visit->status,
                ];
            }

            if ($visit->status === 'approved' && $visit->scheduled_at?->lt(now()->subMinutes(30))) {
                return [
                    'type' => 'approved_not_checked_in',
                    'level' => 'warning',
                    'title' => "Chua check-in: {$visit->code}",
                    'message' => ($visit->visitor?->full_name ?? 'Khach').' da duyet nhung chua check-in.',
                    'time' => $visit->scheduled_at->format('H:i'),
                    'visit_id' => $visit->id,
                    'visit_code' => $visit->code,
                    'status' => $visit->status,
                ];
            }

            if ($visit->status === 'pending' && $visit->scheduled_at?->lt(now()->addHour())) {
                return [
                    'type' => 'pending_near_schedule',
                    'level' => 'warning',
                    'title' => "Sap den gio: {$visit->code}",
                    'message' => 'Lich hen sap den gio nhung van cho phe duyet.',
                    'time' => $visit->scheduled_at->format('H:i'),
                    'visit_id' => $visit->id,
                    'visit_code' => $visit->code,
                    'status' => $visit->status,
                ];
            }

            return null;
        })->filter();

        return $watchlistAlerts
            ->merge($operationalAlerts)
            ->values()
            ->all();
    }

    /**
     * @return array<string, string>
     */
    private function watchlistMatchTypes(): array
    {
        return [
            'any' => 'Bat ky thong tin nao',
            'name' => 'Ten khach',
            'phone' => 'So dien thoai',
            'email' => 'Email',
            'company' => 'Cong ty',
            'identity_no' => 'Giay to/CCCD',
        ];
    }

    /**
     * @return array<string, string>
     */
    private function watchlistLevels(): array
    {
        return [
            'warning' => 'Canh bao',
            'critical' => 'Nghiem trong',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function validateWatchlistPayload(Request $request): array
    {
        return $request->validate([
            'visitor_id' => ['nullable', 'exists:visitors,id'],
            'keyword' => ['required', 'string', 'max:160'],
            'match_type' => ['required', 'in:any,name,phone,email,company,identity_no'],
            'level' => ['required', 'in:warning,critical'],
            'status' => ['required', 'in:active,inactive'],
            'reason' => ['required', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:2000'],
        ]);
    }

    /**
     * @return Collection<int, Visit>
     */
    private function visitsMatchingWatchlist(Watchlist $watchlist): Collection
    {
        return $this->baseVisitQuery()
            ->whereHas('visitor', fn (Builder $query): Builder => $this->applyWatchlistVisitorMatch($query, $watchlist))
            ->orderByDesc('scheduled_at')
            ->limit(50)
            ->get();
    }

    private function applyWatchlistVisitorMatch(Builder $query, Watchlist $watchlist): Builder
    {
        if ($watchlist->visitor_id !== null) {
            return $query->where('id', $watchlist->visitor_id);
        }

        $keyword = '%'.$watchlist->keyword.'%';

        return match ($watchlist->match_type) {
            'name' => $query->where('full_name', 'like', $keyword),
            'phone' => $query->where('phone', 'like', $keyword),
            'email' => $query->where('email', 'like', $keyword),
            'company' => $query->where('company', 'like', $keyword),
            'identity_no' => $query->where('identity_no', 'like', $keyword),
            default => $query->where(function (Builder $builder) use ($keyword): void {
                $builder
                    ->where('full_name', 'like', $keyword)
                    ->orWhere('phone', 'like', $keyword)
                    ->orWhere('email', 'like', $keyword)
                    ->orWhere('company', 'like', $keyword)
                    ->orWhere('identity_no', 'like', $keyword);
            }),
        };
    }

    private function scanWatchlistForVisit(Visit $visit, string $eventType): void
    {
        $visit->loadMissing('visitor');
        if ($visit->visitor === null) {
            return;
        }

        $matches = Watchlist::query()
            ->where('status', 'active')
            ->get()
            ->filter(fn (Watchlist $watchlist): bool => $this->visitorMatchesWatchlist($visit->visitor, $watchlist));

        foreach ($matches as $watchlist) {
            $this->notifyUsersWithPermission(
                'alerts.view',
                'watchlist.match',
                'Khach match watchlist',
                "Lich {$visit->code} match watchlist '{$watchlist->keyword}'. Ly do: {$watchlist->reason}",
                $watchlist->level === 'critical' ? 'danger' : 'warning',
                $visit
            );

            $this->logAudit('watchlist.matched', 'visit', (string) $visit->id, [
                'event_type' => $eventType,
                'watchlist_id' => $watchlist->id,
                'keyword' => $watchlist->keyword,
                'level' => $watchlist->level,
            ]);
        }
    }

    private function visitorMatchesWatchlist(Visitor $visitor, Watchlist $watchlist): bool
    {
        if ($watchlist->visitor_id !== null) {
            return (int) $watchlist->visitor_id === (int) $visitor->id;
        }

        $needle = Str::lower($watchlist->keyword);
        $values = match ($watchlist->match_type) {
            'name' => [$visitor->full_name],
            'phone' => [$visitor->phone],
            'email' => [$visitor->email],
            'company' => [$visitor->company],
            'identity_no' => [$visitor->identity_no],
            default => [$visitor->full_name, $visitor->phone, $visitor->email, $visitor->company, $visitor->identity_no],
        };

        foreach ($values as $value) {
            if (is_string($value) && str_contains(Str::lower($value), $needle)) {
                return true;
            }
        }

        return false;
    }

    private function unreadNotificationCount(?int $userId = null): int
    {
        $targetUserId = $userId ?? auth()->id();
        if ($targetUserId === null) {
            return 0;
        }

        return Notification::query()
            ->where('user_id', $targetUserId)
            ->whereNull('read_at')
            ->count();
    }

    private function notifyHost(Visit $visit, string $type, string $title, string $message, string $level = 'info'): void
    {
        $visit->loadMissing('hostEmployee.user');
        $user = $visit->hostEmployee?->user;

        if ($user === null) {
            return;
        }

        $this->createNotification($user, $type, $title, $message, $level, $visit);
    }

    private function notifyUsersWithPermission(
        string $permission,
        string $type,
        string $title,
        string $message,
        string $level,
        Visit $visit
    ): void {
        User::query()
            ->where('is_active', true)
            ->whereHas('roles.permissions', fn ($query) => $query->where('slug', $permission))
            ->get()
            ->each(function (User $user) use ($type, $title, $message, $level, $visit): void {
                $this->createNotification($user, $type, $title, $message, $level, $visit);
            });
    }

    private function createNotification(
        User $user,
        string $type,
        string $title,
        string $message,
        string $level,
        Visit $visit
    ): void {
        Notification::query()->create([
            'user_id' => $user->id,
            'type' => $type,
            'level' => $level,
            'title' => $title,
            'message' => $message,
            'entity_type' => 'visit',
            'entity_id' => (string) $visit->id,
            'action_url' => route('admin.visits.show', $visit),
            'data' => [
                'visit_code' => $visit->code,
                'status' => $visit->status,
            ],
        ]);
    }

    private function actingUserId(): ?int
    {
        $authId = auth()->id();
        if ($authId !== null) {
            return (int) $authId;
        }

        return User::query()->orderBy('id')->value('id');
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    private function logAudit(string $action, string $entityType, string $entityId, array $meta = []): void
    {
        AuditLog::query()->create([
            'user_id' => $this->actingUserId(),
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'meta' => $meta,
        ]);
    }
}


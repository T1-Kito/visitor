<?php

namespace App\Http\Controllers;

use App\Models\AccessControlLog;
use App\Models\Approval;
use App\Models\AuditLog;
use App\Models\Badge;
use App\Models\Employee;
use App\Models\Notification;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\Visit;
use App\Models\Visitor;
use App\Models\Watchlist;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class KioskController extends Controller
{
    public function index(): View
    {
        $lastVisit = null;
        $lastVisitId = session('kiosk_last_visit_id');

        if (is_numeric($lastVisitId)) {
            $lastVisit = Visit::query()
                ->with(['visitor', 'hostEmployee.department'])
                ->find((int) $lastVisitId);
        }

        return view('kiosk.index', [
            'kioskSettings' => SystemSetting::values(SystemSetting::kioskDefaults()),
            'lastKioskVisit' => $lastVisit,
        ]);
    }

    public function searchEmployees(Request $request): JsonResponse
    {
        $term = trim((string) $request->query('q', ''));

        if (mb_strlen($term) < 2) {
            return response()->json([
                'data' => [],
                'message' => 'Nhap it nhat 2 ky tu de tim nhan vien.',
            ]);
        }

        $employees = Employee::query()
            ->with('department')
            ->where('is_active', true)
            ->where(function ($query) use ($term): void {
                $query
                    ->where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%")
                    ->orWhere('job_title', 'like', "%{$term}%");
            })
            ->orderBy('name')
            ->limit(8)
            ->get()
            ->map(fn (Employee $employee): array => [
                'id' => $employee->id,
                'name' => $employee->name,
                'position' => $employee->job_title,
                'department' => $employee->department?->name,
            ]);

        return response()->json(['data' => $employees]);
    }

    public function storeWalkIn(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'visitor_name' => ['required', 'string', 'max:120'],
            'visitor_phone' => ['nullable', 'string', 'max:30'],
            'visitor_email' => ['nullable', 'email', 'max:160'],
            'visitor_company' => ['nullable', 'string', 'max:160'],
            'host_employee_id' => ['required', 'exists:employees,id'],
            'purpose' => ['required', 'string', 'max:255'],
            'expected_checkout_time' => ['nullable', 'date_format:H:i'],
            'visitor_note' => ['nullable', 'string', 'max:1000'],
            'policy_accepted' => ['accepted'],
        ]);

        $visitor = Visitor::query()->create([
            'full_name' => $validated['visitor_name'],
            'phone' => $validated['visitor_phone'] ?? null,
            'email' => $validated['visitor_email'] ?? null,
            'company' => $validated['visitor_company'] ?? null,
            'note' => $validated['visitor_note'] ?? null,
        ]);

        $scheduledAt = now();
        $expectedCheckoutAt = $scheduledAt->copy()->addHours(2);
        if (! empty($validated['expected_checkout_time'])) {
            $candidate = Carbon::createFromFormat('Y-m-d H:i', $scheduledAt->toDateString().' '.$validated['expected_checkout_time']);
            if ($candidate->greaterThan($scheduledAt)) {
                $expectedCheckoutAt = $candidate;
            }
        }

        $visit = Visit::query()->create([
            'code' => $this->generateVisitCode(),
            'visitor_id' => $visitor->id,
            'host_employee_id' => (int) $validated['host_employee_id'],
            'scheduled_at' => $scheduledAt,
            'expected_checkout_at' => $expectedCheckoutAt,
            'status' => 'pending',
            'purpose' => $validated['purpose'],
            'access_zone' => 'Tang 1 - Le tan',
            'checkin_method' => 'qr',
            'qr_token' => $this->generateQrToken(),
            'qr_expires_at' => $scheduledAt->copy()->addDay(),
        ]);

        Approval::query()->create([
            'visit_id' => $visit->id,
            'status' => 'pending',
        ]);

        AuditLog::query()->create([
            'user_id' => null,
            'action' => 'kiosk.walk_in_created',
            'entity_type' => 'visit',
            'entity_id' => (string) $visit->id,
            'meta' => ['code' => $visit->code],
        ]);
        $this->notifyHost($visit, 'kiosk.walk_in_created', 'Khach walk-in dang cho duyet', "Yeu cau walk-in {$visit->code} vua duoc tao tu kiosk.", 'warning');
        $this->notifyUsersWithPermission('approvals.manage', 'kiosk.walk_in_created', 'Co khach walk-in can duyet', "Yeu cau walk-in {$visit->code} dang cho phe duyet.", 'warning', $visit);
        $this->scanWatchlistForVisit($visit, 'kiosk.walk_in_created');

        $request->session()->put('kiosk_checkin_visit_id', $visit->id);
        $request->session()->put('kiosk_last_visit_id', $visit->id);

        return redirect()
            ->route('kiosk.checkin.status', $visit)
            ->with('status', 'Da gui yeu cau. Vui long cho nhan vien tiep khach phe duyet.');
    }

    public function scanQr(Request $request): RedirectResponse|JsonResponse
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
            if ($request->expectsJson()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Không tìm thấy lịch hẹn với mã vừa nhập.',
                ], 404);
            }

            return redirect()->back()->with('error', 'Không tìm thấy lịch hẹn với mã QR hoặc mã lịch hẹn này.');
        }

        $request->session()->put('kiosk_last_visit_id', $visit->id);

        if ($request->expectsJson()) {
            if ($scannedByQr && $visit->qr_expires_at !== null && $visit->qr_expires_at->lt(now())) {
                return response()->json([
                    'ok' => false,
                    'message' => 'QR đã hết hạn. Vui lòng liên hệ lễ tân để được hỗ trợ.',
                    'visit' => $this->kioskVisitPayload($visit, false),
                ], 422);
            }

            if ($visit->status === 'approved') {
                $request->session()->put('kiosk_checkin_visit_id', $visit->id);
                $request->session()->put('kiosk_scanned_by_qr', $scannedByQr);
            }

            return response()->json([
                'ok' => true,
                'message' => $visit->status === 'approved'
                    ? 'Mã hợp lệ. Khách có thể xác nhận check-in.'
                    : 'Đã tìm thấy lịch hẹn. Vui lòng kiểm tra trạng thái hiện tại.',
                'visit' => $this->kioskVisitPayload($visit, $visit->status === 'approved'),
            ]);
        }

        if ($visit->status !== 'approved') {
            return redirect()
                ->route('kiosk.checkin.status', $visit)
                ->with('error', 'Lịch hẹn chưa sẵn sàng check-in.');
        }

        if ($scannedByQr && $visit->qr_expires_at !== null && $visit->qr_expires_at->lt(now())) {
            return redirect()
                ->route('kiosk.checkin.status', $visit)
                ->with('error', 'QR đã hết hạn.');
        }

        $request->session()->put('kiosk_checkin_visit_id', $visit->id);
        $request->session()->put('kiosk_scanned_by_qr', $scannedByQr);
        return redirect()
            ->route('kiosk.checkin.status', $visit)
            ->with('status', 'Mã hợp lệ. Vui lòng kiểm tra thông tin và xác nhận check-in.');
    }

    public function confirmCheckin(Request $request, Visit $visit): RedirectResponse|JsonResponse
    {
        if ((int) $request->session()->get('kiosk_checkin_visit_id') !== $visit->id) {
            if ($request->expectsJson()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Vui lòng kiểm tra mã lịch hẹn trước khi xác nhận check-in.',
                ], 403);
            }

            return redirect()
                ->route('kiosk.index')
                ->with('error', 'Vui lòng nhập mã QR, mã lịch hẹn hoặc tạo yêu cầu từ kiosk trước khi xác nhận check-in.');
        }

        if ($visit->status === 'checked_in') {
            if ($request->expectsJson()) {
                return response()->json([
                    'ok' => true,
                    'message' => 'Khách đã check-in trước đó.',
                    'visit' => $this->kioskVisitPayload($visit, false),
                ]);
            }

            return redirect()
                ->route('kiosk.checkin.status', $visit)
                ->with('status', 'Khách đã check-in trước đó.');
        }

        if ($visit->status !== 'approved') {
            if ($request->expectsJson()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Lịch hẹn chưa được duyệt nên chưa thể check-in.',
                    'visit' => $this->kioskVisitPayload($visit, false),
                ], 422);
            }

            return redirect()
                ->route('kiosk.checkin.status', $visit)
                ->with('error', 'Lịch hẹn chưa được duyệt nên chưa thể check-in.');
        }

        if ($request->session()->get('kiosk_scanned_by_qr') && $visit->qr_expires_at !== null && $visit->qr_expires_at->lt(now())) {
            if ($request->expectsJson()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'QR đã hết hạn. Vui lòng liên hệ lễ tân để được hỗ trợ.',
                    'visit' => $this->kioskVisitPayload($visit, false),
                ], 422);
            }

            return redirect()
                ->route('kiosk.checkin.status', $visit)
                ->with('error', 'QR đã hết hạn. Vui lòng liên hệ lễ tân để được hỗ trợ.');
        }

        $visit->update([
            'status' => 'checked_in',
            'actual_checkin_at' => now(),
        ]);

        $badge = $this->issueBadgeForVisit($visit);

        AccessControlLog::query()->create([
            'visit_id' => $visit->id,
            'badge_id' => $badge?->id,
            'event' => 'CHECK_IN',
            'source' => 'kiosk',
            'meta' => ['badge_no' => $badge?->badge_no],
        ]);

        AuditLog::query()->create([
            'user_id' => null,
            'action' => 'kiosk.checked_in',
            'entity_type' => 'visit',
            'entity_id' => (string) $visit->id,
            'meta' => ['code' => $visit->code, 'badge_no' => $badge?->badge_no],
        ]);
        $this->notifyHost($visit, 'kiosk.checked_in', 'Khach da check-in tai kiosk', "Khach cua lich {$visit->code} da vao cong ty.", 'success');
        $this->notifyUsersWithPermission('alerts.view', 'kiosk.checked_in', 'Khach da vao cong ty', "Lich {$visit->code} da check-in tai kiosk.", 'info', $visit);
        $this->scanWatchlistForVisit($visit, 'kiosk.checked_in');

        $request->session()->forget(['kiosk_checkin_visit_id', 'kiosk_scanned_by_qr']);

        if ($request->expectsJson()) {
            $visit->refresh()->load(['visitor', 'hostEmployee.department']);

            return response()->json([
                'ok' => true,
                'message' => 'Check-in thành công. Vui lòng nhận badge tại quầy lễ tân.',
                'visit' => $this->kioskVisitPayload($visit, false),
            ]);
        }

        return redirect()
            ->route('kiosk.checkin.status', $visit)
            ->with('status', 'Check-in thành công. Vui lòng nhận badge tại quầy lễ tân.');
    }

    public function status(Request $request, Visit $visit): View
    {
        $visit->load(['visitor', 'hostEmployee.department', 'activeBadge']);

        return view('kiosk.status', [
            'visit' => $visit,
            'canConfirm' => (int) $request->session()->get('kiosk_checkin_visit_id') === $visit->id,
        ]);
    }

    private function issueBadgeForVisit(Visit $visit): ?Badge
    {
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

    private function generateVisitCode(): string
    {
        $prefix = 'WK-'.now()->format('ymd');
        $lastCode = Visit::query()
            ->where('code', 'like', $prefix.'-%')
            ->orderByDesc('code')
            ->value('code');

        $next = 1;
        if (is_string($lastCode)) {
            $parts = explode('-', $lastCode);
            $next = (int) end($parts) + 1;
        }

        return sprintf('%s-%03d', $prefix, $next);
    }

    private function generateQrToken(): string
    {
        do {
            $token = 'kiosk-'.Str::lower(Str::random(18));
        } while (Visit::query()->where('qr_token', $token)->exists());

        return $token;
    }

    private function kioskVisitPayload(Visit $visit, bool $canConfirm): array
    {
        $visit->loadMissing(['visitor', 'hostEmployee.department']);

        $statusLabels = [
            'pending' => 'Đang chờ phê duyệt',
            'approved' => 'Đã được duyệt',
            'checked_in' => 'Đã check-in',
            'checked_out' => 'Đã rời công ty',
            'rejected' => 'Bị từ chối',
            'cancelled' => 'Đã hủy',
        ];

        $statusHints = [
            'pending' => 'Vui lòng chờ người tiếp khách hoặc lễ tân xác nhận.',
            'approved' => 'Lịch hẹn đã được duyệt. Bạn có thể xác nhận check-in.',
            'checked_in' => 'Khách đã check-in trước đó.',
            'checked_out' => 'Lịch hẹn này đã hoàn tất check-out.',
            'rejected' => 'Yêu cầu đã bị từ chối. Vui lòng liên hệ lễ tân nếu cần hỗ trợ.',
            'cancelled' => 'Lịch hẹn đã bị hủy.',
        ];

        return [
            'id' => $visit->id,
            'code' => $visit->code,
            'visitor_name' => $visit->visitor?->full_name ?? '-',
            'visitor_company' => $visit->visitor?->company ?? '-',
            'host_name' => $visit->hostEmployee?->name ?? '-',
            'department' => $visit->hostEmployee?->department?->name ?? '-',
            'scheduled_at' => $visit->scheduled_at?->format('H:i - d/m/Y') ?? '-',
            'status' => $visit->status,
            'status_label' => $statusLabels[$visit->status] ?? $visit->status,
            'status_hint' => $statusHints[$visit->status] ?? 'Vui lòng liên hệ lễ tân để được hỗ trợ.',
            'can_confirm' => $canConfirm,
            'confirm_url' => $canConfirm ? route('kiosk.checkin.confirm', $visit) : null,
            'status_url' => route('kiosk.checkin.status', $visit),
        ];
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

            AuditLog::query()->create([
                'user_id' => null,
                'action' => 'watchlist.matched',
                'entity_type' => 'visit',
                'entity_id' => (string) $visit->id,
                'meta' => [
                    'event_type' => $eventType,
                    'watchlist_id' => $watchlist->id,
                    'keyword' => $watchlist->keyword,
                    'level' => $watchlist->level,
                ],
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
}

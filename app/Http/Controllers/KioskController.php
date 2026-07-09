<?php

namespace App\Http\Controllers;

use App\Models\AccessControlLog;
use App\Models\Approval;
use App\Models\AuditLog;
use App\Models\Badge;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Notification;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\Visit;
use App\Models\Visitor;
use App\Models\Watchlist;
use App\Support\DynamicMailSettings;
use App\Support\PublicRegistrationAccess;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class KioskController extends Controller
{
    public function index(): View
    {
        $lastVisit = null;
        $lastVisitId = session('kiosk_last_visit_id');

        if (is_numeric($lastVisitId)) {
            $lastVisit = Visit::query()
                ->with(['visitor', 'hostEmployee.department', 'department'])
                ->find((int) $lastVisitId);
        }

        return view('kiosk.index', [
            'kioskSettings' => SystemSetting::values(SystemSetting::kioskDefaults()),
            'lastKioskVisit' => $lastVisit,
            'departments' => Department::query()->orderBy('name')->get(['id', 'name']),
            'visitorCardOptions' => $this->visitorCardOptions(),
        ]);
    }

    public function register(): View
    {
        return view('kiosk.register', [
            'kioskSettings' => SystemSetting::values(SystemSetting::kioskDefaults()),
        ]);
    }

    public function privacyNotice(): RedirectResponse
    {
        return redirect()->away('https://www.dhl.com/global-en/home/footer/privacy-notice.html');
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
        $isKioskV2 = $request->input('registration_form') === 'kiosk_v2';
        $requiredForKiosk = $isKioskV2 ? 'required' : 'nullable';
        $visitorCardValues = array_values(array_unique(array_merge(
            $this->visitorCardOptions()->pluck('value')->map(fn ($value): string => (string) $value)->all(),
            array_map('strval', range(1, 20))
        )));

        $validated = $request->validate([
            'registration_form' => ['nullable', 'in:kiosk_v2'],
            'visitor_name' => ['required', 'string', 'max:120'],
            'visitor_phone' => [$requiredForKiosk, 'string', 'max:30'],
            'visitor_email' => ['nullable', 'email', 'max:160'],
            'visitor_company' => ['required', 'string', 'max:160'],
            'visitor_identity_no' => [$requiredForKiosk, 'string', 'max:80'],
            'visitor_id_card_number' => $isKioskV2
                ? ['required', 'string', Rule::in($visitorCardValues)]
                : ['nullable', 'string', 'max:80'],
            'visitor_identity_issued_place' => ['nullable', 'string', 'max:160'],
            'visitor_identity_issued_date' => ['nullable', 'date', 'before_or_equal:today'],
            'host_employee_id' => ['nullable', 'exists:employees,id'],
            'host_name' => ['required', 'string', 'max:120'],
            'department_id' => ['required', 'exists:departments,id'],
            'purpose' => ['required', 'string', 'max:255'],
            'checkin_date' => [$requiredForKiosk, 'date'],
            'checkin_time' => [$requiredForKiosk, 'date_format:H:i'],
            'checkout_date' => [$requiredForKiosk, 'date'],
            'checkout_time' => [$requiredForKiosk, 'date_format:H:i'],
            'expected_checkout_time' => ['nullable', 'date_format:H:i'],
            'policy_accepted' => ['accepted'],
            'safety_acknowledged' => ['accepted'],
        ]);

        $visitor = Visitor::query()->create([
            'full_name' => $validated['visitor_name'],
            'phone' => $validated['visitor_phone'] ?? null,
            'email' => $validated['visitor_email'] ?? null,
            'company' => $validated['visitor_company'] ?? null,
            'identity_no' => $validated['visitor_identity_no'] ?? null,
            'visitor_id_card_number' => $validated['visitor_id_card_number'] ?? null,
            'identity_issued_place' => $validated['visitor_identity_issued_place'] ?? null,
            'identity_issued_date' => $validated['visitor_identity_issued_date'] ?? null,
        ]);

        $scheduledAt = now();
        $expectedCheckoutAt = $scheduledAt->copy()->addHours(4);

        if ($isKioskV2) {
            $scheduledAt = Carbon::createFromFormat('Y-m-d H:i', $validated['checkin_date'].' '.$validated['checkin_time']);
            $expectedCheckoutAt = Carbon::createFromFormat('Y-m-d H:i', $validated['checkout_date'].' '.$validated['checkout_time']);

            if ($expectedCheckoutAt->lessThanOrEqualTo($scheduledAt)) {
                throw ValidationException::withMessages([
                    'checkout_time' => 'Check-out date and time must be after check-in date and time.',
                ]);
            }
        } elseif (! empty($validated['expected_checkout_time'])) {
            $candidate = Carbon::createFromFormat('Y-m-d H:i', $scheduledAt->toDateString().' '.$validated['expected_checkout_time']);
            if ($candidate->greaterThan($scheduledAt)) {
                $expectedCheckoutAt = $candidate;
            }
        }

        $visit = Visit::query()->create([
            'code' => $this->generateVisitCode(),
            'visitor_id' => $visitor->id,
            'host_employee_id' => filled($validated['host_employee_id'] ?? null) ? (int) $validated['host_employee_id'] : null,
            'host_name' => $validated['host_name'],
            'department_id' => (int) $validated['department_id'],
            'scheduled_at' => $scheduledAt,
            'expected_checkout_at' => $expectedCheckoutAt,
            'status' => 'pending',
            'purpose' => $validated['purpose'],
            'access_zone' => $this->defaultAccessZone(),
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
        $this->notifyHost($visit, 'kiosk.walk_in_created', 'Khách walk-in đang chờ bạn duyệt', "Yêu cầu walk-in {$visit->code} vừa được tạo từ kiosk.", 'warning');
        $this->notifyApprovalAdmins('kiosk.walk_in_created', 'Có khách walk-in cần duyệt', "Yêu cầu walk-in {$visit->code} đang chờ host duyệt.", 'warning', $visit);
        $this->scanWatchlistForVisit($visit, 'kiosk.walk_in_created');

        $request->session()->put('kiosk_checkin_visit_id', $visit->id);
        $request->session()->put('kiosk_last_visit_id', $visit->id);

        if (PublicRegistrationAccess::isPublicPortRequest($request)) {
            $request->session()->forget(['kiosk_checkin_visit_id', 'kiosk_last_visit_id']);

            return redirect()
                ->route('kiosk.register')
                ->with('status', 'Đã gửi yêu cầu đăng ký. Vui lòng chờ nhân viên phụ trách phê duyệt.');
        }

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

                return response()->json([
                    'ok' => true,
                    'message' => "Da tim thay lich {$visit->code}. Vui long xac nhan check-in.",
                    'visit' => $this->kioskVisitPayload($visit, true),
                ]);
            }

            return response()->json([
                'ok' => true,
                'message' => match ($visit->status) {
                    'pending' => "Lịch {$visit->code} chưa được duyệt. Vui lòng chờ lễ tân hoặc người tiếp khách xác nhận.",
                    'checked_in' => "Đã tìm thấy lịch {$visit->code}, nhưng trạng thái hiện tại là Đang trong công ty. Chưa thể check-in.",
                    'checked_out' => "Lịch {$visit->code} đã check-out, không thể check-in lại.",
                    'rejected' => "Lịch {$visit->code} đã bị từ chối, không thể check-in.",
                    'cancelled' => "Lịch {$visit->code} đã bị hủy, không thể check-in.",
                    default => "Không thể check-in lịch {$visit->code} ở trạng thái hiện tại.",
                },
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
            ->with('status', "Da tim thay lich {$visit->code}. Vui long xac nhan check-in.");
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

        $this->sendHostCheckinEmail($visit);

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
            $visit->refresh()->load(['visitor', 'hostEmployee.department', 'department']);

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

    public function scanCheckoutQr(Request $request): RedirectResponse|JsonResponse
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
            if ($request->expectsJson()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Không tìm thấy lịch hẹn với mã vừa nhập.',
                ], 404);
            }

            return redirect()->back()->with('error', 'Không tìm thấy lịch hẹn với mã QR hoặc mã lịch hẹn này.');
        }

        if ($visit->status !== 'checked_in') {
            $insideVisit = Visit::query()
                ->where('visitor_id', $visit->visitor_id)
                ->where('status', 'checked_in')
                ->where('id', '!=', $visit->id)
                ->orderByDesc('actual_checkin_at')
                ->first();

            if ($insideVisit !== null) {
                $visit = $insideVisit;
            }
        }

        $request->session()->put('kiosk_last_visit_id', $visit->id);

        if ($visit->status !== 'checked_in') {
            $message = match ($visit->status) {
                'pending' => "Lịch {$visit->code} chưa được duyệt, khách chưa check-in nên không thể check-out.",
                'approved' => "Lịch {$visit->code} đã được duyệt nhưng khách chưa check-in, không thể check-out.",
                'checked_out' => "Đã tìm thấy lịch {$visit->code}, nhưng trạng thái hiện tại là Đã rời công ty. Chưa thể check-out.",
                'rejected' => "Lịch {$visit->code} đã bị từ chối, không thể check-out.",
                'cancelled' => "Lịch {$visit->code} đã bị hủy, không thể check-out.",
                default => "Chỉ khách đang trong công ty mới được check-out. Trạng thái hiện tại chưa phù hợp.",
            };

            if ($request->expectsJson()) {
                return response()->json([
                    'ok' => false,
                    'message' => $message,
                    'visit' => $this->kioskVisitPayload($visit, false),
                ], 422);
            }

            return redirect()
                ->route('kiosk.checkin.status', $visit)
                ->with('error', $message);
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
            'source' => 'kiosk',
            'meta' => [
                'visit_code' => $visit->code,
                'badge_no' => $badge?->badge_no,
            ],
        ]);

        AuditLog::query()->create([
            'user_id' => null,
            'action' => 'kiosk.checked_out',
            'entity_type' => 'visit',
            'entity_id' => (string) $visit->id,
            'meta' => ['code' => $visit->code],
        ]);

        $this->notifyHost($visit, 'visit.checked_out', 'Khách đã rời công ty', "Khách của lịch {$visit->code} đã check-out tại kiosk.", 'info');

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'message' => "Check-out thành công cho lịch {$visit->code}. Cảm ơn quý khách.",
                'visit' => $this->kioskVisitPayload($visit->refresh(), false),
            ]);
        }

        return redirect()
            ->route('kiosk.checkin.status', $visit)
            ->with('status', "Đã check-out cho lịch {$visit->code}.");
    }

    public function status(Request $request, Visit $visit): View
    {
        $visit->load(['visitor', 'hostEmployee.department', 'department', 'activeBadge']);

        return view('kiosk.status', [
            'visit' => $visit,
            'canConfirm' => (int) $request->session()->get('kiosk_checkin_visit_id') === $visit->id,
            'kioskSettings' => SystemSetting::values(SystemSetting::kioskDefaults()),
        ]);
    }

    private function visitorCardOptions(): \Illuminate\Support\Collection
    {
        $badges = Badge::query()
            ->where('status', 'available')
            ->get(['id', 'badge_no'])
            ->sortBy(fn (Badge $badge): string => $this->badgeDisplaySortKey($badge))
            ->values()
            ->map(fn (Badge $badge): array => [
                'value' => $badge->badge_no,
                'label' => $badge->badge_no,
            ]);

        if ($badges->isNotEmpty()) {
            return $badges;
        }

        return collect(range(1, 20))->map(fn (int $number): array => [
            'value' => (string) $number,
            'label' => 'Visitor card '.$number,
        ]);
    }
    private function badgeDisplaySortKey(Badge $badge): string
    {
        $nameKey = Str::lower(Str::ascii($badge->badge_no));
        $isNoEntryCard = str_contains($nameKey, 'guest do not enter')
            || str_contains($nameKey, 'khach khong vao');

        return ($isNoEntryCard ? '9' : '1') . '|' . str_pad((string) $badge->id, 12, '0', STR_PAD_LEFT);
    }
    private function issueBadgeForVisit(Visit $visit): ?Badge
    {
        $badge = Badge::query()
            ->where('status', 'available')
            ->get()
            ->sortBy(fn (Badge $badge): string => $this->badgeDisplaySortKey($badge))
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

    private function defaultAccessZone(): string
    {
        $settings = SystemSetting::values([
            'access.default_zone' => 'Tang 1 - Le tan',
        ]);
        $zone = trim((string) ($settings['access.default_zone'] ?? ''));

        return $zone !== '' ? $zone : 'Tang 1 - Le tan';
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
            $token = (string) random_int(10000000, 99999999);
        } while (Visit::query()->where('qr_token', $token)->exists());

        return $token;
    }

    private function kioskVisitPayload(Visit $visit, bool $canConfirm): array
    {
        $visit->loadMissing(['visitor', 'hostEmployee.department', 'department']);

        $statusLabels = [
            'pending' => 'Đang chờ phê duyệt',
            'approved' => 'Đã được duyệt',
            'checked_in' => 'Đã check-in',
            'checked_out' => 'Đã rời công ty',
            'rejected' => 'Bị từ chối',
            'cancelled' => 'Đã hủy',
        ];

        $statusHints = [
            'pending' => 'Lịch hẹn chưa được duyệt. Vui lòng chờ lễ tân hoặc người tiếp khách xác nhận.',
            'approved' => 'Lịch hẹn đã được duyệt và sẵn sàng check-in.',
            'checked_in' => 'Khách đang trong công ty.',
            'checked_out' => 'Lịch hẹn này đã hoàn tất check-out.',
            'rejected' => 'Yêu cầu đã bị từ chối. Vui lòng liên hệ lễ tân nếu cần hỗ trợ.',
            'cancelled' => 'Lịch hẹn đã bị hủy.',
        ];

        return [
            'id' => $visit->id,
            'code' => $visit->code,
            'visitor_name' => $visit->visitor?->full_name ?? '-',
            'visitor_company' => $visit->visitor?->company ?? '-',
            'host_name' => $visit->host_display_name,
            'department' => $visit->department_display_name,
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

    private function sendHostCheckinEmail(Visit $visit): void
    {
        if (! DynamicMailSettings::triggerEnabled('mail.trigger_host_checkin')) {
            return;
        }

        $visit->refresh()->loadMissing(['visitor', 'hostEmployee.user', 'hostEmployee.department', 'department']);

        $email = trim((string) ($visit->hostEmployee?->email ?: $visit->hostEmployee?->user?->email ?: ''));
        if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return;
        }

        $visitorName = $visit->visitor?->full_name ?? 'Khách';
        $subject = "Khách đã check-in: {$visitorName} - {$visit->code}";
        $html = view('emails.host-checkin', [
            'visit' => $visit,
            'visitUrl' => route('admin.visits.show', $visit),
        ])->render();

        try {
            $mailSettings = DynamicMailSettings::apply();
            Mail::html($html, function ($message) use ($email, $subject, $mailSettings): void {
                $message->to($email)->subject($subject);
                if (! empty($mailSettings['mail.reply_to'])) {
                    $message->replyTo($mailSettings['mail.reply_to']);
                }
            });

            AuditLog::query()->create([
                'user_id' => null,
                'action' => 'kiosk.host_checkin_email_sent',
                'entity_type' => 'visit',
                'entity_id' => (string) $visit->id,
                'meta' => ['code' => $visit->code, 'email' => $email],
            ]);
        } catch (\Throwable $exception) {
            AuditLog::query()->create([
                'user_id' => null,
                'action' => 'kiosk.host_checkin_email_failed',
                'entity_type' => 'visit',
                'entity_id' => (string) $visit->id,
                'meta' => [
                    'code' => $visit->code,
                    'email' => $email,
                    'error' => $exception->getMessage(),
                ],
            ]);
        }
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

    private function notifyApprovalAdmins(
        string $type,
        string $title,
        string $message,
        string $level,
        Visit $visit
    ): void {
        User::query()
            ->where('is_active', true)
            ->whereHas('roles', fn ($query) => $query->whereIn('slug', ['super_admin', 'admin']))
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
        $approvalActionTypes = ['visit.pending', 'visit.updated', 'kiosk.walk_in_created'];

        Notification::query()->create([
            'user_id' => $user->id,
            'type' => $type,
            'level' => $level,
            'title' => $title,
            'message' => $message,
            'entity_type' => 'visit',
            'entity_id' => (string) $visit->id,
            'action_url' => in_array($type, $approvalActionTypes, true)
                ? route('admin.approvals.index', [], false)
                : route('admin.visits.show', $visit, false),
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

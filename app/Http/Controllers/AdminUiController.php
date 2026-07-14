<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HasAdminLayoutData;
use App\Models\Approval;
use App\Models\AccessControlLog;
use App\Models\AuditLog;
use App\Models\Badge;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Notification;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\UserMobileFavorite;
use App\Models\Visit;
use App\Models\Visitor;
use App\Models\Watchlist;
use App\Support\DynamicMailSettings;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class AdminUiController extends Controller
{
    use HasAdminLayoutData;

    public function onlineRegistration(Request $request): View|RedirectResponse
    {
        $kioskSettings = SystemSetting::values(SystemSetting::kioskDefaults());
        if (($kioskSettings['kiosk.lobby_mode_enabled'] ?? '0') === '1') {
            return redirect()
                ->route('admin.visits.index')
                ->with('status', 'Che do kiosk tai sanh dang bat nen muc Dang ky online da duoc an.');
        }

        $mailSettings = DynamicMailSettings::values();

        return view('admin.online-registration', $this->withBase([
            'registrationUrl' => $this->onlineRegistrationUrl($request),
            'mailConfigured' => $this->onlineRegistrationMailConfigured($mailSettings),
            'mailFromAddress' => $mailSettings['mail.from_address'] ?? null,
            'lobbyModeEnabled' => false,
        ]));
    }

    public function sendOnlineRegistrationEmail(Request $request): RedirectResponse
    {
        $kioskSettings = SystemSetting::values(SystemSetting::kioskDefaults());
        if (($kioskSettings['kiosk.lobby_mode_enabled'] ?? '0') === '1') {
            return redirect()
                ->route('admin.visits.index')
                ->with('error', 'Che do kiosk tai sanh dang bat nen chuc nang gui link dang ky online da duoc an.');
        }

        $validated = $request->validate([
            'recipient_email' => ['required', 'email:rfc', 'max:190'],
        ], [
            'recipient_email.required' => 'Vui lòng nhập Gmail/email của khách cần nhận link.',
            'recipient_email.email' => 'Địa chỉ Gmail/email người nhận chưa hợp lệ.',
        ]);

        $mailSettings = DynamicMailSettings::values();
        if (! $this->onlineRegistrationMailConfigured($mailSettings)) {
            return back()
                ->withInput()
                ->withErrors(['recipient_email' => 'Chưa cấu hình tài khoản Gmail/SMTP. Vui lòng cấu hình Gmail trước khi gửi.']);
        }

        $recipient = strtolower(trim($validated['recipient_email']));
        $registrationUrl = $this->onlineRegistrationUrl($request);

        try {
            $mailSettings = DynamicMailSettings::apply();
            $html = view('emails.online-registration-link', [
                'registrationUrl' => $registrationUrl,
                'mailBrandName' => $mailSettings['mail.from_name'] ?: 'VMS Kiosk',
            ])->render();

            Mail::html($html, function ($message) use ($recipient, $mailSettings): void {
                $message->to($recipient)->subject('Link đăng ký khách online');
                if (! empty($mailSettings['mail.reply_to'])) {
                    $message->replyTo($mailSettings['mail.reply_to']);
                }
            });

            $this->logAudit('online_registration.link_emailed', 'system_setting', 'kiosk-registration', [
                'email' => $recipient,
                'registration_url' => $registrationUrl,
            ]);

            return back()->with('status', "Đã gửi link đăng ký online đến {$recipient}.");
        } catch (\Throwable $exception) {
            report($exception);
            $this->logAudit('online_registration.link_email_failed', 'system_setting', 'kiosk-registration', [
                'email' => $recipient,
                'error' => $exception->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['recipient_email' => 'Không gửi được email. Vui lòng kiểm tra lại cấu hình Gmail/SMTP rồi thử lại.']);
        }
    }

    private function onlineRegistrationUrl(Request $request): string
    {
        return rtrim($request->getSchemeAndHttpHost(), '/').route('kiosk.register', [], false);
    }

    /** @param array<string, string|null> $settings */
    private function onlineRegistrationMailConfigured(array $settings): bool
    {
        return filled($settings['mail.username'] ?? null)
            && filled($settings['mail.password'] ?? null)
            && filled($settings['mail.from_address'] ?? null);
    }
    public function dashboard(Request $request): View|RedirectResponse
    {
        if ($this->isMobileRequest($request)) {
            return redirect()->route('mobile.home');
        }

        $recentFilters = [
            'q' => trim((string) $request->query('recent_q', '')),
            'date' => $request->query('recent_date', ''),
            'status' => $request->query('recent_status', 'all'),
        ];

        if ($recentFilters['date'] !== '' && ! preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $recentFilters['date'])) {
            $recentFilters['date'] = '';
        }

        $allowedRecentStatuses = ['all', 'pending', 'approved', 'checked_in', 'checked_out', 'rejected', 'cancelled'];
        if (! in_array($recentFilters['status'], $allowedRecentStatuses, true)) {
            $recentFilters['status'] = 'all';
        }

        $recentVisits = $this->recentVisitsForDashboard($recentFilters)
            ->paginate(8, ['*'], 'recent_page')
            ->withQueryString();
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
            'visits' => $this->mapVisitRows($recentVisits->getCollection()),
            'recentVisits' => $recentVisits,
            'recentFilters' => $recentFilters,
            'alerts' => $alertsData,
        ]));
    }

    public function mobileHome(Request $request): View
    {
        $user = $request->user();
        $summary = $this->dashboardSummaryData();
        $today = now()->toDateString();

        $recentVisits = $this->recentVisitsForDashboard([
            'q' => '',
            'date' => $today,
            'status' => 'all',
        ])->limit(4)->get();

        $pendingApprovals = $this->scopeVisitsForApproval(
            $this->baseVisitQuery()->where('visits.status', 'pending')
        )->count();
        $unreadNotifications = $user === null
            ? 0
            : Notification::query()->where('user_id', $user->id)->whereNull('read_at')->count();

        $modules = collect([
            [
                'key' => 'visits',
                'label' => 'Lịch hẹn',
                'hint' => 'Tạo và xem lịch',
                'icon' => 'bi-calendar-check',
                'tone' => 'blue',
                'route' => route('mobile.visits.index'),
                'enabled' => $user?->hasPermission('visits.manage') ?? false,
            ],
            [
                'key' => 'approvals',
                'label' => 'Khách cần duyệt',
                'hint' => $pendingApprovals.' yêu cầu',
                'icon' => 'bi-patch-check',
                'tone' => 'green',
                'route' => route('mobile.approvals'),
                'enabled' => ($user?->hasPermission('approvals.manage') ?? false) || $pendingApprovals > 0,
                'count' => $pendingApprovals,
            ],
            [
                'key' => 'checkin',
                'label' => 'Check-in',
                'hint' => $summary['pending_checkin'].' chờ vào',
                'icon' => 'bi-box-arrow-in-right',
                'tone' => 'cyan',
                'route' => route('mobile.checkin'),
                'enabled' => $user?->hasPermission('checkin.manage') ?? false,
                'count' => $summary['pending_checkin'],
            ],
            [
                'key' => 'checkout',
                'label' => 'Check-out',
                'hint' => $summary['in_company'].' trong công ty',
                'icon' => 'bi-box-arrow-left',
                'tone' => 'purple',
                'route' => route('mobile.checkout'),
                'enabled' => $user?->hasPermission('checkin.manage') ?? false,
                'count' => $summary['in_company'],
            ],
            [
                'key' => 'current_visitors',
                'label' => 'Khách trong công ty',
                'hint' => 'Danh sách hiện tại',
                'icon' => 'bi-person-walking',
                'tone' => 'teal',
                'route' => route('mobile.access-lists', ['type' => 'inside']),
                'enabled' => $user?->hasPermission('checkin.manage') ?? false,
            ],
            [
                'key' => 'access_logs',
                'label' => 'Danh sách ra/vào',
                'hint' => 'Tra cứu lịch sử',
                'icon' => 'bi-list-check',
                'tone' => 'orange',
                'route' => route('mobile.access-lists', ['type' => 'all']),
                'enabled' => $user?->hasPermission('checkin.manage') ?? false,
            ],
            [
                'key' => 'reports',
                'label' => 'Báo cáo',
                'hint' => 'Theo dõi dữ liệu',
                'icon' => 'bi-file-earmark-bar-graph',
                'tone' => 'slate',
                'route' => route('mobile.reports'),
                'enabled' => $user?->hasPermission('reports.export') ?? false,
            ],
            [
                'key' => 'notifications',
                'label' => 'Thông báo',
                'hint' => $unreadNotifications.' chưa đọc',
                'icon' => 'bi-bell',
                'tone' => 'rose',
                'route' => route('mobile.notifications'),
                'enabled' => true,
                'count' => $unreadNotifications,
            ],
        ])->filter(fn (array $module): bool => (bool) $module['enabled'])->values();

        $favoriteKeys = $user === null
            ? collect()
            : UserMobileFavorite::query()
                ->where('user_id', $user->id)
                ->orderBy('sort_order')
                ->pluck('module_key');

        $favoriteModules = $favoriteKeys->isEmpty()
            ? $modules
            : $favoriteKeys
                ->map(fn (string $key) => $modules->firstWhere('key', $key))
                ->filter()
                ->values();

        if ($favoriteModules->isEmpty()) {
            $favoriteModules = $modules;
        }

        return view('mobile.home', $this->withBase([
            'stats' => [
                'today' => $summary['today_visits'],
                'pending' => $summary['pending'],
                'pending_checkin' => $summary['pending_checkin'],
                'in_company' => $summary['in_company'],
                'checked_out_today' => $summary['checked_out_today'],
                'overstay' => $summary['overstay'] ?? 0,
            ],
            'modules' => $favoriteModules,
            'availableModules' => $modules,
            'favoriteKeys' => $favoriteKeys,
            'visits' => $this->mapVisitRows($recentVisits),
            'pendingApprovals' => $pendingApprovals,
        ]));
    }

    public function updateMobileFavorites(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user === null) {
            return redirect()->route('login');
        }

        $allowedKeys = $this->mobileModuleKeysForUser($user, $request);
        $keys = collect($request->input('modules', []))
            ->filter(fn ($key): bool => is_string($key) && in_array($key, $allowedKeys, true))
            ->unique()
            ->values();

        UserMobileFavorite::query()
            ->where('user_id', $user->id)
            ->delete();

        $keys->each(function (string $key, int $index) use ($user): void {
            UserMobileFavorite::query()->create([
                'user_id' => $user->id,
                'module_key' => $key,
                'sort_order' => $index + 1,
            ]);
        });

        return redirect()
            ->route('mobile.home')
            ->with('status', 'Đã lưu cài đặt yêu thích.');
    }

    public function mobileApprovals(): View
    {
        $pendingVisits = $this->scopeVisitsForApproval(
            $this->baseVisitQuery()->where('visits.status', 'pending')
        )
            ->orderByDesc('visits.created_at')
            ->limit(20)
            ->get();

        $recentApproved = $this->scopeVisitsForApproval(
            $this->baseVisitQuery()->where('visits.status', 'approved')
        )
            ->orderByDesc('visits.updated_at')
            ->limit(10)
            ->get();

        $recentRejected = $this->scopeVisitsForApproval(
            $this->baseVisitQuery()->where('visits.status', 'rejected')
        )
            ->orderByDesc('visits.updated_at')
            ->limit(10)
            ->get();

        return view('mobile.approvals', $this->withBase([
            'pendingVisits' => $this->mapMobileVisitCards($pendingVisits),
            'approvedVisits' => $this->mapMobileVisitCards($recentApproved),
            'rejectedVisits' => $this->mapMobileVisitCards($recentRejected),
        ]));
    }

    public function mobileCheckin(Request $request): View
    {
        $scannedVisit = null;
        $scannedVisitId = $request->session()->get('checkin_scanned_visit_id');
        if (is_numeric($scannedVisitId)) {
            $scannedVisit = $this->baseVisitQuery()->find((int) $scannedVisitId);
        }

        $readyVisits = $this->baseVisitQuery()
            ->where('visits.status', 'approved')
            ->orderByDesc('visits.scheduled_at')
            ->limit(20)
            ->get();

        return view('mobile.access', $this->withBase([
            'mode' => 'checkin',
            'title' => 'Check-in',
            'subtitle' => 'Quét QR hoặc nhập mã lịch để làm thủ tục vào.',
            'scanRoute' => route('admin.checkin.scan-qr'),
            'accessSettings' => SystemSetting::values(SystemSetting::accessDefaults()),
            'scannedVisit' => $scannedVisit,
            'visits' => $this->mapMobileVisitCards($readyVisits),
        ]));
    }

    public function mobileCheckout(Request $request): View
    {
        $scannedVisit = null;
        $scannedVisitId = $request->session()->get('checkout_scanned_visit_id');
        if (is_numeric($scannedVisitId)) {
            $scannedVisit = $this->baseVisitQuery()->find((int) $scannedVisitId);
        }

        $insideVisits = $this->baseVisitQuery()
            ->where('visits.status', 'checked_in')
            ->orderByDesc('visits.actual_checkin_at')
            ->limit(20)
            ->get();

        return view('mobile.access', $this->withBase([
            'mode' => 'checkout',
            'title' => 'Check-out',
            'subtitle' => 'Quét QR hoặc nhập mã lịch để làm thủ tục ra.',
            'scanRoute' => route('admin.checkout.scan-qr'),
            'accessSettings' => SystemSetting::values(SystemSetting::accessDefaults()),
            'scannedVisit' => $scannedVisit,
            'visits' => $this->mapMobileVisitCards($insideVisits),
        ]));
    }

    public function mobileVisits(Request $request): View
    {
        $status = in_array($request->query('status'), ['all', 'pending', 'approved', 'checked_in', 'checked_out', 'rejected'], true)
            ? (string) $request->query('status')
            : 'all';
        $date = preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $request->query('date'))
            ? (string) $request->query('date')
            : '';
        $keyword = trim((string) $request->query('q', ''));

        $query = $this->baseVisitQuery();

        if ($status !== 'all') {
            $query->where('visits.status', $status);
        }

        if ($date !== '') {
            $query->whereDate('visits.scheduled_at', $date);
        }

        if ($keyword !== '') {
            $query->where(function (Builder $nested) use ($keyword): void {
                $nested->where('visits.code', 'like', "%{$keyword}%")
                    ->orWhereHas('visitor', function (Builder $visitorQuery) use ($keyword): void {
                        $visitorQuery->where('full_name', 'like', "%{$keyword}%")
                            ->orWhere('company', 'like', "%{$keyword}%")
                            ->orWhere('phone', 'like', "%{$keyword}%")
                            ->orWhere('email', 'like', "%{$keyword}%");
                    })
                    ->orWhereHas('hostEmployee', function (Builder $hostQuery) use ($keyword): void {
                        $hostQuery->where('name', 'like', "%{$keyword}%");
                    });
            });
        }

        $visits = $query
            ->orderByDesc('visits.scheduled_at')
            ->orderByDesc('visits.id')
            ->paginate(10)
            ->withQueryString();
        $visits->getCollection()->transform(fn (Visit $visit): array => $this->mapMobileVisitListRow($visit));

        $today = now()->toDateString();

        return view('mobile.visits-index', $this->withBase([
            'visits' => $visits,
            'filters' => [
                'q' => $keyword,
                'date' => $date,
                'status' => $status,
            ],
            'stats' => [
                'today' => Visit::query()->whereDate('scheduled_at', $today)->count(),
                'pending' => Visit::query()->where('status', 'pending')->count(),
                'approved' => Visit::query()->where('status', 'approved')->count(),
            ],
            'canCreateVisit' => $request->user()?->hasPermission('visits.manage') ?? false,
        ]));
    }

    public function mobileVisitsCreate(): View
    {
        return view('mobile.visits-create', $this->withBase([
            'hosts' => $this->hostsForSelect(),
            'departments' => Department::query()->orderBy('name')->get(['id', 'name']),
            'accessZones' => $this->accessZones(),
            'visitFormToken' => $this->createVisitFormToken(),
        ]));
    }

    public function mobileVisitShow(Visit $visit): View
    {
        if (! $this->canViewMobileVisit($visit)) {
            abort(403);
        }

        $visit->load([
            'visitor',
            'hostEmployee.department',
            'approval.approver',
            'activeBadge',
        ]);

        return view('mobile.visit-show', $this->withBase([
            'visit' => $visit,
            'activityLogs' => AuditLog::query()
                ->where('entity_type', 'visit')
                ->where('entity_id', (string) $visit->id)
                ->latest()
                ->limit(8)
                ->get(),
        ]));
    }

    public function mobileProfile(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();
        $user->loadMissing(['roles', 'employeeProfile.department']);

        $employee = $user->employeeProfile;
        if ($employee === null && trim((string) $user->email) !== '') {
            $employee = Employee::query()
                ->with('department')
                ->where('email', $user->email)
                ->first();
        }

        return view('mobile.profile', $this->withBase([
            'profileUser' => $user,
            'profileEmployee' => $employee,
        ]));
    }

    public function mobileAccessLists(Request $request): View
    {
        $type = in_array($request->query('type'), ['inside', 'in', 'out', 'all'], true)
            ? (string) $request->query('type')
            : 'inside';
        $date = Carbon::parse((string) $request->query('date', now()->toDateString()))->toDateString();
        $keyword = trim((string) $request->query('q', ''));

        $from = Carbon::parse($date)->startOfDay();
        $to = Carbon::parse($date)->endOfDay();
        $query = $this->baseVisitQuery();

        match ($type) {
            'inside' => $query->where('visits.status', 'checked_in'),
            'in' => $query->whereBetween('visits.actual_checkin_at', [$from, $to]),
            'out' => $query->whereBetween('visits.actual_checkout_at', [$from, $to]),
            default => $query->where(function (Builder $nested) use ($from, $to): void {
                $nested->whereBetween('visits.actual_checkin_at', [$from, $to])
                    ->orWhereBetween('visits.actual_checkout_at', [$from, $to])
                    ->orWhere('visits.status', 'checked_in');
            }),
        };

        if ($keyword !== '') {
            $query->where(function (Builder $nested) use ($keyword): void {
                $nested->where('visits.code', 'like', "%{$keyword}%")
                    ->orWhereHas('visitor', function (Builder $visitorQuery) use ($keyword): void {
                        $visitorQuery->where('full_name', 'like', "%{$keyword}%")
                            ->orWhere('company', 'like', "%{$keyword}%")
                            ->orWhere('phone', 'like', "%{$keyword}%");
                    })
                    ->orWhereHas('hostEmployee', fn (Builder $hostQuery) => $hostQuery->where('name', 'like', "%{$keyword}%"));
            });
        }

        match ($type) {
            'inside', 'in' => $query->orderByDesc('visits.actual_checkin_at'),
            'out' => $query->orderByDesc('visits.actual_checkout_at'),
            default => $query->orderByRaw('COALESCE(visits.actual_checkout_at, visits.actual_checkin_at, visits.scheduled_at) DESC'),
        };

        $visits = $query->paginate(10)->withQueryString();
        $visits->getCollection()->transform(fn (Visit $visit): array => $this->mapMobileAccessRow($visit, $type));

        return view('mobile.access-lists', $this->withBase([
            'visits' => $visits,
            'filters' => [
                'type' => $type,
                'date' => $date,
                'q' => $keyword,
            ],
            'stats' => [
                'inside' => Visit::query()->where('status', 'checked_in')->count(),
                'in_today' => Visit::query()->whereDate('actual_checkin_at', now()->toDateString())->count(),
                'out_today' => Visit::query()->whereDate('actual_checkout_at', now()->toDateString())->count(),
            ],
        ]));
    }

    public function mobileReports(Request $request): View
    {
        $filters = $this->reportFilters($request);
        $from = Carbon::parse($filters['from_date'])->startOfDay();
        $to = Carbon::parse($filters['to_date'])->endOfDay();
        $visits = $this->filteredVisitsQuery($filters)
            ->orderByDesc('scheduled_at')
            ->get();
        $total = $visits->count();
        $overstayVisits = $visits->filter(
            fn (Visit $visit): bool => $visit->status === 'checked_in'
                && $visit->expected_checkout_at !== null
                && $visit->expected_checkout_at->lt(now())
        );

        $chartDays = [];
        for ($day = $from->copy(); $day->lte($to); $day->addDay()) {
            $daily = $visits->filter(fn (Visit $visit): bool => $visit->scheduled_at?->isSameDay($day) ?? false);
            $chartDays[] = [
                'label' => $day->format('d/m'),
                'total' => $daily->count(),
                'checkin' => $daily->filter(fn (Visit $visit): bool => $visit->actual_checkin_at !== null)->count(),
            ];
        }
        $chartDays = array_slice($chartDays, -7);
        $chartMax = max(1, ...array_column($chartDays, 'total'));

        $topDepartments = $visits
            ->groupBy(fn (Visit $visit): string => $visit->hostEmployee?->department?->name ?? 'Chưa có phòng ban')
            ->map(fn (Collection $items, string $name): array => [
                'name' => $name,
                'total' => $items->count(),
                'percent' => $total > 0 ? (int) round($items->count() / $total * 100) : 0,
            ])
            ->sortByDesc('total')
            ->take(4)
            ->values();

        $statusLabels = [
            'pending' => 'Chờ duyệt',
            'approved' => 'Chờ check-in',
            'rejected' => 'Từ chối',
            'checked_in' => 'Trong công ty',
            'checked_out' => 'Đã ra',
            'cancelled' => 'Đã hủy',
        ];

        return view('mobile.reports', $this->withBase([
            'filters' => $filters,
            'statuses' => ['all' => 'Tất cả trạng thái', ...$statusLabels],
            'statusLabels' => $statusLabels,
            'stats' => [
                'total' => $total,
                'pending' => $visits->where('status', 'pending')->count(),
                'inside' => $visits->where('status', 'checked_in')->count(),
                'checked_out' => $visits->where('status', 'checked_out')->count(),
                'overstay' => $overstayVisits->count(),
            ],
            'chartDays' => $chartDays,
            'chartMax' => $chartMax,
            'topDepartments' => $topDepartments,
            'alerts' => $overstayVisits->sortBy('expected_checkout_at')->take(4)->values(),
            'recentVisits' => $visits->take(8)->values(),
        ]));
    }

    public function mobileNotifications(Request $request): View
    {
        $status = in_array($request->query('status'), ['all', 'unread', 'read'], true)
            ? (string) $request->query('status')
            : 'all';

        $query = Notification::query()
            ->where('user_id', auth()->id())
            ->orderByDesc('id');

        if ($status === 'unread') {
            $query->whereNull('read_at');
        }

        if ($status === 'read') {
            $query->whereNotNull('read_at');
        }

        return view('mobile.notifications', $this->withBase([
            'notifications' => $query->paginate(12)->withQueryString(),
            'filters' => ['status' => $status],
            'unreadCount' => $this->unreadNotificationCount(),
        ]));
    }

    public function markMobileNotificationRead(Notification $notification): RedirectResponse
    {
        if ((int) $notification->user_id !== (int) auth()->id()) {
            abort(404);
        }

        if ($notification->read_at === null) {
            $notification->update(['read_at' => now()]);
        }

        return redirect()
            ->to($this->mobileNotificationActionUrl($notification))
            ->with('status', 'Đã mở thông báo.');
    }

    private function mobileModuleKeysForUser(User $user, Request $request): array
    {
        $pendingApprovals = $this->scopeVisitsForApproval(
            $this->baseVisitQuery()->where('visits.status', 'pending')
        )->count();

        return collect([
            'visits' => $user->hasPermission('visits.manage'),
            'approvals' => $user->hasPermission('approvals.manage') || $pendingApprovals > 0,
            'checkin' => $user->hasPermission('checkin.manage'),
            'checkout' => $user->hasPermission('checkin.manage'),
            'current_visitors' => $user->hasPermission('checkin.manage'),
            'access_logs' => $user->hasPermission('checkin.manage'),
            'reports' => $user->hasPermission('reports.export'),
            'notifications' => true,
        ])
            ->filter()
            ->keys()
            ->all();
    }

    private function isMobileRequest(Request $request): bool
    {
        $userAgent = strtolower((string) $request->userAgent());

        if ($userAgent === '') {
            return false;
        }

        return str_contains($userAgent, 'android')
            || str_contains($userAgent, 'iphone')
            || str_contains($userAgent, 'ipad')
            || str_contains($userAgent, 'ipod')
            || str_contains($userAgent, 'mobile')
            || str_contains($userAgent, 'opera mini')
            || str_contains($userAgent, 'windows phone');
    }

    private function canViewMobileVisit(Visit $visit): bool
    {
        /** @var User|null $user */
        $user = auth()->user();

        if ($user === null) {
            return false;
        }

        return $user->hasPermission('visits.manage')
            || $user->hasPermission('checkin.manage')
            || $this->canActOnVisit($visit);
    }

    private function mapMobileVisitCards(Collection $visits): array
    {
        return $visits->map(function (Visit $visit): array {
            return [
                'id' => $visit->id,
                'code' => $visit->code,
                'visitor' => $visit->visitor?->full_name ?? '-',
                'company' => $visit->visitor?->company ?? '-',
                'phone' => $visit->visitor?->phone ?? '-',
                'email' => $visit->visitor?->email ?? '-',
                'host' => $visit->host_display_name,
                'department' => $visit->department_display_name,
                'purpose' => $visit->purpose,
                'status' => $visit->status,
                'time' => $visit->scheduled_at?->format('H:i') ?? '-',
                'date' => $visit->scheduled_at?->format('d/m/Y') ?? '-',
                'date_iso' => $visit->scheduled_at?->toDateString() ?? '',
                'checkin_at' => $visit->actual_checkin_at?->format('H:i - d/m/Y') ?? '-',
                'checkout_at' => $visit->actual_checkout_at?->format('H:i - d/m/Y') ?? '-',
                'url' => route('mobile.visits.show', $visit),
            ];
        })->all();
    }

    private function mapMobileAccessRow(Visit $visit, string $type): array
    {
        $label = match ($type) {
            'inside' => 'Đang trong công ty',
            'in' => 'Khách vào',
            'out' => 'Khách ra',
            default => $visit->status === 'checked_in'
                ? 'Đang trong công ty'
                : ($visit->actual_checkout_at !== null ? 'Khách ra' : 'Khách vào'),
        };

        $badgeType = match ($label) {
            'Khách vào' => 'in',
            'Khách ra' => 'out',
            default => 'inside',
        };

        return [
            'id' => $visit->id,
            'code' => $visit->code,
            'label' => $label,
            'badge_type' => $badgeType,
            'visitor' => $visit->visitor?->full_name ?? '-',
            'company' => $visit->visitor?->company ?? '-',
            'phone' => $visit->visitor?->phone ?? '-',
            'host' => $visit->host_display_name,
            'department' => $visit->department_display_name,
            'purpose' => $visit->purpose ?? '-',
            'checkin_at' => $visit->actual_checkin_at?->format('H:i d/m/Y') ?? '-',
            'checkout_at' => $visit->actual_checkout_at?->format('H:i d/m/Y') ?? '-',
            'url' => route('mobile.visits.show', $visit),
        ];
    }

    private function mapMobileVisitListRow(Visit $visit): array
    {
        return [
            'id' => $visit->id,
            'code' => $visit->code,
            'visitor' => $visit->visitor?->full_name ?? '-',
            'company' => $visit->visitor?->company ?? '-',
            'phone' => $visit->visitor?->phone ?? '-',
            'host' => $visit->host_display_name,
            'department' => $visit->department_display_name,
            'purpose' => $visit->purpose ?? '-',
            'status' => $visit->status,
            'status_label' => $this->visitStatusLabel($visit->status),
            'time' => $visit->scheduled_at?->format('H:i') ?? '-',
            'date' => $visit->scheduled_at?->format('d/m/Y') ?? '-',
            'date_iso' => $visit->scheduled_at?->toDateString() ?? '',
            'checkin_at' => $visit->actual_checkin_at?->format('H:i d/m/Y') ?? '-',
            'checkout_at' => $visit->actual_checkout_at?->format('H:i d/m/Y') ?? '-',
            'url' => route('mobile.visits.show', $visit),
        ];
    }

    private function mobileNotificationActionUrl(Notification $notification): string
    {
        if ($notification->entity_type === 'visit' && is_numeric($notification->entity_id)) {
            return route('mobile.visits.show', ['visit' => (int) $notification->entity_id]);
        }

        return route('mobile.notifications');
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
            'visitLiveState' => $this->adminVisitLiveState(),
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
    public function visitsLiveState(): JsonResponse
    {
        return response()->json($this->adminVisitLiveState());
    }

    public function visitsCreate(): View
    {
        return view('admin.visits.create', $this->withBase([
            'hosts' => $this->hostsForSelect(),
            'departments' => Department::query()->orderBy('name')->get(['id', 'name']),
            'visitorCardOptions' => $this->visitorCardOptionsForAdmin(),
            'accessZones' => $this->accessZones(),
            'visitFormToken' => $this->createVisitFormToken(),
        ]));
    }

    public function searchVisitors(Request $request): JsonResponse
    {
        $keyword = trim((string) $request->query('q', ''));

        if (mb_strlen($keyword) < 2) {
            return response()->json(['data' => []]);
        }

        $visitorColumns = [
            'id',
            'visitor_code',
            'full_name',
            'phone',
            'email',
            'company',
            'identity_no',
            'visitor_id_card_number',
            'identity_issued_place',
            'identity_issued_date',
            'note',
        ];

        $visitors = Visitor::query()
            ->withCount('visits')
            ->where(function (Builder $query) use ($keyword): void {
                $query
                    ->where('visitor_code', 'like', '%'.$keyword.'%')
                    ->orWhere('full_name', 'like', '%'.$keyword.'%')
                    ->orWhere('phone', 'like', '%'.$keyword.'%')
                    ->orWhere('email', 'like', '%'.$keyword.'%')
                    ->orWhere('company', 'like', '%'.$keyword.'%')
                    ->orWhere('identity_no', 'like', '%'.$keyword.'%')
                    ->orWhere('visitor_id_card_number', 'like', '%'.$keyword.'%');
            })
            ->orderByDesc('visits_count')
            ->orderBy('full_name')
            ->limit(8)
            ->get($visitorColumns);

        if ($visitors->count() < 8) {
            $normalizedKeyword = Str::lower(Str::ascii($keyword));
            $selectedIds = $visitors->modelKeys();

            Visitor::query()
                ->select($visitorColumns)
                ->withCount('visits')
                ->when($selectedIds !== [], fn (Builder $query): Builder => $query->whereNotIn('id', $selectedIds))
                ->orderByDesc('visits_count')
                ->orderBy('full_name')
                ->cursor()
                ->filter(function (Visitor $visitor) use ($normalizedKeyword): bool {
                    $searchableText = implode(' ', array_filter([
                        $visitor->visitor_code,
                        $visitor->full_name,
                        $visitor->phone,
                        $visitor->email,
                        $visitor->company,
                        $visitor->identity_no,
                        $visitor->visitor_id_card_number,
                    ]));

                    return str_contains(Str::lower(Str::ascii($searchableText)), $normalizedKeyword);
                })
                ->take(8 - $visitors->count())
                ->each(fn (Visitor $visitor) => $visitors->push($visitor));
        }

        return response()->json([
            'data' => $visitors->map(fn (Visitor $visitor): array => [
                'id' => $visitor->id,
                'visitor_code' => $visitor->visitor_code,
                'full_name' => $visitor->full_name,
                'phone' => $visitor->phone,
                'email' => $visitor->email,
                'company' => $visitor->company,
                'identity_no' => $visitor->identity_no,
                'visitor_id_card_number' => $visitor->visitor_id_card_number,
                'identity_issued_place' => $visitor->identity_issued_place,
                'identity_issued_date' => $visitor->identity_issued_date?->format('Y-m-d'),
                'note' => $visitor->note,
                'visits_count' => $visitor->visits_count,
            ])->values(),
        ]);
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

        if (! $this->consumeVisitFormToken($request)) {
            return redirect()
                ->route($request->boolean('mobile') ? 'mobile.visits.create' : 'admin.visits.create')
                ->withInput()
                ->with('error', 'Yeu cau tao lich da duoc gui hoac da het han. Vui long kiem tra danh sach lich hen truoc khi tao lai.');
        }

        $visitor = $this->firstOrCreateVisitor($validated);

        $qrBaseTime = $scheduledAt->lt(now()) ? now() : $scheduledAt;

        $visit = Visit::query()->create([
            'code' => $this->generateVisitCode(),
            'visitor_id' => $visitor->id,
            'host_employee_id' => filled($validated['host_employee_id'] ?? null) ? (int) $validated['host_employee_id'] : null,
            'host_name' => $validated['host_name'],
            'department_id' => (int) $validated['department_id'],
            'created_by_user_id' => $this->actingUserId(),
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
        $this->notifyHost($visit, 'visit.pending', 'Lịch hẹn cần bạn duyệt', "Lịch {$visit->code} đang chờ bạn duyệt trước khi lễ tân check-in.", 'warning');
        $this->notifyApprovalAdmins('visit.pending', 'Có lịch hẹn mới cần duyệt', "Lịch {$visit->code} vừa được tạo và đang chờ host duyệt.", 'warning', $visit);
        $this->scanWatchlistForVisit($visit, 'visit.created');

        $redirectRoute = $request->boolean('mobile') ? 'mobile.visits.show' : 'admin.visits.show';

        return redirect()
            ->route($redirectRoute, $visit)
            ->with('status', "Đã tạo lịch hẹn {$visit->code}. Lịch đang chờ host duyệt.");
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
            'hosts' => $this->hostsForSelect(),
            'departments' => Department::query()->orderBy('name')->get(['id', 'name']),
            'visitorCardOptions' => $this->visitorCardOptionsForAdmin($visit->visitor?->visitor_id_card_number),
            'accessZones' => $this->accessZones(),
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
            'departments' => Department::query()->orderBy('name')->get(['id', 'name']),
            'visitorCardOptions' => $this->visitorCardOptionsForAdmin($visit->visitor?->visitor_id_card_number),
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
            'host_employee_id' => filled($validated['host_employee_id'] ?? null) ? (int) $validated['host_employee_id'] : null,
            'host_name' => $validated['host_name'],
            'department_id' => (int) $validated['department_id'],
            'scheduled_at' => $scheduledAt,
            'expected_checkout_at' => $expectedCheckoutAt,
            'status' => 'pending',
            'purpose' => $validated['purpose'],
            'access_zone' => $visit->access_zone,
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
        $this->notifyHost($visit, 'visit.updated', 'Lịch hẹn cần duyệt lại', "Lịch {$visit->code} đã cập nhật và đang chờ bạn duyệt lại.", 'warning');
        $this->notifyApprovalAdmins('visit.updated', 'Lịch hẹn cần duyệt lại', "Lịch {$visit->code} đã cập nhật và quay về trạng thái chờ duyệt.", 'warning', $visit);

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

    public function visitsDestroy(Visit $visit): RedirectResponse
    {
        $code = $visit->code;
        $visitId = $visit->id;

        DB::transaction(function () use ($visit, $visitId): void {
            Badge::query()
                ->where('visit_id', $visitId)
                ->update([
                    'visit_id' => null,
                    'status' => 'available',
                    'issued_at' => null,
                ]);

            $visit->delete();
        });

        $this->logAudit('visit.deleted', 'visit', (string) $visitId, [
            'code' => $code,
        ]);

        return redirect()
            ->route('admin.visits.index')
            ->with('status', "Da xoa lich hen {$code}.");
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

    public function sendVisitQrEmail(Visit $visit): RedirectResponse
    {
        $visit->load(['visitor', 'hostEmployee.department']);

        $email = trim((string) ($visit->visitor?->email ?? ''));
        if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return redirect()
                ->route('admin.visits.show', $visit)
                ->with('error', 'Khách chưa có email hợp lệ để gửi mã QR.');
        }

        if (in_array($visit->status, ['rejected', 'cancelled', 'checked_out'], true)) {
            return redirect()
                ->route('admin.visits.show', $visit)
                ->with('error', "Không thể gửi email cho lịch {$visit->code} ở trạng thái {$this->visitStatusLabel($visit->status)}.");
        }

        if ($visit->status === 'pending') {
            return redirect()
                ->route('admin.visits.show', $visit)
                ->with('error', "Lịch {$visit->code} đang chờ host duyệt. Chỉ gửi mã QR cho khách sau khi lịch đã được duyệt.");
        }

        try {
            $sentEmail = $this->sendQrEmailToVisitor($visit, false);
        } catch (\Throwable $exception) {
            return redirect()
                ->route('admin.visits.show', $visit)
                ->with('error', 'Chưa gửi được email: '.$exception->getMessage());
        }

        return redirect()
            ->route('admin.visits.show', $visit)
            ->with('status', "Đã gửi mã QR lịch {$visit->code} qua Gmail/email cho {$sentEmail}.");
    }

    public function approvalsIndex(): View
    {
        $today = now()->toDateString();

        $pendingVisits = $this->scopeVisitsForApproval(
            $this->baseVisitQuery()->where('status', 'pending')
        )
            ->orderByDesc('created_at')
            ->orderByDesc('scheduled_at')
            ->get();

        $approvedVisits = $this->scopeVisitsForApproval(
            $this->baseVisitQuery()->whereHas(
                'approval',
                fn (Builder $query): Builder => $query->where('status', 'approved')
            )
        )
            ->orderByDesc('updated_at')
            ->limit(30)
            ->get();

        $rejectedVisits = $this->scopeVisitsForApproval(
            $this->baseVisitQuery()->whereHas(
                'approval',
                fn (Builder $query): Builder => $query->where('status', 'rejected')
            )
        )
            ->orderByDesc('updated_at')
            ->limit(20)
            ->get();

        $approvedToday = $this->scopeVisitsForApproval(
            $this->baseVisitQuery()->whereHas(
                'approval',
                fn (Builder $query): Builder => $query
                    ->where('status', 'approved')
                    ->whereDate('acted_at', $today)
            )
        )->count();

        $rejectedToday = $this->scopeVisitsForApproval(
            $this->baseVisitQuery()->whereHas(
                'approval',
                fn (Builder $query): Builder => $query
                    ->where('status', 'rejected')
                    ->whereDate('acted_at', $today)
            )
        )->count();

        $todayVisits = $this->scopeVisitsForApproval(
            $this->baseVisitQuery()->whereDate('scheduled_at', $today)
        )->count();

        $mapApprovalRows = function (Collection $visits): Collection {
            return $visits->map(function (Visit $visit): array {
            $createdAt = $visit->created_at instanceof Carbon ? $visit->created_at : null;
            $waitingMinutes = $createdAt ? max(0, (int) $createdAt->diffInMinutes(now())) : 0;
            $approvalStatus = $visit->approval?->status;
            if (! in_array($approvalStatus, ['pending', 'approved', 'rejected'], true)) {
                $approvalStatus = match ($visit->status) {
                    'rejected' => 'rejected',
                    'approved', 'checked_in', 'checked_out' => 'approved',
                    default => 'pending',
                };
            }

            return [
                'id' => $visit->id,
                'code' => $visit->code,
                'visitor' => $visit->visitor?->full_name ?? '-',
                'company' => $visit->visitor?->company ?? '-',
                'host' => $visit->host_display_name,
                'department' => $visit->department_display_name,
                'creator' => $visit->creator?->name ?? 'Kiosk / Khách tự đăng ký',
                'time' => $visit->scheduled_at?->format('H:i') ?? '-',
                'date' => $visit->scheduled_at?->format('d/m/Y') ?? '-',
                'date_iso' => $visit->scheduled_at?->toDateString() ?? '',
                'created_time' => $createdAt?->format('H:i - d/m/Y') ?? '-',
                'waiting_minutes' => $waitingMinutes,
                'status' => $approvalStatus,
                'visit_status' => $visit->status,
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
            'visitLiveState' => $this->adminVisitLiveState(),
            'urgentApprovals' => $pendingRows
                ->sortByDesc('waiting_minutes')
                ->take(4)
                ->values()
                ->all(),
        ]));
    }

    public function approveVisit(Request $request, Visit $visit): RedirectResponse|JsonResponse
    {
        if ($this->kioskLobbyModeEnabled()) {
            return $this->approveAndCheckin($request, $visit);
        }

        if (! $this->canActOnVisit($visit)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => "Bạn không có quyền xử lý lịch {$visit->code}."], 403);
            }

            return redirect()->back()->with('error', "Bạn không có quyền xử lý lịch {$visit->code}.");
        }

        if ($visit->status !== 'pending') {
            if ($request->expectsJson()) {
                return response()->json(['message' => "Lịch {$visit->code} không ở trạng thái chờ duyệt."], 422);
            }

            return redirect()->back()->with('error', "Lịch {$visit->code} không ở trạng thái chờ duyệt.");
        }

        $approved = Visit::query()
            ->whereKey($visit->id)
            ->where('status', 'pending')
            ->update([
            'status' => 'approved',
            'rejection_reason' => null,
            'qr_token' => $visit->qr_token ?: $this->generateQrToken(),
            'qr_expires_at' => $visit->qr_expires_at ?? ($visit->scheduled_at?->lt(now()) ? now() : ($visit->scheduled_at ?? now()))->copy()->addDay(),
        ]);

        if ($approved !== 1) {
            if ($request->expectsJson()) {
                return response()->json(['message' => "Lịch {$visit->code} đã được xử lý trước đó."], 409);
            }

            return redirect()->back()->with('error', "Lịch {$visit->code} đã được xử lý trước đó.");
        }

        $visit->refresh();

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
        $this->scanWatchlistForVisit($visit, 'approval.approved');
        $this->notifyUsersWithPermission('checkin.manage', 'approval.approved', 'Lịch đã duyệt, sẵn sàng làm thủ tục vào', "Lịch {$visit->code} đã được duyệt và mã QR đã sẵn sàng.", 'success', $visit);

        $statusMessage = "Đã duyệt lịch {$visit->code}. Mã QR đã sẵn sàng.";
        $errorMessage = null;

        if (DynamicMailSettings::triggerEnabled('mail.trigger_qr_approved')) {
            try {
                $sentEmail = $this->sendQrEmailToVisitor($visit);
                $statusMessage .= $sentEmail
                    ? " Đã gửi mã QR cho khách qua {$sentEmail}."
                    : ' Khách chưa có email hợp lệ nên hệ thống chưa gửi được mã QR.';
                if ($sentEmail === null) {
                    $this->notifyUsersWithPermission('visits.manage', 'visit.qr_email_missing', 'Chưa gửi được QR cho khách', "Lịch {$visit->code} đã duyệt nhưng khách chưa có email hợp lệ. Vui lòng bổ sung email hoặc gửi QR thủ công.", 'warning', $visit);
                }
            } catch (\Throwable $exception) {
                $errorMessage = 'Lịch đã được duyệt nhưng chưa gửi được email QR: '.$exception->getMessage();
                $this->notifyUsersWithPermission('visits.manage', 'visit.qr_email_failed', 'Gửi QR cho khách bị lỗi', "Lịch {$visit->code} đã duyệt nhưng gửi email QR thất bại. Vui lòng kiểm tra cấu hình email hoặc gửi lại từ chi tiết lịch.", 'danger', $visit);
            }
        } else {
            $statusMessage .= ' Trigger gửi email QR tự động đang tắt.';
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $statusMessage,
                'error' => $errorMessage,
                'visit' => $this->mapMobileVisitCards(collect([$visit->refresh()]))[0],
            ], $errorMessage === null ? 200 : 207);
        }

        $redirect = redirect()->back()->with('status', $statusMessage);
        if ($errorMessage !== null) {
            $redirect->with('error', $errorMessage);
        }

        return $redirect;
    }

    public function approveAndCheckin(Request $request, Visit $visit): RedirectResponse|JsonResponse
    {
        if (! $this->canActOnVisit($visit)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => "Bạn không có quyền xử lý lịch {$visit->code}."], 403);
            }

            return redirect()->back()->with('error', "Bạn không có quyền xử lý lịch {$visit->code}.");
        }

        if ($visit->status !== 'pending') {
            if ($request->expectsJson()) {
                return response()->json(['message' => "Lịch {$visit->code} không ở trạng thái chờ duyệt."], 422);
            }

            return redirect()->back()->with('error', "Lịch {$visit->code} không ở trạng thái chờ duyệt.");
        }

        $approved = Visit::query()
            ->whereKey($visit->id)
            ->where('status', 'pending')
            ->update([
                'status' => 'approved',
                'rejection_reason' => null,
            ]);

        if ($approved !== 1) {
            if ($request->expectsJson()) {
                return response()->json(['message' => "Lịch {$visit->code} đã được xử lý trước đó."], 409);
            }

            return redirect()->back()->with('error', "Lịch {$visit->code} đã được xử lý trước đó.");
        }

        $visit->refresh();

        Approval::query()->updateOrCreate(
            ['visit_id' => $visit->id],
            [
                'approver_user_id' => $this->actingUserId(),
                'status' => 'approved',
                'note' => 'Lễ tân đã kiểm tra, duyệt và cho khách vào tại sảnh.',
                'acted_at' => now(),
            ]
        );

        $this->logAudit('approval.approved_and_checked_in', 'visit', (string) $visit->id, [
            'code' => $visit->code,
        ]);
        $this->scanWatchlistForVisit($visit, 'approval.approved_and_checked_in');

        $error = $this->performCheckin($visit, true);
        if ($error !== null) {
            return redirect()->back()->with('error', $error);
        }

        $this->logAudit('visit.checked_in', 'visit', (string) $visit->id, [
            'code' => $visit->code,
            'source' => 'approve_and_checkin',
        ]);
        $this->notifyHost($visit, 'visit.checked_in', 'Khách đã check-in', "Khách của lịch {$visit->code} đã vào công ty.", 'success');
        $this->notifyUsersWithPermission('alerts.view', 'visit.checked_in', 'Khách đang trong công ty', "Lịch {$visit->code} đã check-in.", 'info', $visit);
        $this->scanWatchlistForVisit($visit, 'visit.checked_in');

        if ($request->expectsJson()) {
            return response()->json([
                'message' => "Đã duyệt và xác nhận khách vào cho lịch {$visit->code}.",
                'visit' => $this->mapMobileVisitCards(collect([$visit->refresh()]))[0],
            ]);
        }

        return redirect()->back()->with('status', "Đã duyệt và xác nhận khách vào cho lịch {$visit->code}.");
    }
    public function rejectVisit(Request $request, Visit $visit): RedirectResponse|JsonResponse
    {
        if (! $this->canActOnVisit($visit)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => "Bạn không có quyền xử lý lịch {$visit->code}."], 403);
            }

            return redirect()->back()->with('error', "Bạn không có quyền xử lý lịch {$visit->code}.");
        }

        if (! in_array($visit->status, ['pending', 'approved'], true)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => "Không thể từ chối lịch {$visit->code} trong trạng thái hiện tại."], 422);
            }

            return redirect()->back()->with('error', "Không thể từ chối lịch {$visit->code} trong trạng thái hiện tại.");
        }

        $reason = trim((string) $request->input('reason', 'Không phù hợp lịch tiếp khách.'));

        $rejected = Visit::query()
            ->whereKey($visit->id)
            ->whereIn('status', ['pending', 'approved'])
            ->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
        ]);

        if ($rejected !== 1) {
            if ($request->expectsJson()) {
                return response()->json(['message' => "Lịch {$visit->code} đã được xử lý trước đó."], 409);
            }

            return redirect()->back()->with('error', "Lịch {$visit->code} đã được xử lý trước đó.");
        }

        $visit->refresh();

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
        $this->notifyUsersWithPermission('visits.manage', 'approval.rejected', 'Lịch hẹn bị từ chối', "Lịch {$visit->code} bị từ chối. Lý do: {$reason}", 'danger', $visit);

        $message = "Đã từ chối lịch {$visit->code}.";

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'visit' => $this->mapMobileVisitCards(collect([$visit->refresh()]))[0],
            ]);
        }

        return redirect()->back()->with('status', $message);
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

    public function accessIndex(Request $request): View
    {
        $today = now()->toDateString();
        $accessSettings = SystemSetting::values(SystemSetting::accessDefaults());
        $requestedMode = (string) $request->query('mode', '');
        $activeMode = in_array($requestedMode, ['checkin', 'checkout'], true)
            ? $requestedMode
            : ($request->session()->has('checkout_scanned_visit_id') ? 'checkout' : 'checkin');

        $readyToCheckin = $this->baseVisitQuery()
            ->where('status', 'approved')
            ->orderByDesc('scheduled_at')
            ->get();

        $checkinScannedVisit = null;
        $checkinScannedVisitId = $request->session()->get('checkin_scanned_visit_id');
        $checkinScannedByQr = (bool) $request->session()->get('checkin_scanned_by_qr', false);
        if (is_numeric($checkinScannedVisitId)) {
            $checkinScannedVisit = $this->baseVisitQuery()->find((int) $checkinScannedVisitId);
        }

        $insideVisits = $this->baseVisitQuery()
            ->where('status', 'checked_in')
            ->orderByDesc('actual_checkin_at')
            ->orderByDesc('scheduled_at')
            ->get();

        $checkoutScannedVisit = null;
        $checkoutScannedVisitId = $request->session()->get('checkout_scanned_visit_id');
        if (is_numeric($checkoutScannedVisitId)) {
            $checkoutScannedVisit = $this->baseVisitQuery()->find((int) $checkoutScannedVisitId);
        }

        $checkedOutToday = Visit::query()
            ->where('status', 'checked_out')
            ->whereDate('actual_checkout_at', $today)
            ->count();
        $checkedInToday = Visit::query()
            ->where('status', 'checked_in')
            ->whereDate('actual_checkin_at', $today)
            ->count();
        $overstayInside = Visit::query()
            ->where('status', 'checked_in')
            ->whereNotNull('expected_checkout_at')
            ->where('expected_checkout_at', '<', now())
            ->count();
        $historyDate = Carbon::parse((string) $request->query('history_date', $today))->toDateString();
        $historyType = in_array($request->query('history'), ['in', 'out'], true)
            ? (string) $request->query('history')
            : null;
        $accessHistory = $historyType === null
            ? []
            : $this->baseVisitQuery()
                ->whereDate($historyType === 'in' ? 'actual_checkin_at' : 'actual_checkout_at', $historyDate)
                ->get()
                ->map(function (Visit $visit) use ($historyType): array {
                    $time = $historyType === 'in' ? $visit->actual_checkin_at : $visit->actual_checkout_at;

                    return [
                        'id' => $visit->id,
                        'code' => $visit->code,
                        'type' => $historyType,
                        'label' => $historyType === 'in' ? 'Khách vào' : 'Khách ra',
                        'time' => $time?->format('H:i') ?? '-',
                        'sort_at' => $time?->timestamp ?? 0,
                        'visitor' => $visit->visitor?->full_name ?? '-',
                        'company' => $visit->visitor?->company ?? '-',
                        'host' => $visit->host_display_name,
                        'department' => $visit->department_display_name,
                        'detail_url' => route('admin.visits.show', $visit),
                    ];
                })
                ->sortByDesc('sort_at')
                ->values()
                ->all();

        return view('admin.access.index', $this->withBase([
            'activeMode' => $activeMode,
            'accessSettings' => $accessSettings,
            'readyToCheckin' => $this->mapVisitRows($readyToCheckin),
            'checkinScannedVisit' => $checkinScannedVisit,
            'checkinScannedQrExpired' => $checkinScannedByQr && ($checkinScannedVisit?->qr_expires_at?->lt(now()) ?? false),
            'insideVisits' => $insideVisits->map(function (Visit $visit): array {
                $isOverstay = $visit->expected_checkout_at?->lt(now()) ?? false;

                return [
                    'id' => $visit->id,
                    'code' => $visit->code,
                    'visitor' => $visit->visitor?->full_name ?? '-',
                    'company' => $visit->visitor?->company ?? '-',
                    'host' => $visit->host_display_name,
                    'department' => $visit->department_display_name,
                    'checkin_time' => $visit->actual_checkin_at?->format('H:i') ?? '-',
                    'remaining' => $visit->expected_checkout_at === null
                        ? '-'
                        : ($isOverstay ? 'Quá giờ' : $this->humanDuration(now(), $visit->expected_checkout_at)),
                    'is_overstay' => $isOverstay,
                ];
            })->all(),
            'checkoutScannedVisit' => $checkoutScannedVisit,
            'accessStats' => [
                'waiting_in' => $readyToCheckin->count(),
                'inside' => $insideVisits->count(),
                'checked_in_today' => $checkedInToday,
                'checked_out_today' => $checkedOutToday,
                'overstay' => $overstayInside,
            ],
            'accessHistory' => $accessHistory,
            'accessHistoryDate' => $historyDate,
            'accessHistoryType' => $historyType,
            'accessHistoryOpen' => $historyType !== null,
        ]));
    }

    public function accessListsIndex(Request $request): View
    {
        $data = $this->accessListData($request, true);

        return view('admin.access.lists', $this->withBase($data));
    }

    public function accessListsExport(Request $request): StreamedResponse
    {
        $data = $this->accessListData($request, false);
        $rows = $data['accessRows'];
        $filename = 'danh-sach-ra-vao-'.$data['filters']['type'].'-'.$data['filters']['from'].'-'.$data['filters']['to'].'.csv';

        return response()->streamDownload(function () use ($rows): void {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($handle, ['Mã lịch', 'Trạng thái', 'Khách', 'Công ty', 'Người gặp', 'Phòng ban', 'Giờ vào', 'Giờ ra', 'Mục đích']);

            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row['code'],
                    $row['label'],
                    $row['visitor'],
                    $row['company'],
                    $row['host'],
                    $row['department'],
                    $row['checkin_at'],
                    $row['checkout_at'],
                    $row['purpose'],
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function accessListData(Request $request, bool $paginate = false): array
    {
        $type = in_array($request->query('type'), ['inside', 'in', 'out', 'all'], true)
            ? (string) $request->query('type')
            : 'inside';
        $from = Carbon::parse((string) $request->query('from', now()->toDateString()))->startOfDay();
        $to = Carbon::parse((string) $request->query('to', now()->toDateString()))->endOfDay();
        $keyword = trim((string) $request->query('q', ''));
        $department = trim((string) $request->query('department', ''));

        $query = $this->baseVisitQuery();

        match ($type) {
            'inside' => $query->where('status', 'checked_in'),
            'in' => $query->whereBetween('actual_checkin_at', [$from, $to]),
            'out' => $query->whereBetween('actual_checkout_at', [$from, $to]),
            default => $query->where(function ($nested) use ($from, $to): void {
                $nested->whereBetween('actual_checkin_at', [$from, $to])
                    ->orWhereBetween('actual_checkout_at', [$from, $to])
                    ->orWhere('status', 'checked_in');
            }),
        };

        if ($keyword !== '') {
            $query->where(function ($nested) use ($keyword): void {
                $nested->where('code', 'like', "%{$keyword}%")
                    ->orWhereHas('visitor', function ($visitorQuery) use ($keyword): void {
                        $visitorQuery->where('full_name', 'like', "%{$keyword}%")
                            ->orWhere('company', 'like', "%{$keyword}%")
                            ->orWhere('phone', 'like', "%{$keyword}%");
                    })
                    ->orWhereHas('hostEmployee', function ($hostQuery) use ($keyword): void {
                        $hostQuery->where('name', 'like', "%{$keyword}%");
                    });
            });
        }

        if ($department !== '') {
            $query->whereHas('hostEmployee.department', function ($departmentQuery) use ($department): void {
                $departmentQuery->where('name', $department);
            });
        }

        $totalRows = (clone $query)->count();

        match ($type) {
            'inside', 'in' => $query->orderByDesc('actual_checkin_at'),
            'out' => $query->orderByDesc('actual_checkout_at'),
            default => $query->orderByRaw('COALESCE(actual_checkout_at, actual_checkin_at, scheduled_at) DESC'),
        };

        $mapRow = function (Visit $visit) use ($type): array {
            $label = match ($type) {
                'inside' => 'Đang trong công ty',
                'in' => 'Khách vào',
                'out' => 'Khách ra',
                default => $visit->status === 'checked_in'
                    ? 'Đang trong công ty'
                    : ($visit->actual_checkout_at !== null ? 'Khách ra' : 'Khách vào'),
            };
            $badgeType = match ($label) {
                'Khách vào' => 'in',
                'Khách ra' => 'out',
                default => 'inside',
            };

            return [
                'id' => $visit->id,
                'code' => $visit->code,
                'label' => $label,
                'badge_type' => $badgeType,
                'visitor' => $visit->visitor?->full_name ?? '-',
                'company' => $visit->visitor?->company ?? '-',
                'phone' => $visit->visitor?->phone ?? '-',
                'host' => $visit->host_display_name,
                'department' => $visit->department_display_name,
                'purpose' => $visit->purpose ?? '-',
                'checkin_at' => $visit->actual_checkin_at?->format('H:i d/m/Y') ?? '-',
                'checkout_at' => $visit->actual_checkout_at?->format('H:i d/m/Y') ?? '-',
                'detail_url' => route('admin.visits.show', $visit),
            ];
        };

        if ($paginate) {
            $rows = $query->paginate(10)->withQueryString();
            $rows->getCollection()->transform($mapRow);
        } else {
            $rows = $query->get()->map($mapRow);
        }

        $today = now()->toDateString();

        return [
            'accessRows' => $rows,
            'filters' => [
                'type' => $type,
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
                'q' => $keyword,
                'department' => $department,
            ],
            'listStats' => [
                'inside' => Visit::query()->where('status', 'checked_in')->count(),
                'in_today' => Visit::query()->whereDate('actual_checkin_at', $today)->count(),
                'out_today' => Visit::query()->whereDate('actual_checkout_at', $today)->count(),
                'all_range' => $totalRows,
            ],
            'departments' => Department::query()->orderBy('name')->pluck('name')->all(),
        ];
    }

    public function checkinIndex(Request $request): View
    {
        $today = now()->toDateString();

        $readyToCheckin = $this->baseVisitQuery()
            ->where('status', 'approved')
            ->orderByDesc('scheduled_at')
            ->get();

        $approvedWaitingCheckin = $this->baseVisitQuery()
            ->where('status', 'approved')
            ->orderByDesc('scheduled_at')
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
        $error = $this->performCheckin($visit, $this->kioskLobbyModeEnabled());
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

        if (in_array($visit->status, ['checked_out', 'cancelled', 'rejected'], true)) {
            $activeVisit = $this->findActiveVisitForVisitor($visit->visitor_id, $visit->id);
            if ($activeVisit !== null) {
                $visit = $activeVisit;
                $scannedByQr = false;
            }
        }

        $redirectRoute = $request->boolean('mobile') ? 'mobile.checkin' : 'admin.access.index';
        $redirectParams = $request->boolean('mobile') ? [] : ['mode' => 'checkin'];
        $redirect = redirect()
            ->route($redirectRoute, $redirectParams)
            ->with('checkin_scanned_visit_id', $visit->id)
            ->with('checkin_scanned_by_qr', $scannedByQr);

        if ($scannedByQr && $visit->qr_expires_at !== null && $visit->qr_expires_at->lt(now())) {
            return $redirect->with('error', "QR của lịch {$visit->code} đã hết hạn. Vui lòng tạo QR mới hoặc liên hệ quản trị.");
        }

        if ($visit->status === 'pending') {
            return $redirect->with('error', "Lịch {$visit->code} chưa được duyệt. Vui lòng vào trang Duyệt lịch để phê duyệt trước khi check-in.");
        }

        if ($visit->status !== 'approved') {
            return $redirect->with('error', "Đã tìm thấy lịch {$visit->code}, nhưng trạng thái hiện tại là {$this->visitStatusLabel($visit->status)}. Chưa thể check-in.");
        }

        $this->logAudit('visit.qr_scanned_for_checkin', 'visit', (string) $visit->id, [
            'code' => $visit->code,
        ]);

        $error = $this->performCheckin($visit, $this->kioskLobbyModeEnabled());
        if ($error !== null) {
            return $redirect
                ->with('checkin_scanned_visit_id', $visit->id)
                ->with('checkin_scanned_by_qr', $scannedByQr)
                ->with('error', $error);
        }

        $this->logAudit('visit.checked_in', 'visit', (string) $visit->id, [
            'code' => $visit->code,
        ]);
        $this->notifyHost($visit, 'visit.checked_in', 'Khách đã check-in', "Khách của lịch {$visit->code} đã vào công ty.", 'success');
        $this->notifyUsersWithPermission('alerts.view', 'visit.checked_in', 'Khách đang trong công ty', "Lịch {$visit->code} đã check-in.", 'info', $visit);
        $this->scanWatchlistForVisit($visit, 'visit.checked_in');

        return $redirect
            ->with('checkin_scanned_visit_id', $visit->id)
            ->with('checkin_scanned_by_qr', $scannedByQr)
            ->with('status', "Đã tự động xác nhận khách vào cho lịch {$visit->code}.");
    }
    public function checkoutIndex(): View
    {
        $insideVisits = $this->baseVisitQuery()
            ->where('status', 'checked_in')
            ->orderByDesc('actual_checkin_at')
            ->orderByDesc('scheduled_at')
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
                    'host' => $visit->host_display_name,
                    'department' => $visit->department_display_name,
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
            ->get()
            ->sortBy(fn (Badge $badge): string => $this->badgeDisplaySortKey($badge))
            ->values();

        return view('admin.badges.index', $this->withBase([
            'badges' => $badges,
        ]));
    }

    private function badgeDisplaySortKey(Badge $badge): string
    {
        $nameKey = Str::lower(Str::ascii($badge->badge_no));
        $isNoEntryCard = str_contains($nameKey, 'guest do not enter')
            || str_contains($nameKey, 'khach khong vao');

        return ($isNoEntryCard ? '9' : '1') . '|' . str_pad((string) $badge->id, 12, '0', STR_PAD_LEFT);
    }

    /**
     * Admin screens do not switch language, so show both configured labels.
     *
     * @return Collection<int, array{value: string, label: string}>
     */
    private function visitorCardOptionsForAdmin(?string $currentValue = null): Collection
    {
        $currentValue = trim((string) $currentValue);

        $options = Badge::query()
            ->where(function ($query) use ($currentValue): void {
                $query->where('status', 'available');

                if ($currentValue !== '') {
                    $query->orWhere('badge_no', $currentValue);
                }
            })
            ->get(['id', 'badge_no', 'label_vi', 'label_en'])
            ->sortBy(fn (Badge $badge): string => $this->badgeDisplaySortKey($badge))
            ->map(function (Badge $badge): array {
                $labelVi = trim((string) ($badge->label_vi ?: $badge->badge_no));
                $labelEn = trim((string) ($badge->label_en ?: $badge->badge_no));
                $label = $labelVi === $labelEn ? $labelVi : 'VI: '.$labelVi.' — EN: '.$labelEn;

                if (! in_array($badge->badge_no, [$labelVi, $labelEn], true)) {
                    $label .= ' ('.$badge->badge_no.')';
                }

                return ['value' => $badge->badge_no, 'label' => $label];
            })
            ->values();

        if ($currentValue !== '' && ! $options->contains('value', $currentValue)) {
            $options->prepend(['value' => $currentValue, 'label' => $currentValue.' (đang lưu)']);
        }

        return $options;
    }

    public function badgesStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'badge_no' => ['nullable', 'string', 'max:40'],
            'badge_numbers' => ['nullable', 'string', 'max:12000'],
            'badge_prefix' => ['nullable', 'string', 'max:30'],
            'badge_range_start' => ['nullable', 'integer', 'min:1', 'max:9999'],
            'badge_range_end' => ['nullable', 'integer', 'min:1', 'max:9999'],
            'status' => ['nullable', 'in:available,revoked'],
            'label_vi' => ['required_with:badge_no', 'nullable', 'string', 'max:120'],
            'label_en' => ['required_with:badge_no', 'nullable', 'string', 'max:120'],
        ], [
            'badge_no.max' => 'Số thẻ không được quá 40 ký tự.',
            'badge_numbers.max' => 'Danh sách thẻ quá dài, vui lòng chia thành nhiều lần thêm.',
            'badge_range_start.integer' => 'Số bắt đầu phải là số.',
            'badge_range_end.integer' => 'Số kết thúc phải là số.',
        ]);

        $badgeNumbers = collect();

        if (filled($validated['badge_no'] ?? null)) {
            $badgeNumbers->push(trim((string) $validated['badge_no']));
        }

        if (filled($validated['badge_numbers'] ?? null)) {
            $items = preg_split('/[\r\n,;]+/u', (string) $validated['badge_numbers']);
            foreach ($items as $item) {
                $item = trim((string) $item);
                if ($item !== '') {
                    $badgeNumbers->push($item);
                }
            }
        }

        $rangeStart = $validated['badge_range_start'] ?? null;
        $rangeEnd = $validated['badge_range_end'] ?? null;

        if ($rangeStart !== null || $rangeEnd !== null) {
            if ($rangeStart === null || $rangeEnd === null || $rangeEnd < $rangeStart) {
                return back()
                    ->withErrors(['badge_range_start' => 'Vui lòng nhập dải số hợp lệ, ví dụ từ 1 đến 100.'])
                    ->withInput();
            }

            $prefix = trim((string) ($validated['badge_prefix'] ?? 'Visitor card'));
            if ($prefix === '') {
                $prefix = 'Visitor card';
            }

            for ($number = (int) $rangeStart; $number <= (int) $rangeEnd; $number++) {
                $badgeNumbers->push($prefix . ' ' . $number);
            }
        }

        $badgeNumbers = $badgeNumbers
            ->map(fn ($value) => trim((string) $value))
            ->filter()
            ->unique()
            ->values();

        if ($badgeNumbers->isEmpty()) {
            return back()
                ->withErrors(['badge_no' => 'Vui lòng nhập ít nhất một số thẻ khách.'])
                ->withInput();
        }

        if ($badgeNumbers->count() > 300) {
            return back()
                ->withErrors(['badge_numbers' => 'Mỗi lần chỉ nên thêm tối đa 300 thẻ để hệ thống xử lý ổn định.'])
                ->withInput();
        }

        $tooLong = $badgeNumbers->first(fn ($value) => mb_strlen($value) > 40);
        if ($tooLong !== null) {
            return back()
                ->withErrors(['badge_numbers' => 'Số thẻ "' . $tooLong . '" dài quá 40 ký tự.'])
                ->withInput();
        }

        $existing = Badge::query()
            ->whereIn('badge_no', $badgeNumbers->all())
            ->pluck('badge_no')
            ->all();

        if (! empty($existing)) {
            return back()
                ->withErrors(['badge_numbers' => 'Các số thẻ đã tồn tại: ' . implode(', ', array_slice($existing, 0, 12)) . (count($existing) > 12 ? '...' : '')])
                ->withInput();
        }

        $status = $validated['status'] ?? 'available';

        foreach ($badgeNumbers as $badgeNo) {
            $autoVi = preg_replace('/^Visitor\s+card\s+(\d+)$/i', 'Thẻ khách $1', $badgeNo);
            Badge::query()->create([
                'badge_no' => $badgeNo,
                'label_vi' => $badgeNumbers->count() === 1 && filled($validated['label_vi'] ?? null) ? trim($validated['label_vi']) : $autoVi,
                'label_en' => $badgeNumbers->count() === 1 && filled($validated['label_en'] ?? null) ? trim($validated['label_en']) : $badgeNo,
                'status' => $status,
            ]);
        }

        return redirect()
            ->route('admin.badges.index')
            ->with('status', 'Đã thêm ' . $badgeNumbers->count() . ' số thẻ khách.');
    }

    public function badgesUpdate(Request $request, Badge $badge): RedirectResponse
    {
        $validated = $request->validate([
            'badge_no' => ['required', 'string', 'max:40', \Illuminate\Validation\Rule::unique('badges', 'badge_no')->ignore($badge->id)],
            'status' => ['required', 'in:available,revoked'],
            'label_vi' => ['required', 'string', 'max:120'],
            'label_en' => ['required', 'string', 'max:120'],
        ]);

        if ($badge->status === 'active') {
            $badge->update([
                'badge_no' => trim($validated['badge_no']),
                'label_vi' => trim($validated['label_vi']),
                'label_en' => trim($validated['label_en']),
            ]);

            return redirect()->route('admin.badges.index')->with('status', 'Đã cập nhật mã thẻ đang sử dụng.');
        }

        $badge->update([
            'badge_no' => trim($validated['badge_no']),
            'label_vi' => trim($validated['label_vi']),
            'label_en' => trim($validated['label_en']),
            'status' => $validated['status'],
            'visit_id' => $validated['status'] === 'available' ? null : $badge->visit_id,
            'issued_at' => $validated['status'] === 'available' ? null : $badge->issued_at,
            'valid_until' => $validated['status'] === 'available' ? null : $badge->valid_until,
        ]);

        return redirect()->route('admin.badges.index')->with('status', 'Đã cập nhật số thẻ khách.');
    }

    public function badgesDestroy(Badge $badge): RedirectResponse
    {
        if ($badge->status === 'active') {
            return redirect()->route('admin.badges.index')->with('error', 'Không thể xóa thẻ đang được khách sử dụng.');
        }

        $badge->delete();

        return redirect()->route('admin.badges.index')->with('status', 'Đã xóa số thẻ khách.');
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

        if ($visit->status !== 'checked_in') {
            $redirectRoute = $request->boolean('mobile') ? 'mobile.checkout' : 'admin.access.index';
            $redirectParams = $request->boolean('mobile') ? [] : ['mode' => 'checkout'];

            return redirect()
                ->route($redirectRoute, $redirectParams)
                ->with('checkout_scanned_visit_id', $visit->id)
                ->with('error', "Đã tìm thấy lịch {$visit->code}, nhưng trạng thái hiện tại là {$this->visitStatusLabel($visit->status)}. Chỉ khách đang trong công ty mới được làm thủ tục ra.");
        }

        $this->logAudit('visit.qr_scanned_for_checkout', 'visit', (string) $visit->id, [
            'code' => $visit->code,
            'qr_token' => $validated['qr_token'],
        ]);

        $error = $this->performCheckout($visit);
        if ($error !== null) {
            $redirectRoute = $request->boolean('mobile') ? 'mobile.checkout' : 'admin.access.index';
            $redirectParams = $request->boolean('mobile') ? [] : ['mode' => 'checkout'];

            return redirect()
                ->route($redirectRoute, $redirectParams)
                ->with('checkout_scanned_visit_id', $visit->id)
                ->with('error', $error);
        }

        $this->logAudit('visit.checked_out', 'visit', (string) $visit->id, [
            'code' => $visit->code,
        ]);
        $this->notifyHost($visit, 'visit.checked_out', 'Khách đã rời công ty', "Khách của lịch {$visit->code} đã ra khỏi công ty.", 'info');

        $redirectRoute = $request->boolean('mobile') ? 'mobile.checkout' : 'admin.access.index';
        $redirectParams = $request->boolean('mobile') ? [] : ['mode' => 'checkout'];

        return redirect()
            ->route($redirectRoute, $redirectParams)
            ->with('checkout_scanned_visit_id', $visit->id)
            ->with('status', "Đã tự động xác nhận khách ra cho lịch {$visit->code}.");
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

        $error = $this->performCheckout($badge->visit);
        if ($error !== null) {
            return redirect()
                ->route('admin.access.index', ['mode' => 'checkout'])
                ->with('checkout_scanned_visit_id', $badge->visit->id)
                ->with('error', $error);
        }

        $this->logAudit('visit.checked_out', 'visit', (string) $badge->visit->id, [
            'code' => $badge->visit->code,
        ]);
        $this->notifyHost($badge->visit, 'visit.checked_out', 'Khách đã rời công ty', "Khách của lịch {$badge->visit->code} đã ra khỏi công ty.", 'info');

        return redirect()
            ->route('admin.access.index', ['mode' => 'checkout'])
            ->with('checkout_scanned_visit_id', $badge->visit->id)
            ->with('status', "Đã tự động xác nhận khách ra bằng thẻ {$badge->badge_no}.");
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
            ->to($notification->localActionUrl() ?: route('admin.notifications.index', [], false))
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

    public function notificationsDestroy(Notification $notification): RedirectResponse
    {
        if ((int) $notification->user_id !== (int) auth()->id()) {
            abort(404);
        }

        $notification->delete();

        return redirect()
            ->route('admin.notifications.index')
            ->with('status', 'Da xoa thong bao.');
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
                fn (Visit $visit) => (string) ($visit->department_id ?? $visit->hostEmployee?->department_id ?? '') === $departmentFilter
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
        $pendingApproval = $visits->where('status', 'pending')->count();
        $pendingCheckin = $visits->where('status', 'approved')->count();
        $overstayVisits = $visits->filter(
            fn (Visit $visit) => $visit->status === 'checked_in'
                && $visit->expected_checkout_at !== null
                && $visit->expected_checkout_at->lt(now())
        );
        $overstay = $overstayVisits->count();

        $reportAlerts = $overstayVisits
            ->sortBy('expected_checkout_at')
            ->map(fn (Visit $visit): array => [
                'tone' => 'danger',
                'icon' => 'bi-alarm',
                'title' => 'Khách quá giờ: '.($visit->visitor?->full_name ?? 'Chưa rõ tên'),
                'detail' => $visit->code.' · '.($visit->hostEmployee?->department?->name ?? 'Chưa có phòng ban'),
                'time' => $visit->expected_checkout_at?->format('H:i d/m'),
                'url' => route('admin.visits.show', $visit),
            ])
            ->concat(
                $visits->where('status', 'pending')
                    ->sortBy('scheduled_at')
                    ->map(fn (Visit $visit): array => [
                        'tone' => 'warning',
                        'icon' => 'bi-hourglass-split',
                        'title' => 'Chờ duyệt: '.($visit->visitor?->full_name ?? 'Chưa rõ tên'),
                        'detail' => $visit->code.' · '.($visit->host_display_name !== '-' ? $visit->host_display_name : 'Chưa có người tiếp'),
                        'time' => $visit->scheduled_at?->format('H:i d/m'),
                        'url' => route('admin.visits.show', $visit),
                    ])
            )
            ->take(5)
            ->values();

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
            ->groupBy(fn (Visit $visit) => $visit->host_employee_id !== null
                ? 'employee:'.$visit->host_employee_id
                : 'manual:'.Str::lower($visit->host_display_name))
            ->map(function (Collection $items): array {
                $visit = $items->first();

                return [
                    'name' => $visit?->host_display_name !== '-' ? $visit->host_display_name : 'Chưa có người tiếp',
                    'department' => $visit?->department_display_name ?? '-',
                    'total' => $items->count(),
                ];
            })
            ->sortByDesc('total')
            ->take(5)
            ->values();

        $topDepartments = $visits
            ->groupBy(fn (Visit $visit) => $visit->department_display_name !== '-' ? $visit->department_display_name : 'Chưa có phòng ban')
            ->map(fn (Collection $items, string $name): array => [
                'name' => $name,
                'total' => $items->count(),
                'percent' => $total > 0 ? round($items->count() / $total * 100) : 0,
            ])
            ->sortByDesc('total')
            ->take(5)
            ->values();

        $departments = $rangeVisits
            ->map(fn (Visit $visit) => $visit->department ?? $visit->hostEmployee?->department)
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
                'pending_approval' => $pendingApproval,
                'pending_checkin' => $pendingCheckin,
                'overstay' => $overstay,
                'growth' => $previousTotal > 0 ? round(($total - $previousTotal) / $previousTotal * 100) : ($total > 0 ? 100 : 0),
            ],
            'chartDays' => $chartDays,
            'reportAlerts' => $reportAlerts,
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
            ->withoutEagerLoads()
            ->leftJoin('employees', 'visits.host_employee_id', '=', 'employees.id')
            ->leftJoin('departments as visit_departments', 'visits.department_id', '=', 'visit_departments.id')
            ->leftJoin('departments as employee_departments', 'employees.department_id', '=', 'employee_departments.id')
            ->selectRaw('COALESCE(visit_departments.name, employee_departments.name, "No department") as department')
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
            ->withoutEagerLoads()
            ->leftJoin('employees', 'visits.host_employee_id', '=', 'employees.id')
            ->leftJoin('departments as visit_departments', 'visits.department_id', '=', 'visit_departments.id')
            ->leftJoin('departments as employee_departments', 'employees.department_id', '=', 'employee_departments.id')
            ->selectRaw('employees.id as host_id')
            ->selectRaw('COALESCE(NULLIF(visits.host_name, ""), employees.name, "No host") as host')
            ->selectRaw('COALESCE(visit_departments.name, employee_departments.name, "No department") as department')
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
                'host' => $visit->host_display_name,
                'department' => $visit->department_display_name,
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
            'host' => $visit->host_display_name,
            'department' => $visit->department_display_name,
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
                ->withoutEagerLoads()
                ->leftJoin('employees', 'visits.host_employee_id', '=', 'employees.id')
                ->leftJoin('departments as visit_departments', 'visits.department_id', '=', 'visit_departments.id')
                ->leftJoin('departments as employee_departments', 'employees.department_id', '=', 'employee_departments.id')
                ->selectRaw('COALESCE(visit_departments.name, employee_departments.name, "No department") as department')
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
                ->withoutEagerLoads()
                ->leftJoin('employees', 'visits.host_employee_id', '=', 'employees.id')
                ->leftJoin('departments as visit_departments', 'visits.department_id', '=', 'visit_departments.id')
                ->leftJoin('departments as employee_departments', 'employees.department_id', '=', 'employee_departments.id')
                ->selectRaw('COALESCE(NULLIF(visits.host_name, ""), employees.name, "No host") as host')
                ->selectRaw('COALESCE(visit_departments.name, employee_departments.name, "No department") as department')
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
                'host' => $visit->host_display_name,
                'department' => $visit->department_display_name,
                'creator' => $visit->creator?->name ?? 'Kiosk / Khách tự đăng ký',
                'time' => $visit->scheduled_at?->format('H:i') ?? '-',
                'date' => $visit->scheduled_at?->format('d/m/Y') ?? '-',
                'date_iso' => $visit->scheduled_at?->toDateString() ?? '',
                'status' => $visit->status,
                'approver' => $visit->approval?->approver?->name
                    ?? ($visit->status === 'pending' ? 'Chưa duyệt' : 'Chưa có thông tin'),
                'purpose' => $visit->purpose,
            ];
        })->all();
    }

    private function createVisitFormToken(): string
    {
        $token = (string) Str::uuid();
        session()->put($this->visitFormTokenSessionKey($token), now()->addMinutes(30)->toIso8601String());

        return $token;
    }

    private function consumeVisitFormToken(Request $request): bool
    {
        $token = (string) $request->input('visit_form_token', '');

        if ($token === '') {
            return false;
        }

        $expiresAt = session()->pull($this->visitFormTokenSessionKey($token));

        if (! is_string($expiresAt)) {
            return false;
        }

        return Carbon::parse($expiresAt)->isFuture();
    }

    private function visitFormTokenSessionKey(string $token): string
    {
        return 'visit_create_token_'.$token;
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
            'pending_checkin' => Visit::query()->where('status', 'approved')->count(),
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
                    'host' => $visit->host_display_name,
                    'department' => $visit->department_display_name,
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

    /**
     * @param  array{q: string, date: string, status: string}  $filters
     */
    private function recentVisitsForDashboard(array $filters): Builder
    {
        $query = $this->baseVisitQuery();

        if (($filters['date'] ?? '') !== '') {
            $query->whereDate('scheduled_at', $filters['date']);
        }

        if ($filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        if ($filters['q'] !== '') {
            $keyword = $filters['q'];
            $query->where(function (Builder $query) use ($keyword): void {
                $query->where('code', 'like', "%{$keyword}%")
                    ->orWhere('purpose', 'like', "%{$keyword}%")
                    ->orWhereHas('visitor', function (Builder $visitorQuery) use ($keyword): void {
                        $visitorQuery->where('full_name', 'like', "%{$keyword}%")
                            ->orWhere('company', 'like', "%{$keyword}%")
                            ->orWhere('phone', 'like', "%{$keyword}%")
                            ->orWhere('email', 'like', "%{$keyword}%");
                    })
                    ->orWhereHas('hostEmployee', function (Builder $hostQuery) use ($keyword): void {
                        $hostQuery->where('name', 'like', "%{$keyword}%");
                    });
            });
        }

        return $query
            ->orderByDesc('scheduled_at')
            ->orderByDesc('id');
    }

    private function baseVisitQuery(): Builder
    {
        return Visit::query()->with([
            'visitor',
            'hostEmployee.department',
            'department',
            'creator',
            'approval.approver',
            'activeBadge',
        ]);
    }
    /**
     * @return array<string, mixed>
     */
    private function adminVisitLiveState(): array
    {
        $totalVisits = Visit::query()->count();
        $latestVisitId = (int) (Visit::query()->max('id') ?? 0);
        $latestVisitUpdate = (string) (Visit::query()->max('updated_at') ?? '');
        $pendingApprovals = $this->scopeVisitsForApproval(
            $this->baseVisitQuery()->where('visits.status', 'pending')
        )->count();

        $version = sha1(implode('|', [
            $totalVisits,
            $latestVisitId,
            $latestVisitUpdate,
            $pendingApprovals,
        ]));

        return [
            'version' => $version,
            'total_visits' => $totalVisits,
            'latest_visit_id' => $latestVisitId,
            'latest_visit_update' => $latestVisitUpdate,
            'pending_approvals' => $pendingApprovals,
            'server_time' => now()->toIso8601String(),
        ];
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
        $validated = $request->validate([
            'existing_visitor_id' => ['nullable', 'exists:visitors,id'],
            'visitor_name' => ['required', 'string', 'max:120'],
            'visitor_phone' => ['nullable', 'string', 'max:30'],
            'visitor_email' => ['nullable', 'email', 'max:160'],
            'visitor_company' => ['nullable', 'string', 'max:160'],
            'visitor_identity_no' => ['nullable', 'string', 'max:80'],
            'visitor_id_card_number' => ['nullable', 'string', 'max:80'],
            'visitor_identity_issued_place' => ['nullable', 'string', 'max:160'],
            'visitor_identity_issued_date' => ['nullable', 'date', 'before_or_equal:today'],
            'visitor_note' => ['nullable', 'string', 'max:1000'],
            'host_employee_id' => ['nullable', 'exists:employees,id'],
            'host_name' => ['nullable', 'string', 'max:120'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'visit_date' => ['required', 'date'],
            'visit_time' => ['required', 'date_format:H:i'],
            'expected_checkout_time' => ['required', 'date_format:H:i'],
            'purpose' => ['required', 'string', 'max:255'],
            'access_zone' => ['nullable', 'string', 'max:120'],
            'checkin_method' => ['required', 'in:qr,badge,manual'],
        ]);

        $hostEmployee = null;
        if (filled($validated['host_employee_id'] ?? null)) {
            $hostEmployee = Employee::query()->with('department')->find((int) $validated['host_employee_id']);
        }

        if (blank($validated['host_name'] ?? null) && $hostEmployee !== null) {
            $validated['host_name'] = $hostEmployee->name;
        }

        if (blank($validated['department_id'] ?? null) && $hostEmployee?->department_id) {
            $validated['department_id'] = $hostEmployee->department_id;
        }

        if (blank($validated['host_name'] ?? null)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'host_name' => 'Vui long nhap nguoi tiep khach.',
            ]);
        }

        if (blank($validated['department_id'] ?? null)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'department_id' => 'Vui long chon phong ban.',
            ]);
        }

        return $validated;
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

    private function scopeVisitsForApproval(Builder $query): Builder
    {
        /** @var User|null $user */
        $user = auth()->user();
        if ($user === null) {
            return $query->whereRaw('1 = 0');
        }

        $user->loadMissing(['roles', 'employeeProfile']);

        if ($this->userCanManageAllApprovals($user)) {
            return $query;
        }

        if (! $this->userCanApproveOwnHostVisits($user)) {
            return $query->whereRaw('1 = 0');
        }

        $hostEmployeeIds = $this->hostEmployeeIdsForUser($user);
        if ($hostEmployeeIds === []) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn('host_employee_id', $hostEmployeeIds);
    }

    private function canActOnVisit(Visit $visit): bool
    {
        /** @var User|null $user */
        $user = auth()->user();
        if ($user === null) {
            return false;
        }

        $user->loadMissing(['roles', 'employeeProfile']);
        if ($this->userCanManageAllApprovals($user)) {
            return true;
        }

        if (! $this->userCanApproveOwnHostVisits($user)) {
            return false;
        }

        $hostEmployeeIds = $this->hostEmployeeIdsForUser($user);
        if ($hostEmployeeIds === []) {
            return false;
        }

        return in_array((int) $visit->host_employee_id, $hostEmployeeIds, true);
    }

    private function userCanManageAllApprovals(User $user): bool
    {
        $user->loadMissing('roles');

        return $user->roles->contains(fn ($role): bool => in_array($role->slug, ['super_admin', 'admin'], true));
    }

    private function userCanApproveOwnHostVisits(User $user): bool
    {
        $user->loadMissing('roles');

        return $user->roles->contains(fn ($role): bool => $role->slug === 'employee');
    }

    /**
     * @return array<int, int>
     */
    private function hostEmployeeIdsForUser(User $user): array
    {
        $email = trim((string) $user->email);

        return Employee::query()
            ->where(function (Builder $query) use ($user, $email): void {
                $query->where('user_id', $user->id);

                if ($email !== '') {
                    $query->orWhere('email', $email);
                }
            })
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function firstOrCreateVisitor(array $validated): Visitor
    {
        if (! empty($validated['existing_visitor_id'])) {
            $visitor = Visitor::query()->findOrFail((int) $validated['existing_visitor_id']);

            $visitor->update($this->visitorPayloadFromVisitForm($validated));

            return $visitor;
        }

        $visitor = null;

        if (! empty($validated['visitor_identity_no'])) {
            $visitor = Visitor::query()->where('identity_no', $validated['visitor_identity_no'])->first();
        }

        if ($visitor === null && ! empty($validated['visitor_phone'])) {
            $visitor = Visitor::query()->where('phone', $validated['visitor_phone'])->first();
        }

        if ($visitor === null && ! empty($validated['visitor_email'])) {
            $visitor = Visitor::query()->where('email', $validated['visitor_email'])->first();
        }

        if ($visitor === null) {
            return Visitor::query()->create($this->visitorPayloadFromVisitForm($validated));
        }

        $visitor->update($this->visitorPayloadFromVisitForm($validated));

        return $visitor;
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function visitorPayloadFromVisitForm(array $validated): array
    {
        return [
            'full_name' => $validated['visitor_name'],
            'phone' => $validated['visitor_phone'] ?? null,
            'email' => $validated['visitor_email'] ?? null,
            'company' => $validated['visitor_company'] ?? null,
            'identity_no' => $validated['visitor_identity_no'] ?? null,
            'visitor_id_card_number' => $validated['visitor_id_card_number'] ?? null,
            'identity_issued_place' => $validated['visitor_identity_issued_place'] ?? null,
            'identity_issued_date' => $validated['visitor_identity_issued_date'] ?? null,
            'note' => $validated['visitor_note'] ?? null,
        ];
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

        $visitor->update($this->visitorPayloadFromVisitForm($validated));

        return $visitor;
    }

    private function findActiveVisitForVisitor(?int $visitorId, int $excludeVisitId): ?Visit
    {
        if ($visitorId === null) {
            return null;
        }

        return Visit::query()
            ->where('visitor_id', $visitorId)
            ->where('id', '!=', $excludeVisitId)
            ->whereIn('status', ['pending', 'approved', 'checked_in'])
            ->orderByRaw("FIELD(status, 'approved', 'checked_in', 'pending')")
            ->orderByDesc('scheduled_at')
            ->first();
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
            $token = (string) random_int(10000000, 99999999);
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

    private function performCheckin(Visit $visit, bool $ignoreWindow = false): ?string
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

        $windowError = $ignoreWindow ? null : $this->checkinWindowError($visit);
        if ($windowError !== null) {
            return $windowError;
        }

        $checkedInAt = $visit->actual_checkin_at ?? now();
        $checkedIn = Visit::query()
            ->whereKey($visit->id)
            ->where('status', 'approved')
            ->update([
            'status' => 'checked_in',
            'actual_checkin_at' => $checkedInAt,
        ]);

        if ($checkedIn !== 1) {
            $visit->refresh();

            return "Lịch {$visit->code} đã được xử lý trước đó. Trạng thái hiện tại: {$this->visitStatusLabel($visit->status)}.";
        }

        $visit->refresh();

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

        $this->sendHostCheckinEmail($visit);

        return null;
    }

    private function kioskLobbyModeEnabled(): bool
    {
        $settings = SystemSetting::values(SystemSetting::kioskDefaults());

        return ($settings['kiosk.lobby_mode_enabled'] ?? '0') === '1';
    }

    private function checkinWindowError(Visit $visit): ?string
    {
        if ($visit->scheduled_at === null) {
            return null;
        }

        $settings = SystemSetting::values(SystemSetting::accessDefaults());
        $scheduledAt = $visit->scheduled_at->copy();
        $allowEarly = ($settings['access.allow_early_checkin'] ?? '1') === '1';
        $allowLate = ($settings['access.allow_late_checkin'] ?? '1') === '1';
        $earlyMinutes = max(0, (int) ($settings['access.early_checkin_minutes'] ?? 30));
        $lateMinutes = max(0, (int) ($settings['access.late_checkin_minutes'] ?? 60));
        $earliestAt = $allowEarly ? $scheduledAt->copy()->subMinutes($earlyMinutes) : $scheduledAt;
        $latestAt = $allowLate ? $scheduledAt->copy()->addMinutes($lateMinutes) : $scheduledAt;
        $message = null;

        if (now()->lt($earliestAt)) {
            $message = "Chưa đến giờ check-in. Lịch {$visit->code} được phép check-in từ {$earliestAt->format('H:i d/m/Y')}.";
        } elseif (now()->gt($latestAt)) {
            $message = "Đã quá giờ check-in. Lịch {$visit->code} chỉ được check-in đến {$latestAt->format('H:i d/m/Y')}.";
        }

        if ($message === null) {
            return null;
        }

        $warningEnabled = ($settings['access.warning_enabled'] ?? '1') === '1';
        $warningMessage = trim((string) ($settings['access.warning_message'] ?? ''));

        return $warningEnabled && $warningMessage !== ''
            ? "{$warningMessage} {$message}"
            : $message;
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

        $checkedOut = Visit::query()
            ->whereKey($visit->id)
            ->where('status', 'checked_in')
            ->update([
            'status' => 'checked_out',
            'actual_checkout_at' => now(),
        ]);

        if ($checkedOut !== 1) {
            $visit->refresh();

            return "Lịch {$visit->code} đã được xử lý trước đó. Trạng thái hiện tại: {$this->visitStatusLabel($visit->status)}.";
        }

        $visit->refresh();

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
                        'title' => "Khách trùng danh sách cảnh báo: {$visit->code}",
                        'message' => ($visit->visitor?->full_name ?? 'Khách').' trùng từ khóa cảnh báo "'.$watchlist->keyword.'". Vui lòng kiểm tra trước khi xử lý.',
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
                    'title' => "Khách quá giờ: {$visit->code}",
                    'message' => ($visit->visitor?->full_name ?? 'Khách').' chưa check-out sau giờ dự kiến. Vui lòng liên hệ người tiếp.',
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
                    'title' => "Chưa check-in: {$visit->code}",
                    'message' => ($visit->visitor?->full_name ?? 'Khách').' đã được duyệt nhưng chưa làm thủ tục vào.',
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
                    'title' => "Sắp đến giờ hẹn: {$visit->code}",
                    'message' => 'Lịch hẹn sắp đến giờ nhưng vẫn đang chờ phê duyệt.',
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
        $validated = $request->validate([
            'visitor_id' => ['nullable', 'exists:visitors,id'],
            'keyword' => ['required', 'string', 'max:160'],
            'match_type' => ['required', 'in:any,name,phone,email,company,identity_no'],
            'level' => ['required', 'in:warning,critical'],
            'status' => ['required', 'in:active,inactive'],
            'reason' => ['required', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:2000'],
        ]);

        return $validated;
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

    private function sendHostCheckinEmail(Visit $visit): void
    {
        if (! DynamicMailSettings::triggerEnabled('mail.trigger_host_checkin')) {
            return;
        }

        $visit->refresh()->loadMissing(['visitor', 'hostEmployee.user', 'hostEmployee.department']);

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

            $this->logAudit('visit.host_checkin_email_sent', 'visit', (string) $visit->id, [
                'code' => $visit->code,
                'email' => $email,
            ]);
        } catch (\Throwable $exception) {
            $this->logAudit('visit.host_checkin_email_failed', 'visit', (string) $visit->id, [
                'code' => $visit->code,
                'email' => $email,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    private function sendQrEmailToVisitor(Visit $visit, bool $respectTrigger = true): ?string
    {
        if ($respectTrigger && ! DynamicMailSettings::triggerEnabled('mail.trigger_qr_approved')) {
            return null;
        }

        $visit->loadMissing(['visitor', 'hostEmployee.department']);

        $email = trim((string) ($visit->visitor?->email ?? ''));
        if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return null;
        }

        if (! $visit->qr_token) {
            $baseTime = $visit->scheduled_at ?? now();
            if ($baseTime->lt(now())) {
                $baseTime = now();
            }

            $visit->update([
                'qr_token' => $this->generateQrToken(),
                'qr_expires_at' => $visit->qr_expires_at ?? $baseTime->copy()->addDay(),
            ]);
        }

        $qrSvg = (string) \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
            ->size(180)
            ->margin(1)
            ->errorCorrection('M')
            ->generate($visit->qr_token);
        $subject = 'Mã QR lịch hẹn '.$visit->code;
        $mailSettings = DynamicMailSettings::apply();
        $html = view('emails.visit-qr', [
            'visit' => $visit,
            'qrSvg' => $qrSvg,
            'statusUrl' => route('kiosk.checkin.status', $visit),
            'mailBrandName' => $mailSettings['mail.from_name'] ?: 'VMS Kiosk',
        ])->render();

        Mail::html($html, function ($message) use ($email, $subject, $mailSettings): void {
            $message->to($email)->subject($subject);
            if (! empty($mailSettings['mail.reply_to'])) {
                $message->replyTo($mailSettings['mail.reply_to']);
            }
        });

        $this->logAudit('visit.qr_emailed', 'visit', (string) $visit->id, [
            'code' => $visit->code,
            'email' => $email,
        ]);

        return $email;
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

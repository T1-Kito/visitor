<?php

namespace Database\Seeders;

use App\Models\Approval;
use App\Models\Badge;
use App\Models\AccessControlLog;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Permission;
use App\Models\Notification;
use App\Models\Role;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\Visit;
use App\Models\Visitor;
use App\Models\Watchlist;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class VmsSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::transaction(function (): void {
            $roles = $this->seedRolesAndPermissions();
            $users = $this->seedUsers($roles);
            $departments = $this->seedDepartments();
            $employees = $this->seedEmployees($users, $departments);
            $visitors = $this->seedVisitors();

            $this->seedVisitsAndApprovals($employees, $visitors, $users);
            $this->seedDemoOperations($employees, $visitors, $users);
            $this->seedWatchlists($visitors, $users);
            $this->seedNotifications($users);
            $this->seedKioskSettings();
        });
    }

    /**
     * @return Collection<string, Role>
     */
    private function seedRolesAndPermissions(): Collection
    {
        $roles = collect([
            ['name' => 'Super Admin', 'slug' => 'super_admin'],
            ['name' => 'Admin', 'slug' => 'admin'],
            ['name' => 'Le tan', 'slug' => 'receptionist'],
            ['name' => 'Bao ve', 'slug' => 'guard'],
            ['name' => 'Host', 'slug' => 'employee'],
            ['name' => 'Quan ly phong ban', 'slug' => 'department_manager'],
            ['name' => 'An ninh/Hanh chinh', 'slug' => 'security_admin'],
        ])->mapWithKeys(function (array $role): array {
            $model = Role::query()->updateOrCreate(
                ['slug' => $role['slug']],
                ['name' => $role['name']]
            );

            return [$role['slug'] => $model];
        });

        $permissions = collect([
            ['name' => 'Xem dashboard', 'slug' => 'dashboard.view'],
            ['name' => 'Quan ly lich hen', 'slug' => 'visits.manage'],
            ['name' => 'Phe duyet lich hen', 'slug' => 'approvals.manage'],
            ['name' => 'Checkin/Checkout', 'slug' => 'checkin.manage'],
            ['name' => 'Xuat bao cao', 'slug' => 'reports.export'],
            ['name' => 'Quan tri he thong', 'slug' => 'system.manage'],
            ['name' => 'Quan ly phong ban', 'slug' => 'departments.manage'],
            ['name' => 'Quan ly nhan vien', 'slug' => 'employees.manage'],
            ['name' => 'Quan ly khach', 'slug' => 'visitors.manage'],
            ['name' => 'Quan ly badge', 'slug' => 'badges.manage'],
            ['name' => 'Xem canh bao', 'slug' => 'alerts.view'],
        ])->mapWithKeys(function (array $permission): array {
            $model = Permission::query()->updateOrCreate(
                ['slug' => $permission['slug']],
                ['name' => $permission['name']]
            );

            return [$permission['slug'] => $model];
        });

        $rolePermissionMap = [
            'super_admin' => $permissions->pluck('id')->all(),
            'admin' => $permissions->pluck('id')->all(),
            'receptionist' => [
                $permissions['dashboard.view']->id,
                $permissions['visits.manage']->id,
                $permissions['checkin.manage']->id,
                $permissions['visitors.manage']->id,
            ],
            'guard' => [
                $permissions['dashboard.view']->id,
                $permissions['checkin.manage']->id,
                $permissions['badges.manage']->id,
            ],
            'employee' => [
                $permissions['dashboard.view']->id,
                $permissions['visits.manage']->id,
                $permissions['approvals.manage']->id,
            ],
            'department_manager' => [
                $permissions['dashboard.view']->id,
                $permissions['approvals.manage']->id,
                $permissions['reports.export']->id,
                $permissions['alerts.view']->id,
            ],
            'security_admin' => [
                $permissions['dashboard.view']->id,
                $permissions['checkin.manage']->id,
                $permissions['reports.export']->id,
                $permissions['badges.manage']->id,
                $permissions['alerts.view']->id,
            ],
        ];

        foreach ($rolePermissionMap as $roleSlug => $permissionIds) {
            $roles[$roleSlug]->permissions()->sync($permissionIds);
        }

        return $roles;
    }

    /**
     * @param  Collection<string, Role>  $roles
     * @return Collection<string, User>
     */
    private function seedUsers(Collection $roles): Collection
    {
        $users = collect([
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@company.local',
                'password' => Hash::make('Admin@123'),
                'is_active' => true,
                'role' => 'super_admin',
            ],
            [
                'name' => 'Admin',
                'email' => 'admin@company.local',
                'password' => Hash::make('Admin@123'),
                'is_active' => true,
                'role' => 'admin',
            ],
            [
                'name' => 'Le Tan 1',
                'email' => 'reception1@company.local',
                'password' => Hash::make('Admin@123'),
                'is_active' => true,
                'role' => 'receptionist',
            ],
            [
                'name' => 'Bao Ve 1',
                'email' => 'guard1@company.local',
                'password' => Hash::make('Admin@123'),
                'is_active' => true,
                'role' => 'guard',
            ],
            [
                'name' => 'Nguyen Minh Anh',
                'email' => 'employee1@company.local',
                'password' => Hash::make('Admin@123'),
                'is_active' => true,
                'role' => 'employee',
            ],
            [
                'name' => 'Le Thu Trang',
                'email' => 'employee2@company.local',
                'password' => Hash::make('Admin@123'),
                'is_active' => true,
                'role' => 'employee',
            ],
            [
                'name' => 'Tran Quoc Bao',
                'email' => 'employee3@company.local',
                'password' => Hash::make('Admin@123'),
                'is_active' => true,
                'role' => 'employee',
            ],
            [
                'name' => 'Pham Hong Son',
                'email' => 'employee4@company.local',
                'password' => Hash::make('Admin@123'),
                'is_active' => true,
                'role' => 'employee',
            ],
            [
                'name' => 'Manager Sales',
                'email' => 'manager1@company.local',
                'password' => Hash::make('Admin@123'),
                'is_active' => true,
                'role' => 'department_manager',
            ],
            [
                'name' => 'Security Admin',
                'email' => 'security.admin@company.local',
                'password' => Hash::make('Admin@123'),
                'is_active' => true,
                'role' => 'security_admin',
            ],
        ])->mapWithKeys(function (array $data) use ($roles): array {
            $roleSlug = $data['role'];
            unset($data['role']);

            $user = User::query()->updateOrCreate(
                ['email' => $data['email']],
                $data
            );

            $user->roles()->sync([$roles[$roleSlug]->id]);

            return [$user->email => $user];
        });

        return $users;
    }

    /**
     * @return Collection<string, Department>
     */
    private function seedDepartments(): Collection
    {
        return collect([
            ['code' => 'SALES', 'name' => 'Sales'],
            ['code' => 'OPS', 'name' => 'Operations'],
            ['code' => 'IT', 'name' => 'IT'],
            ['code' => 'FIN', 'name' => 'Finance'],
        ])->mapWithKeys(function (array $data): array {
            $department = Department::query()->updateOrCreate(
                ['code' => $data['code']],
                ['name' => $data['name']]
            );

            return [$department->code => $department];
        });
    }

    /**
     * @param  Collection<string, User>  $users
     * @param  Collection<string, Department>  $departments
     * @return Collection<string, Employee>
     */
    private function seedEmployees(Collection $users, Collection $departments): Collection
    {
        return collect([
            [
                'name' => 'Nguyen Minh Anh',
                'email' => 'employee1@company.local',
                'department' => 'SALES',
                'job_title' => 'Sales Executive',
                'phone' => '0901000001',
                'user_email' => 'employee1@company.local',
            ],
            [
                'name' => 'Le Thu Trang',
                'email' => 'employee2@company.local',
                'department' => 'OPS',
                'job_title' => 'Operations Lead',
                'phone' => '0901000002',
                'user_email' => 'employee2@company.local',
            ],
            [
                'name' => 'Tran Quoc Bao',
                'email' => 'employee3@company.local',
                'department' => 'FIN',
                'job_title' => 'Finance Manager',
                'phone' => '0901000003',
                'user_email' => 'employee3@company.local',
            ],
            [
                'name' => 'Pham Hong Son',
                'email' => 'employee4@company.local',
                'department' => 'IT',
                'job_title' => 'IT Supervisor',
                'phone' => '0901000004',
                'user_email' => 'employee4@company.local',
            ],
            [
                'name' => 'Manager Sales',
                'email' => 'manager1@company.local',
                'department' => 'SALES',
                'job_title' => 'Sales Manager',
                'phone' => '0901000005',
                'user_email' => 'manager1@company.local',
            ],
            [
                'name' => 'Security Admin',
                'email' => 'security.admin@company.local',
                'department' => 'OPS',
                'job_title' => 'Security Operations',
                'phone' => '0901000006',
                'user_email' => 'security.admin@company.local',
            ],
        ])->mapWithKeys(function (array $data) use ($users, $departments): array {
            $departmentCode = $data['department'];
            $userEmail = $data['user_email'];
            unset($data['department'], $data['user_email']);

            $employee = Employee::query()->updateOrCreate(
                ['email' => $data['email']],
                array_merge($data, [
                    'department_id' => $departments[$departmentCode]->id,
                    'user_id' => $users[$userEmail]->id ?? null,
                ])
            );

            return [$employee->name => $employee];
        });
    }

    /**
     * @return Collection<string, Visitor>
     */
    private function seedVisitors(): Collection
    {
        return collect([
            ['full_name' => 'Nguyen Van Long', 'phone' => '0911001001', 'company' => 'ABC Logistics'],
            ['full_name' => 'Pham Thi Lan', 'phone' => '0911001002', 'company' => 'Delta Supplies'],
            ['full_name' => 'Tran Quoc Dung', 'phone' => '0911001003', 'company' => 'Techview'],
            ['full_name' => 'Le Phuong Anh', 'phone' => '0911001004', 'company' => 'Zen Contracting'],
            ['full_name' => 'Ngo Gia Huy', 'phone' => '0911001005', 'company' => 'Mekong Global'],
            ['full_name' => 'Doan Minh Tam', 'phone' => '0911001006', 'company' => 'Northline'],
            ['full_name' => 'Bui Thanh Tuan', 'phone' => '0911001007', 'company' => 'CPS Service'],
            ['full_name' => 'Hoang My Linh', 'phone' => '0911001008', 'company' => 'Finance Plus'],
            ['full_name' => 'Vo Thai Nam', 'phone' => '0911001009', 'company' => 'Greenway'],
            ['full_name' => 'Mai Thu Ha', 'phone' => '0911001010', 'company' => 'Supply Hub'],
            ['full_name' => 'Kito Nguyen', 'phone' => '0879774476', 'email' => 'kito@example.com', 'company' => 'Vigilance'],
            ['full_name' => 'Dang Hoan Thang', 'phone' => '0912223333', 'email' => 'thang@example.com', 'company' => 'Demo Corp'],
            ['full_name' => 'Doan Hoai Nam', 'phone' => '0913334444', 'email' => 'nam@example.com', 'company' => 'Northline'],
            ['full_name' => 'Sarah Lee', 'phone' => '0914445555', 'email' => 'sarah.lee@example.com', 'company' => 'Global Industry'],
            ['full_name' => 'David Chen', 'phone' => '0915556666', 'email' => 'david.chen@example.com', 'company' => 'Samsung'],
            ['full_name' => 'John Smith', 'phone' => '0916667777', 'email' => 'john.smith@example.com', 'company' => 'Microsoft'],
        ])->mapWithKeys(function (array $data): array {
            $visitor = Visitor::query()->updateOrCreate(
                ['phone' => $data['phone']],
                $data
            );

            return [$visitor->full_name => $visitor];
        });
    }

    /**
     * @param  Collection<string, Employee>  $employees
     * @param  Collection<string, Visitor>  $visitors
     * @param  Collection<string, User>  $users
     */
    private function seedVisitsAndApprovals(Collection $employees, Collection $visitors, Collection $users): void
    {
        $today = Carbon::today();
        $approverId = $users['admin@company.local']->id ?? null;
        $badgeIndex = 1;

        $visits = [
            ['code' => 'VO-MN-2201', 'visitor' => 'Nguyen Van Long', 'host' => 'Nguyen Minh Anh', 'time' => '09:00', 'status' => 'checked_in', 'purpose' => 'Hop ban giao du an'],
            ['code' => 'VO-MN-2202', 'visitor' => 'Pham Thi Lan', 'host' => 'Le Thu Trang', 'time' => '09:30', 'status' => 'approved', 'purpose' => 'Lam viec nha cung cap'],
            ['code' => 'VO-MN-2203', 'visitor' => 'Tran Quoc Dung', 'host' => 'Pham Hong Son', 'time' => '10:00', 'status' => 'pending', 'purpose' => 'Demo thiet bi'],
            ['code' => 'VO-MN-2204', 'visitor' => 'Le Phuong Anh', 'host' => 'Tran Quoc Bao', 'time' => '10:45', 'status' => 'rejected', 'purpose' => 'Trinh ky hop dong'],
            ['code' => 'VO-MN-2205', 'visitor' => 'Ngo Gia Huy', 'host' => 'Nguyen Minh Anh', 'time' => '11:10', 'status' => 'checked_out', 'purpose' => 'Lam viec truoc giao hang'],
            ['code' => 'VO-MN-2206', 'visitor' => 'Doan Minh Tam', 'host' => 'Le Thu Trang', 'time' => '11:40', 'status' => 'pending', 'purpose' => 'Gap doi van hanh'],
            ['code' => 'VO-MN-2207', 'visitor' => 'Bui Thanh Tuan', 'host' => 'Pham Hong Son', 'time' => '13:20', 'status' => 'checked_in', 'purpose' => 'Bao tri he thong'],
            ['code' => 'VO-MN-2208', 'visitor' => 'Hoang My Linh', 'host' => 'Tran Quoc Bao', 'time' => '14:00', 'status' => 'checked_out', 'purpose' => 'Doi soat thanh toan'],
            ['code' => 'VO-MN-2209', 'visitor' => 'Vo Thai Nam', 'host' => 'Nguyen Minh Anh', 'time' => '15:10', 'status' => 'approved', 'purpose' => 'Ky hop dong mua ban'],
            ['code' => 'VO-MN-2210', 'visitor' => 'Mai Thu Ha', 'host' => 'Le Thu Trang', 'time' => '16:00', 'status' => 'pending', 'purpose' => 'Danh gia quy trinh kho'],
        ];

        foreach ($visits as $index => $data) {
            $scheduledAt = $today->copy()->setTimeFromTimeString($data['time']);
            $expectedCheckoutAt = $scheduledAt->copy()->addHours(2);
            $actualCheckinAt = null;
            $actualCheckoutAt = null;
            $rejectionReason = null;

            if ($data['status'] === 'checked_in') {
                $actualCheckinAt = $scheduledAt->copy()->addMinutes(7);
            }

            if ($data['status'] === 'checked_out') {
                $actualCheckinAt = $scheduledAt->copy()->addMinutes(5);
                $actualCheckoutAt = $scheduledAt->copy()->addMinutes(95);
            }

            if ($data['status'] === 'rejected') {
                $rejectionReason = 'Khong phu hop lich tiep khach cua phong ban.';
            }

            $visit = Visit::query()->updateOrCreate(
                ['code' => $data['code']],
                [
                    'visitor_id' => $visitors[$data['visitor']]->id,
                    'host_employee_id' => $employees[$data['host']]->id,
                    'scheduled_at' => $scheduledAt,
                    'expected_checkout_at' => $expectedCheckoutAt,
                    'actual_checkin_at' => $actualCheckinAt,
                    'actual_checkout_at' => $actualCheckoutAt,
                    'status' => $data['status'],
                    'purpose' => $data['purpose'],
                    'access_zone' => $index % 2 === 0 ? 'Tang 2 - Van phong kinh doanh' : 'Tang 3 - Khu ky thuat',
                    'checkin_method' => 'qr',
                    'qr_token' => sprintf('260529%02d', $index + 1),
                    'qr_expires_at' => $scheduledAt->copy()->addDay(),
                    'rejection_reason' => $rejectionReason,
                ]
            );

            $approvalStatus = match ($data['status']) {
                'pending' => 'pending',
                'rejected' => 'rejected',
                default => 'approved',
            };

            Approval::query()->updateOrCreate(
                ['visit_id' => $visit->id],
                [
                    'approver_user_id' => $approvalStatus === 'pending' ? null : $approverId,
                    'status' => $approvalStatus,
                    'note' => $approvalStatus === 'rejected'
                        ? 'Khung gio khong kha dung.'
                        : ($approvalStatus === 'approved' ? 'Da duyet lich tiep don.' : null),
                    'acted_at' => $approvalStatus === 'pending' ? null : $scheduledAt->copy()->subMinutes(30),
                ]
            );

            if (in_array($data['status'], ['checked_in', 'checked_out'], true)) {
                $badge = Badge::query()->updateOrCreate(
                    ['badge_no' => 'B-'.str_pad((string) $badgeIndex, 3, '0', STR_PAD_LEFT)],
                    [
                        'visit_id' => $visit->id,
                        'status' => $data['status'] === 'checked_in' ? 'active' : 'revoked',
                        'issued_at' => $actualCheckinAt,
                        'revoked_at' => $actualCheckoutAt,
                        'valid_until' => $expectedCheckoutAt,
                    ]
                );

                AccessControlLog::query()->updateOrCreate(
                    [
                        'visit_id' => $visit->id,
                        'badge_id' => $badge->id,
                        'event' => 'CHECK_IN',
                    ],
                    [
                        'source' => 'seed',
                        'meta' => ['badge_no' => $badge->badge_no],
                    ]
                );

                if ($data['status'] === 'checked_out') {
                    AccessControlLog::query()->updateOrCreate(
                        [
                            'visit_id' => $visit->id,
                            'badge_id' => $badge->id,
                            'event' => 'CHECK_OUT',
                        ],
                        [
                            'source' => 'seed',
                            'meta' => ['badge_no' => $badge->badge_no],
                        ]
                    );
                }

                $badgeIndex++;
            }
        }

        for ($i = $badgeIndex; $i <= 12; $i++) {
            Badge::query()->updateOrCreate(
                ['badge_no' => 'B-'.str_pad((string) $i, 3, '0', STR_PAD_LEFT)],
                [
                    'visit_id' => null,
                    'status' => 'available',
                    'issued_at' => null,
                    'revoked_at' => null,
                    'valid_until' => null,
                ]
            );
        }
    }

    /**
     * @param  Collection<string, Employee>  $employees
     * @param  Collection<string, Visitor>  $visitors
     * @param  Collection<string, User>  $users
     */
    private function seedDemoOperations(Collection $employees, Collection $visitors, Collection $users): void
    {
        $adminId = $users['admin@company.local']->id ?? null;
        $receptionId = $users['reception1@company.local']->id ?? null;
        $today = Carbon::today();

        $demoVisits = [
            ['code' => 'WK-260529-001', 'visitor' => 'Dang Hoan Thang', 'host' => 'Tran Quoc Bao', 'day' => 0, 'time' => '08:45', 'status' => 'checked_in', 'purpose' => 'Demo san pham', 'zone' => 'Tang 2 - Phong Finance'],
            ['code' => 'WK-260529-002', 'visitor' => 'Kito Nguyen', 'host' => 'Tran Quoc Bao', 'day' => 0, 'time' => '09:15', 'status' => 'approved', 'purpose' => 'Hop trien khai dich vu', 'zone' => 'Tang 2 - Phong Finance'],
            ['code' => 'VO-260529-003', 'visitor' => 'John Smith', 'host' => 'Pham Hong Son', 'day' => 0, 'time' => '10:30', 'status' => 'pending', 'purpose' => 'Hop chien luoc doi tac', 'zone' => 'Tang 4 - Khu IT'],
            ['code' => 'VO-260529-004', 'visitor' => 'Sarah Lee', 'host' => 'Le Thu Trang', 'day' => 0, 'time' => '13:30', 'status' => 'approved', 'purpose' => 'Danh gia quy trinh van hanh', 'zone' => 'Tang 3 - Operations'],
            ['code' => 'VO-260529-005', 'visitor' => 'David Chen', 'host' => 'Manager Sales', 'day' => 0, 'time' => '14:15', 'status' => 'rejected', 'purpose' => 'Trao doi hop dong', 'zone' => 'Tang 1 - Le tan'],
        ];

        $historyVisitors = [
            'Nguyen Van Long',
            'Pham Thi Lan',
            'Tran Quoc Dung',
            'Le Phuong Anh',
            'Ngo Gia Huy',
            'Doan Minh Tam',
            'Bui Thanh Tuan',
            'Hoang My Linh',
            'Vo Thai Nam',
            'Mai Thu Ha',
            'Doan Hoai Nam',
            'Sarah Lee',
        ];
        $historyHosts = ['Nguyen Minh Anh', 'Le Thu Trang', 'Tran Quoc Bao', 'Pham Hong Son'];
        $historyStatuses = ['checked_out', 'checked_out', 'checked_out', 'approved', 'pending', 'rejected'];
        $purposes = [
            'Hop du an',
            'Lam viec nha cung cap',
            'Bao tri thiet bi',
            'Doi soat thanh toan',
            'Dao tao noi bo',
            'Trao doi hop dong',
        ];

        for ($day = 1; $day <= 12; $day++) {
            for ($slot = 1; $slot <= 3; $slot++) {
                $index = (($day - 1) * 3) + $slot;
                $demoVisits[] = [
                    'code' => 'RP-'.$today->copy()->subDays($day)->format('ymd').'-'.str_pad((string) $slot, 2, '0', STR_PAD_LEFT),
                    'visitor' => $historyVisitors[$index % count($historyVisitors)],
                    'host' => $historyHosts[$index % count($historyHosts)],
                    'day' => -$day,
                    'time' => ['08:30', '10:00', '14:00'][$slot - 1],
                    'status' => $historyStatuses[$index % count($historyStatuses)],
                    'purpose' => $purposes[$index % count($purposes)],
                    'zone' => ['Tang 1 - Le tan', 'Tang 2 - Van phong', 'Tang 3 - Phong hop'][$slot - 1],
                ];
            }
        }

        foreach ($demoVisits as $index => $data) {
            $scheduledAt = $today->copy()
                ->addDays($data['day'])
                ->setTimeFromTimeString($data['time']);
            $expectedCheckoutAt = $scheduledAt->copy()->addHours(2);
            $actualCheckinAt = null;
            $actualCheckoutAt = null;
            $rejectionReason = null;

            if ($data['status'] === 'checked_in') {
                $actualCheckinAt = $scheduledAt->copy()->addMinutes(8);
                if ($scheduledAt->isToday() && $scheduledAt->hour < 10) {
                    $expectedCheckoutAt = Carbon::now()->subMinutes(30);
                }
            }

            if ($data['status'] === 'checked_out') {
                $actualCheckinAt = $scheduledAt->copy()->addMinutes(5);
                $actualCheckoutAt = $scheduledAt->copy()->addMinutes(95 + ($index % 40));
            }

            if ($data['status'] === 'rejected') {
                $rejectionReason = 'Lich tiep khach khong phu hop thoi gian hien tai.';
            }

            $visit = Visit::query()->updateOrCreate(
                ['code' => $data['code']],
                [
                    'visitor_id' => $visitors[$data['visitor']]->id,
                    'host_employee_id' => $employees[$data['host']]->id,
                    'scheduled_at' => $scheduledAt,
                    'expected_checkout_at' => $expectedCheckoutAt,
                    'actual_checkin_at' => $actualCheckinAt,
                    'actual_checkout_at' => $actualCheckoutAt,
                    'status' => $data['status'],
                    'purpose' => $data['purpose'],
                    'access_zone' => $data['zone'],
                    'checkin_method' => 'qr',
                    'qr_token' => sprintf('860601%02d', $index + 1),
                    'qr_expires_at' => $scheduledAt->copy()->addDay(),
                    'rejection_reason' => $rejectionReason,
                ]
            );

            $approvalStatus = match ($data['status']) {
                'pending' => 'pending',
                'rejected' => 'rejected',
                default => 'approved',
            };

            Approval::query()->updateOrCreate(
                ['visit_id' => $visit->id],
                [
                    'approver_user_id' => $approvalStatus === 'pending' ? null : $adminId,
                    'status' => $approvalStatus,
                    'note' => match ($approvalStatus) {
                        'approved' => 'Da duyet lich tiep khach.',
                        'rejected' => 'Tu choi do khong phu hop lich tiep khach.',
                        default => null,
                    },
                    'acted_at' => $approvalStatus === 'pending' ? null : $scheduledAt->copy()->subMinutes(25),
                ]
            );

            if (in_array($data['status'], ['checked_in', 'checked_out'], true)) {
                $badge = Badge::query()->updateOrCreate(
                    ['badge_no' => 'D-'.str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT)],
                    [
                        'visit_id' => $visit->id,
                        'status' => $data['status'] === 'checked_in' ? 'active' : 'revoked',
                        'issued_at' => $actualCheckinAt,
                        'revoked_at' => $actualCheckoutAt,
                        'valid_until' => $expectedCheckoutAt,
                    ]
                );

                AccessControlLog::query()->updateOrCreate(
                    [
                        'visit_id' => $visit->id,
                        'badge_id' => $badge->id,
                        'event' => 'CHECK_IN',
                    ],
                    [
                        'source' => 'seed',
                        'meta' => [
                            'badge_no' => $badge->badge_no,
                            'operator_user_id' => $receptionId,
                        ],
                    ]
                );

                if ($data['status'] === 'checked_out') {
                    AccessControlLog::query()->updateOrCreate(
                        [
                            'visit_id' => $visit->id,
                            'badge_id' => $badge->id,
                            'event' => 'CHECK_OUT',
                        ],
                        [
                            'source' => 'seed',
                            'meta' => [
                                'badge_no' => $badge->badge_no,
                                'operator_user_id' => $receptionId,
                            ],
                        ]
                    );
                }
            }
        }
    }

    /**
     * @param  Collection<string, Visitor>  $visitors
     * @param  Collection<string, User>  $users
     */
    private function seedWatchlists(Collection $visitors, Collection $users): void
    {
        $creatorId = $users['security.admin@company.local']->id ?? $users['admin@company.local']->id ?? null;

        $items = [
            [
                'visitor' => 'John Smith',
                'keyword' => 'John Smith',
                'match_type' => 'name',
                'level' => 'critical',
                'reason' => 'VIP/doi tac quan trong, can bao an ninh xac nhan truoc khi cho vao.',
                'note' => 'Thong bao cho quan ly an ninh khi khach den kiosk hoac le tan.',
            ],
            [
                'visitor' => 'David Chen',
                'keyword' => 'Samsung',
                'match_type' => 'company',
                'level' => 'warning',
                'reason' => 'Doi tac can tiep don theo quy trinh rieng.',
                'note' => 'Le tan kiem tra lich hen va thong bao nguoi tiep.',
            ],
            [
                'visitor' => 'Kito Nguyen',
                'keyword' => '0879774476',
                'match_type' => 'phone',
                'level' => 'info',
                'reason' => 'Khach demo thuong xuyen, dung de test watchlist.',
                'note' => 'Du lieu mau cho man hinh danh sach canh bao.',
            ],
        ];

        foreach ($items as $item) {
            Watchlist::query()->updateOrCreate(
                [
                    'keyword' => $item['keyword'],
                    'match_type' => $item['match_type'],
                ],
                [
                    'visitor_id' => $visitors[$item['visitor']]->id ?? null,
                    'created_by_user_id' => $creatorId,
                    'level' => $item['level'],
                    'status' => 'active',
                    'reason' => $item['reason'],
                    'note' => $item['note'],
                ]
            );
        }
    }

    /**
     * @param  Collection<string, User>  $users
     */
    private function seedNotifications(Collection $users): void
    {
        $visitPending = Visit::query()->where('status', 'pending')->orderBy('scheduled_at')->first();
        $overstay = Visit::query()
            ->where('status', 'checked_in')
            ->whereNotNull('expected_checkout_at')
            ->where('expected_checkout_at', '<', Carbon::now())
            ->orderBy('expected_checkout_at')
            ->first();

        $targets = [
            'superadmin@company.local',
            'admin@company.local',
            'reception1@company.local',
            'security.admin@company.local',
        ];

        foreach ($targets as $email) {
            if (! isset($users[$email])) {
                continue;
            }

            $user = $users[$email];
            $items = [
                [
                    'type' => 'visit.pending',
                    'level' => 'warning',
                    'title' => 'Co yeu cau dang cho duyet',
                    'message' => 'Mot so lich hen moi dang cho nhan vien tiep khach xu ly.',
                    'entity_type' => 'visit',
                    'entity_id' => $visitPending?->id,
                    'action_url' => '/approvals',
                    'data' => ['code' => $visitPending?->code],
                ],
                [
                    'type' => 'visit.overstay',
                    'level' => 'danger',
                    'title' => 'Co khach qua gio',
                    'message' => 'He thong phat hien khach dang trong cong ty qua thoi gian du kien.',
                    'entity_type' => 'visit',
                    'entity_id' => $overstay?->id,
                    'action_url' => '/alerts',
                    'data' => ['code' => $overstay?->code],
                ],
                [
                    'type' => 'watchlist.match',
                    'level' => 'info',
                    'title' => 'Co khach trong danh sach canh bao',
                    'message' => 'Khi khach thuoc watchlist den, le tan can thong bao an ninh.',
                    'entity_type' => 'watchlist',
                    'entity_id' => null,
                    'action_url' => '/watchlists',
                    'data' => ['source' => 'demo'],
                ],
            ];

            foreach ($items as $item) {
                Notification::query()->updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'type' => $item['type'],
                        'title' => $item['title'],
                    ],
                    [
                        'level' => $item['level'],
                        'message' => $item['message'],
                        'entity_type' => $item['entity_type'],
                        'entity_id' => $item['entity_id'],
                        'action_url' => $item['action_url'],
                        'data' => $item['data'],
                        'read_at' => null,
                    ]
                );
            }
        }
    }

    private function seedKioskSettings(): void
    {
        SystemSetting::putMany([
            'kiosk.company_name' => 'Công ty ABC',
            'kiosk.system_name' => 'VMS Kiosk',
            'kiosk.subtitle' => 'Giao diện tự động cho khách đến công ty',
            'kiosk.welcome_title' => 'Chào mừng bạn đến Công ty ABC',
            'kiosk.welcome_description' => 'Vui lòng đăng ký thông tin hoặc check-in bằng QR để được hỗ trợ nhanh chóng.',
            'kiosk.hotline' => '1900 0000',
            'kiosk.working_hours' => '07:30 - 18:00',
            'kiosk.logo_url' => null,
            'kiosk.background_url' => null,
            'kiosk.primary_color' => '#0f6eea',
        ]);
    }
}

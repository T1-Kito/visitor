<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HasAdminLayoutData;
use App\Models\AuditLog;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Visitor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CatalogController extends Controller
{
    use HasAdminLayoutData;

    public function departmentsIndex(Request $request): View
    {
        $keyword = trim((string) $request->input('q', ''));
        $query = Department::query()->withCount('employees')->orderBy('name');

        if ($keyword !== '') {
            $query->where(function ($departmentQuery) use ($keyword): void {
                $departmentQuery
                    ->where('code', 'like', '%'.$keyword.'%')
                    ->orWhere('name', 'like', '%'.$keyword.'%');
            });
        }

        return view('admin.departments.index', $this->withBase([
            'departments' => $query->get(),
            'filters' => ['q' => $keyword],
        ]));
    }

    public function departmentsStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
        ]);

        $validated['code'] = $this->generateDepartmentCode($validated['name']);
        $department = Department::query()->create($validated);
        $this->logAudit('department.created', 'department', (string) $department->id, $validated);

        return redirect()->back()->with('status', 'Da tao phong ban moi.');
    }

    public function departmentsShow(Department $department): View
    {
        $department->loadCount('employees');
        $department->load([
            'employees' => fn ($query) => $query->withCount('hostedVisits')->orderBy('name'),
        ]);

        return view('admin.departments.show', $this->withBase([
            'department' => $department,
        ]));
    }

    public function departmentsEdit(Department $department): View
    {
        return view('admin.departments.edit', $this->withBase([
            'department' => $department,
        ]));
    }

    public function departmentsUpdate(Request $request, Department $department): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
        ]);

        $validated['code'] = $this->generateDepartmentCode($validated['name'], $department->id);
        $department->update($validated);
        $this->logAudit('department.updated', 'department', (string) $department->id, $validated);

        return redirect()
            ->route('admin.departments.show', $department)
            ->with('status', 'Da cap nhat phong ban.');
    }

    public function departmentsDestroy(Department $department): RedirectResponse
    {
        if ($department->employees()->exists()) {
            return redirect()
                ->back()
                ->with('error', 'Khong the xoa phong ban dang co nhan vien. Hay chuyen nhan vien sang phong ban khac truoc.');
        }

        $departmentId = (string) $department->id;
        $departmentCode = $department->code;
        $department->delete();

        $this->logAudit('department.deleted', 'department', $departmentId, ['code' => $departmentCode]);

        return redirect()
            ->route('admin.departments.index')
            ->with('status', 'Da xoa phong ban.');
    }

    public function employeesIndex(Request $request): View
    {
        $keyword = trim((string) $request->input('q', ''));
        $departmentId = $request->input('department_id', 'all');

        $query = Employee::query()
            ->with('department')
            ->withCount('hostedVisits')
            ->orderBy('name');

        if ($keyword !== '') {
            $query->where(function ($employeeQuery) use ($keyword): void {
                $employeeQuery
                    ->where('name', 'like', '%'.$keyword.'%')
                    ->orWhere('email', 'like', '%'.$keyword.'%')
                    ->orWhere('phone', 'like', '%'.$keyword.'%')
                    ->orWhere('job_title', 'like', '%'.$keyword.'%');
            });
        }

        if (is_numeric($departmentId)) {
            $query->where('department_id', (int) $departmentId);
        }

        return view('admin.employees.index', $this->withBase([
            'employees' => $query->get(),
            'departments' => Department::query()->orderBy('name')->get(),
            'filters' => [
                'q' => $keyword,
                'department_id' => (string) $departmentId,
            ],
        ]));
    }

    public function employeesStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:160', 'unique:employees,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'job_title' => ['nullable', 'string', 'max:100'],
            'department_id' => ['required', 'exists:departments,id'],
        ]);

        $employee = Employee::query()->create(array_merge($validated, ['is_active' => true]));
        $this->logAudit('employee.created', 'employee', (string) $employee->id, ['email' => $employee->email]);

        return redirect()->back()->with('status', 'Da tao nhan vien moi.');
    }

    public function employeesShow(Employee $employee): View
    {
        $employee->load(['department', 'user']);
        $visits = $employee->hostedVisits()
            ->with('visitor')
            ->orderByDesc('scheduled_at')
            ->limit(20)
            ->get();

        return view('admin.employees.show', $this->withBase([
            'employee' => $employee,
            'visits' => $visits,
        ]));
    }

    public function employeesEdit(Employee $employee): View
    {
        return view('admin.employees.edit', $this->withBase([
            'employee' => $employee,
            'departments' => Department::query()->orderBy('name')->get(),
        ]));
    }

    public function employeesUpdate(Request $request, Employee $employee): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:160', Rule::unique('employees', 'email')->ignore($employee->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'job_title' => ['nullable', 'string', 'max:100'],
            'department_id' => ['required', 'exists:departments,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);
        $employee->update($validated);

        $this->logAudit('employee.updated', 'employee', (string) $employee->id, ['email' => $employee->email]);

        return redirect()
            ->route('admin.employees.show', $employee)
            ->with('status', 'Da cap nhat nhan vien.');
    }

    public function employeesDestroy(Employee $employee): RedirectResponse
    {
        if ($employee->hostedVisits()->exists()) {
            return redirect()
                ->back()
                ->with('error', 'Khong the xoa nhan vien da co lich tiep khach. Hay chuyen sang inactive neu khong con lam viec.');
        }

        $employeeId = (string) $employee->id;
        $employeeEmail = $employee->email;
        $employee->delete();

        $this->logAudit('employee.deleted', 'employee', $employeeId, ['email' => $employeeEmail]);

        return redirect()
            ->route('admin.employees.index')
            ->with('status', 'Da xoa nhan vien.');
    }

    public function visitorsIndex(Request $request): View
    {
        $keyword = trim((string) $request->input('q', ''));
        $query = Visitor::query()->withCount('visits')->orderByDesc('id');

        if ($keyword !== '') {
            $query->where(function ($visitorQuery) use ($keyword): void {
                $visitorQuery
                    ->where('full_name', 'like', '%'.$keyword.'%')
                    ->orWhere('phone', 'like', '%'.$keyword.'%')
                    ->orWhere('email', 'like', '%'.$keyword.'%')
                    ->orWhere('company', 'like', '%'.$keyword.'%');
            });
        }

        return view('admin.visitors.index', $this->withBase([
            'visitors' => $query->limit(200)->get(),
            'filters' => ['q' => $keyword],
        ]));
    }

    public function visitorsStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:160'],
            'company' => ['nullable', 'string', 'max:160'],
            'identity_no' => ['nullable', 'string', 'max:80'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $visitor = Visitor::query()->create($validated);
        $this->logAudit('visitor.created', 'visitor', (string) $visitor->id, ['phone' => $visitor->phone]);

        return redirect()->back()->with('status', 'Da tao ho so khach.');
    }

    public function visitorsShow(Visitor $visitor): View
    {
        $visitor->loadCount('visits');
        $visits = $visitor->visits()
            ->with('hostEmployee.department')
            ->orderByDesc('scheduled_at')
            ->limit(20)
            ->get();

        return view('admin.visitors.show', $this->withBase([
            'visitor' => $visitor,
            'visits' => $visits,
        ]));
    }

    public function visitorsEdit(Visitor $visitor): View
    {
        return view('admin.visitors.edit', $this->withBase([
            'visitor' => $visitor,
        ]));
    }

    public function visitorsUpdate(Request $request, Visitor $visitor): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:160'],
            'company' => ['nullable', 'string', 'max:160'],
            'identity_no' => ['nullable', 'string', 'max:80'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $visitor->update($validated);
        $this->logAudit('visitor.updated', 'visitor', (string) $visitor->id, ['phone' => $visitor->phone]);

        return redirect()
            ->route('admin.visitors.show', $visitor)
            ->with('status', 'Da cap nhat ho so khach.');
    }

    public function visitorsDestroy(Visitor $visitor): RedirectResponse
    {
        if ($visitor->visits()->exists()) {
            return redirect()
                ->back()
                ->with('error', 'Khong the xoa khach da co lich su ra vao. Can giu lai de bao cao va audit.');
        }

        $visitorId = (string) $visitor->id;
        $visitorName = $visitor->full_name;
        $visitor->delete();

        $this->logAudit('visitor.deleted', 'visitor', $visitorId, ['name' => $visitorName]);

        return redirect()
            ->route('admin.visitors.index')
            ->with('status', 'Da xoa ho so khach.');
    }


    private function generateDepartmentCode(string $name, ?int $ignoreDepartmentId = null): string
    {
        $name = trim($name);
        $ascii = Str::ascii($name);
        $words = preg_split('/[^A-Za-z0-9]+/', $ascii, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        if (count($words) >= 2) {
            $base = collect($words)
                ->map(fn (string $word): string => Str::upper(Str::substr($word, 0, 1)))
                ->implode('');
        } else {
            $base = Str::upper(Str::slug($ascii, ''));
        }

        $base = Str::substr(preg_replace('/[^A-Z0-9]/', '', $base) ?: 'PB', 0, 24);
        $candidate = $base;
        $index = 2;

        while (
            Department::query()
                ->where('code', $candidate)
                ->when($ignoreDepartmentId !== null, fn ($query) => $query->whereKeyNot($ignoreDepartmentId))
                ->exists()
        ) {
            $suffix = '-'.$index;
            $candidate = Str::substr($base, 0, 32 - strlen($suffix)).$suffix;
            $index++;
        }

        return $candidate;
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    private function logAudit(string $action, string $entityType, string $entityId, array $meta = []): void
    {
        AuditLog::query()->create([
            'user_id' => auth()->id(),
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'meta' => $meta,
        ]);
    }
}

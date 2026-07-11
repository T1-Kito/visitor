<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HasAdminLayoutData;
use App\Models\AuditLog;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Visitor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CatalogController extends Controller
{
    use HasAdminLayoutData;

    public function departmentsIndex(Request $request): View
    {
        $keyword = trim((string) $request->input('q', ''));
        $query = Department::query()
            ->with([
                'parent',
                'employees' => fn ($employeeQuery) => $employeeQuery
                    ->orderByDesc('is_active')
                    ->orderBy('name'),
            ])
            ->withCount(['employees', 'children'])
            ->orderByRaw('COALESCE(parent_id, id) ASC')
            ->orderBy('name');

        if ($keyword !== '') {
            $query->where(function ($departmentQuery) use ($keyword): void {
                $departmentQuery
                    ->where('code', 'like', '%'.$keyword.'%')
                    ->orWhere('name', 'like', '%'.$keyword.'%')
                    ->orWhere('name_vi', 'like', '%'.$keyword.'%')
                    ->orWhere('name_en', 'like', '%'.$keyword.'%');
            });
        }

        return view('admin.departments.index', $this->withBase([
            'departments' => $query->get(),
            'departmentOptions' => Department::query()->orderBy('name')->get(),
            'filters' => ['q' => $keyword],
        ]));
    }

    public function departmentsStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:120'],
            'name_vi' => ['required_without:name', 'nullable', 'string', 'max:120'],
            'name_en' => ['nullable', 'string', 'max:120'],
            'parent_id' => ['nullable', 'exists:departments,id'],
        ]);

        $validated['name_vi'] = $validated['name_vi'] ?? $validated['name'];
        $validated['name_en'] = $validated['name_en'] ?? $validated['name_vi'];
        $validated['name'] = $validated['name_vi'];
        $validated['code'] = $this->generateDepartmentCode($validated['name_vi']);
        $department = Department::query()->create($validated);
        $this->logAudit('department.created', 'department', (string) $department->id, $validated);

        return redirect()->back()->with('status', 'Da tao phong ban moi.');
    }

    public function departmentsShow(Department $department): View
    {
        $blockedDepartmentIds = array_values(array_unique(array_merge(
            [$department->id],
            $this->departmentDescendantIds($department)
        )));

        $department->load([
            'parent',
            'children' => fn ($query) => $query->withCount('employees')->orderBy('name'),
            'employees' => fn ($query) => $query->withCount('hostedVisits')->orderBy('name'),
        ]);
        $department->loadCount(['employees', 'children']);

        return view('admin.departments.show', $this->withBase([
            'department' => $department,
            'departmentOptions' => Department::query()
                ->whereNotIn('id', $blockedDepartmentIds)
                ->orderBy('name')
                ->get(),
        ]));
    }

    public function departmentsEdit(Department $department): View
    {
        $blockedDepartmentIds = array_values(array_unique(array_merge(
            [$department->id],
            $this->departmentDescendantIds($department)
        )));

        return view('admin.departments.edit', $this->withBase([
            'department' => $department,
            'departmentOptions' => Department::query()
                ->whereNotIn('id', $blockedDepartmentIds)
                ->orderBy('name')
                ->get(),
        ]));
    }

    public function departmentsUpdate(Request $request, Department $department): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:120'],
            'name_vi' => ['required_without:name', 'nullable', 'string', 'max:120'],
            'name_en' => ['nullable', 'string', 'max:120'],
            'parent_id' => [
                'nullable',
                'exists:departments,id',
                Rule::notIn(array_values(array_unique(array_merge(
                    [$department->id],
                    $this->departmentDescendantIds($department)
                )))),
            ],
        ]);

        $validated['name_vi'] = $validated['name_vi'] ?? $validated['name'];
        $validated['name_en'] = $validated['name_en'] ?? $department->name_en ?? $validated['name_vi'];
        $validated['name'] = $validated['name_vi'];
        $validated['code'] = $this->generateDepartmentCode($validated['name_vi'], $department->id);
        $department->update($validated);
        $this->logAudit('department.updated', 'department', (string) $department->id, $validated);

        return redirect()
            ->route('admin.departments.index')
            ->with('status', 'Đã cập nhật phòng ban.');
    }

    public function departmentsDestroy(Department $department): RedirectResponse
    {
        if ($department->children()->exists()) {
            return redirect()
                ->back()
                ->with('error', 'Khong the xoa phong ban dang co phong ban cap duoi.');
        }

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

    public function employeesImportTemplate(): StreamedResponse
    {
        $rows = [
            [
                $this->vi('H&#7885; v&#224; t&#234;n'),
                'Email',
                $this->vi('S&#7889; &#273;i&#7879;n tho&#7841;i'),
                $this->vi('Ch&#7913;c danh'),
                $this->vi('Ph&#242;ng ban'),
                $this->vi('&#272;ang ho&#7841;t &#273;&#7897;ng'),
            ],
            [
                $this->vi('Nguy&#7877;n V&#259;n A'),
                'nguyenvana@company.com',
                '0909123456',
                $this->vi('Nh&#226;n vi&#234;n kinh doanh'),
                $this->vi('Kinh doanh'),
                '1',
            ],
            [
                $this->vi('Tr&#7847;n Th&#7883; B'),
                'tranthib@company.com',
                '0909765432',
                $this->vi('Tr&#432;&#7903;ng nh&#243;m'),
                $this->vi('Nh&#226;n s&#7921;'),
                '1',
            ],
        ];
        $path = $this->makeEmployeeTemplateXlsx($rows);

        return response()->streamDownload(function () use ($path): void {
            readfile($path);
            @unlink($path);
        }, 'mau-import-nhan-vien.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function employeesImport(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('importEmployees', [
            'import_file' => ['required', 'file', 'mimes:xlsx,csv,txt', 'max:4096'],
        ]);

        $file = $validated['import_file'];
        $rows = $this->readEmployeeImportRows($file->getRealPath(), $file->getClientOriginalExtension());
        $header = array_shift($rows);
        if (! is_array($header)) {

            return back()->with('error', 'File import không có dữ liệu.');
        }

        $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $header[0]);
        $header = array_map(fn ($value): string => $this->normalizeEmployeeImportHeader((string) $value), $header);
        $requiredHeaders = ['name', 'email', 'phone', 'job_title', 'department', 'is_active'];
        $missingHeaders = array_diff($requiredHeaders, $header);

        if ($missingHeaders !== []) {
            return back()->with('error', 'File import thiếu cột: '.implode(', ', $missingHeaders).'. Hãy tải file mẫu mới nhất.');
        }

        $indexes = array_flip($header);
        $created = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];
        $line = 1;

        foreach ($rows as $row) {
            $line++;
            if ($row === [null] || count(array_filter($row, fn ($value): bool => trim((string) $value) !== '')) === 0) {
                continue;
            }

            $data = [];
            foreach ($requiredHeaders as $key) {
                $data[$key] = trim((string) ($row[$indexes[$key]] ?? ''));
            }

            $name = $data['name'];
            $email = Str::lower($data['email']);
            $departmentName = $data['department'];

            if ($name === '') {
                $skipped++;
                $errors[] = "Dòng {$line}: thiếu tên nhân viên.";
                continue;
            }

            if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                $skipped++;
                $errors[] = "Dòng {$line}: email không hợp lệ.";
                continue;
            }

            if ($departmentName === '') {
                $skipped++;
                $errors[] = "Dòng {$line}: thiếu phòng ban.";
                continue;
            }

            $department = Department::query()->firstOrCreate(
                ['name' => $departmentName],
                ['code' => $this->generateDepartmentCode($departmentName)]
            );

            $isActive = ! in_array(Str::lower($data['is_active']), ['0', 'false', 'no', 'inactive', 'ngung', 'ngừng'], true);
            $payload = [
                'department_id' => $department->id,
                'name' => $name,
                'email' => $email !== '' ? $email : null,
                'phone' => $data['phone'] !== '' ? $data['phone'] : null,
                'job_title' => $data['job_title'] !== '' ? $data['job_title'] : null,
                'is_active' => $isActive,
            ];

            if ($email !== '') {
                $employee = Employee::query()->where('email', $email)->first();
                if ($employee) {
                    $employee->update($payload);
                    $updated++;
                    continue;
                }
            }

            Employee::query()->create($payload);
            $created++;
        }

        $this->logAudit('employee.imported', 'employee', 'bulk', [
            'created' => $created,
            'updated' => $updated,
            'skipped' => $skipped,
        ]);

        $message = "Đã import nhân viên: thêm {$created}, cập nhật {$updated}, bỏ qua {$skipped}.";
        if ($errors !== []) {
            $message .= ' Lỗi: '.implode(' ', array_slice($errors, 0, 5));
        }

        return back()->with($errors === [] ? 'status' : 'error', $message);
    }

    /**
     * @param  array<int, array<int, string>>  $rows
     */
    private function makeEmployeeTemplateXlsx(array $rows): string
    {
        $path = tempnam(sys_get_temp_dir(), 'employee-template-').'.xlsx';
        $zip = new \ZipArchive();
        $zip->open($path, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/></Types>');
        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>');
        $zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/></Relationships>');
        $zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="Nhân viên" sheetId="1" r:id="rId1"/></sheets></workbook>');

        $sheetRows = '';
        foreach ($rows as $rowIndex => $row) {
            $cells = '';
            foreach ($row as $colIndex => $value) {
                $cellRef = $this->xlsxColumnName($colIndex + 1).($rowIndex + 1);
                $escaped = htmlspecialchars((string) $value, ENT_XML1 | ENT_COMPAT, 'UTF-8');
                $cells .= '<c r="'.$cellRef.'" t="inlineStr"><is><t>'.$escaped.'</t></is></c>';
            }
            $sheetRows .= '<row r="'.($rowIndex + 1).'">'.$cells.'</row>';
        }

        $zip->addFromString('xl/worksheets/sheet1.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><cols><col min="1" max="1" width="22" customWidth="1"/><col min="2" max="2" width="28" customWidth="1"/><col min="3" max="6" width="18" customWidth="1"/></cols><sheetData>'.$sheetRows.'</sheetData></worksheet>');
        $zip->close();

        return $path;
    }

    /**
     * @return array<int, array<int, string|null>>
     */
    private function readEmployeeImportRows(string $path, string $extension): array
    {
        if (Str::lower($extension) === 'xlsx') {
            return $this->readEmployeeImportXlsxRows($path);
        }

        $handle = fopen($path, 'r');
        if ($handle === false) {
            return [];
        }

        $rows = [];
        while (($row = fgetcsv($handle)) !== false) {
            if ($rows === [] && isset($row[0]) && Str::startsWith(Str::lower((string) $row[0]), 'sep=')) {
                continue;
            }
            $rows[] = $row;
        }
        fclose($handle);

        return $rows;
    }

    /**
     * @return array<int, array<int, string|null>>
     */
    private function readEmployeeImportXlsxRows(string $path): array
    {
        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) {
            return [];
        }

        $sharedStrings = [];
        $sharedXml = $zip->getFromName('xl/sharedStrings.xml');
        if (is_string($sharedXml)) {
            $shared = simplexml_load_string($sharedXml);
            if ($shared) {
                foreach ($shared->si as $item) {
                    $sharedStrings[] = isset($item->t) ? (string) $item->t : trim((string) $item->asXML());
                }
            }
        }

        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();
        if (! is_string($sheetXml)) {
            return [];
        }

        $sheet = simplexml_load_string($sheetXml);
        if (! $sheet) {
            return [];
        }

        $rows = [];
        foreach ($sheet->sheetData->row as $row) {
            $values = [];
            foreach ($row->c as $cell) {
                $cellRef = (string) $cell['r'];
                $column = $this->xlsxColumnIndex(preg_replace('/\d+/', '', $cellRef) ?: 'A') - 1;
                $type = (string) $cell['t'];
                $value = null;

                if ($type === 's') {
                    $value = $sharedStrings[(int) $cell->v] ?? null;
                } elseif ($type === 'inlineStr') {
                    $value = isset($cell->is->t) ? (string) $cell->is->t : null;
                } elseif (isset($cell->v)) {
                    $value = (string) $cell->v;
                }

                $values[$column] = $value;
            }

            if ($values !== []) {
                ksort($values);
                $rows[] = array_values($values);
            }
        }

        return $rows;
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

    private function xlsxColumnIndex(string $name): int
    {
        $index = 0;
        foreach (str_split($name) as $char) {
            $index = $index * 26 + (ord($char) - 64);
        }

        return $index;
    }

    private function normalizeEmployeeImportHeader(string $header): string
    {
        $header = Str::lower(trim($header));
        $header = preg_replace('/\s+/', ' ', $header) ?? $header;
        $key = str_replace(['_', '-', '.'], ' ', Str::ascii($header));

        return match ($key) {
            'ho va ten', 'ten nhan vien', 'nhan vien', 'name' => 'name',
            'email', 'e mail' => 'email',
            'so dien thoai', 'dien thoai', 'phone', 'sdt' => 'phone',
            'chuc danh', 'job title' => 'job_title',
            'phong ban', 'department' => 'department',
            'dang hoat dong', 'trang thai', 'is active', 'active' => 'is_active',
            default => $header,
        };
    }

    private function vi(string $value): string
    {
        return html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
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
            'departments' => Department::query()->orderBy('name')->get(),
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
            ->back()
            ->with('status', 'Đã cập nhật nhân viên.');
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
                    ->where('visitor_code', 'like', '%'.$keyword.'%')
                    ->orWhere('full_name', 'like', '%'.$keyword.'%')
                    ->orWhere('phone', 'like', '%'.$keyword.'%')
                    ->orWhere('email', 'like', '%'.$keyword.'%')
                    ->orWhere('company', 'like', '%'.$keyword.'%')
                    ->orWhere('identity_no', 'like', '%'.$keyword.'%')
                    ->orWhere('visitor_id_card_number', 'like', '%'.$keyword.'%');
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
            'visitor_id_card_number' => ['nullable', 'string', 'max:80'],
            'identity_issued_place' => ['nullable', 'string', 'max:160'],
            'identity_issued_date' => ['nullable', 'date'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $visitor = Visitor::query()->create($validated);
        $this->logAudit('visitor.created', 'visitor', (string) $visitor->id, [
            'visitor_code' => $visitor->visitor_code,
            'phone' => $visitor->phone,
        ]);

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
            'visitor_id_card_number' => ['nullable', 'string', 'max:80'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $visitor->update($validated);
        $this->logAudit('visitor.updated', 'visitor', (string) $visitor->id, [
            'visitor_code' => $visitor->visitor_code,
            'phone' => $visitor->phone,
        ]);

        return redirect()
            ->route('admin.visitors.show', $visitor)
            ->with('status', 'Da cap nhat ho so khach.');
    }

    public function visitorsDestroy(Visitor $visitor): RedirectResponse
    {
        $visitorId = (string) $visitor->id;
        $visitorName = $visitor->full_name;
        $visitIds = $visitor->visits()->pluck('id');

        DB::transaction(function () use ($visitor, $visitIds): void {
            if ($visitIds->isNotEmpty()) {
                Badge::query()
                    ->whereIn('visit_id', $visitIds)
                    ->update([
                        'visit_id' => null,
                        'status' => 'available',
                        'issued_at' => null,
                    ]);
            }

            $visitor->delete();
        });

        $this->logAudit('visitor.deleted', 'visitor', $visitorId, [
            'name' => $visitorName,
            'visits_deleted' => $visitIds->count(),
        ]);

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
     * @return array<int, int>
     */
    private function departmentDescendantIds(Department $department): array
    {
        $department->loadMissing('children');

        $ids = [];
        foreach ($department->children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $this->departmentDescendantIds($child));
        }

        return $ids;
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

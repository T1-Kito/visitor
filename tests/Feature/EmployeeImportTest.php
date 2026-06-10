<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EmployeeImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_download_employee_import_template(): void
    {
        $admin = $this->adminUser();

        $this->actingAs($admin)
            ->get(route('admin.employees.import-template'))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_admin_can_import_employees_from_csv(): void
    {
        Storage::fake('local');
        $admin = $this->adminUser();
        $department = Department::query()->create(['name' => 'Nhân sự', 'code' => 'nhan-su']);
        Employee::query()->create([
            'department_id' => $department->id,
            'name' => 'Người cũ',
            'email' => 'old@company.com',
            'phone' => '0900000000',
            'job_title' => 'Cũ',
            'is_active' => true,
        ]);

        $csv = implode("\n", [
            'name,email,phone,job_title,department,is_active',
            'Người mới,new@company.com,0901111111,Nhân viên,Kinh doanh,1',
            'Người cập nhật,old@company.com,0902222222,Trưởng nhóm,Nhân sự,0',
        ]);
        $file = UploadedFile::fake()->createWithContent('employees.csv', $csv);

        $this->actingAs($admin)
            ->post(route('admin.employees.import'), ['import_file' => $file])
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertDatabaseHas('departments', ['name' => 'Kinh doanh']);
        $this->assertDatabaseHas('employees', [
            'email' => 'new@company.com',
            'name' => 'Người mới',
            'job_title' => 'Nhân viên',
        ]);
        $this->assertDatabaseHas('employees', [
            'email' => 'old@company.com',
            'name' => 'Người cập nhật',
            'phone' => '0902222222',
            'is_active' => false,
        ]);
    }

    private function adminUser(): User
    {
        $admin = User::factory()->create();
        $admin->roles()->create(['name' => 'Admin', 'slug' => 'admin'])
            ->permissions()->create(['name' => 'Manage employees', 'slug' => 'employees.manage']);

        return $admin;
    }
}

<?php

use App\Models\Employee;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visits', function (Blueprint $table): void {
            $table->dropForeign(['host_employee_id']);
        });

        Schema::table('visits', function (Blueprint $table): void {
            $table->unsignedBigInteger('host_employee_id')->nullable()->change();
            $table->string('host_name', 120)->nullable()->after('host_employee_id');
            $table->foreignId('department_id')->nullable()->after('host_name')->constrained()->nullOnDelete();
            $table->foreign('host_employee_id')->references('id')->on('employees')->restrictOnDelete();
        });

        DB::table('visits')
            ->whereNotNull('host_employee_id')
            ->orderBy('id')
            ->chunkById(200, function ($visits): void {
                $employees = Employee::query()
                    ->withoutGlobalScopes()
                    ->whereIn('id', $visits->pluck('host_employee_id')->filter()->unique())
                    ->get(['id', 'name', 'department_id'])
                    ->keyBy('id');

                foreach ($visits as $visit) {
                    $employee = $employees->get($visit->host_employee_id);
                    if ($employee === null) {
                        continue;
                    }

                    DB::table('visits')->where('id', $visit->id)->update([
                        'host_name' => $employee->name,
                        'department_id' => $employee->department_id,
                    ]);
                }
            });
    }

    public function down(): void
    {
        $fallbackEmployeeId = Employee::query()->withoutGlobalScopes()->value('id');

        if ($fallbackEmployeeId !== null) {
            DB::table('visits')->whereNull('host_employee_id')->update([
                'host_employee_id' => $fallbackEmployeeId,
            ]);
        }

        Schema::table('visits', function (Blueprint $table): void {
            $table->dropForeign(['host_employee_id']);
            $table->dropConstrainedForeignId('department_id');
            $table->dropColumn('host_name');
        });

        Schema::table('visits', function (Blueprint $table) use ($fallbackEmployeeId): void {
            $table->unsignedBigInteger('host_employee_id')->nullable($fallbackEmployeeId === null)->change();
            $table->foreign('host_employee_id')->references('id')->on('employees')->restrictOnDelete();
        });
    }
};
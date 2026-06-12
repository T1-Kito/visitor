<div class="modal fade resource-modal" id="editEmployeeModal" tabindex="-1" aria-labelledby="editEmployeeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form id="editEmployeeForm"
              class="modal-content"
              method="post"
              action="{{ old('employee_id') ? route('admin.employees.update', old('employee_id')) : '#' }}"
              data-disable-on-submit>
            @csrf
            @method('put')
            <input type="hidden" name="form_context" value="edit_employee">
            <input id="editEmployeeId" type="hidden" name="employee_id" value="{{ old('employee_id') }}">

            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="editEmployeeModalLabel">Sửa nhân viên</h5>
                    <div class="text-secondary small">Cập nhật hồ sơ và trạng thái tiếp khách của nhân viên.</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>

            <div class="modal-body">
                <div class="resource-form-grid">
                    <div>
                        <label class="form-label" for="editEmployeeName">Họ và tên <span class="text-danger">*</span></label>
                        <input id="editEmployeeName"
                               class="form-control @error('name') is-invalid @enderror"
                               name="name"
                               value="{{ old('form_context') === 'edit_employee' ? old('name') : '' }}"
                               required>
                        @if (old('form_context') === 'edit_employee')
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        @endif
                    </div>

                    <div>
                        <label class="form-label" for="editEmployeeEmail">Email</label>
                        <input id="editEmployeeEmail"
                               class="form-control @error('email') is-invalid @enderror"
                               type="email"
                               name="email"
                               value="{{ old('form_context') === 'edit_employee' ? old('email') : '' }}">
                        @if (old('form_context') === 'edit_employee')
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        @endif
                    </div>

                    <div>
                        <label class="form-label" for="editEmployeePhone">Số điện thoại</label>
                        <input id="editEmployeePhone"
                               class="form-control @error('phone') is-invalid @enderror"
                               name="phone"
                               value="{{ old('form_context') === 'edit_employee' ? old('phone') : '' }}">
                        @if (old('form_context') === 'edit_employee')
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        @endif
                    </div>

                    <div>
                        <label class="form-label" for="editEmployeeJobTitle">Chức danh</label>
                        <input id="editEmployeeJobTitle"
                               class="form-control @error('job_title') is-invalid @enderror"
                               name="job_title"
                               value="{{ old('form_context') === 'edit_employee' ? old('job_title') : '' }}">
                        @if (old('form_context') === 'edit_employee')
                            @error('job_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        @endif
                    </div>

                    <div>
                        <label class="form-label" for="editEmployeeDepartment">Phòng ban <span class="text-danger">*</span></label>
                        <select id="editEmployeeDepartment"
                                class="form-select @error('department_id') is-invalid @enderror"
                                name="department_id"
                                required>
                            <option value="">Chọn phòng ban</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}"
                                        @selected(old('form_context') === 'edit_employee' && (string) old('department_id') === (string) $department->id)>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                        @if (old('form_context') === 'edit_employee')
                            @error('department_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        @endif
                    </div>

                    <div class="d-flex align-items-end pb-2">
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input id="editEmployeeActive"
                                   class="form-check-input"
                                   type="checkbox"
                                   name="is_active"
                                   value="1"
                                   @checked(old('form_context') === 'edit_employee' && old('is_active'))>
                            <label class="form-check-label" for="editEmployeeActive">Đang hoạt động</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                <button class="btn btn-brand" type="submit" data-loading-text="Đang lưu...">
                    <i class="bi bi-check2-circle"></i>
                    Lưu thay đổi
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const modalElement = document.getElementById('editEmployeeModal');
    const form = document.getElementById('editEmployeeForm');
    const idInput = document.getElementById('editEmployeeId');
    const nameInput = document.getElementById('editEmployeeName');
    const emailInput = document.getElementById('editEmployeeEmail');
    const phoneInput = document.getElementById('editEmployeePhone');
    const jobTitleInput = document.getElementById('editEmployeeJobTitle');
    const departmentSelect = document.getElementById('editEmployeeDepartment');
    const activeInput = document.getElementById('editEmployeeActive');

    if (!modalElement || !form || !idInput || !nameInput || !emailInput || !phoneInput || !jobTitleInput || !departmentSelect || !activeInput) {
        return;
    }

    const editButtons = Array.from(document.querySelectorAll('[data-edit-employee]'));
    const findButton = (employeeId) => editButtons.find(
        (button) => String(button.dataset.employeeId) === String(employeeId)
    );

    const populateForm = (button, preserveOldInput = false) => {
        if (!button) return;

        form.action = button.dataset.updateUrl || '#';
        idInput.value = button.dataset.employeeId || '';

        if (!preserveOldInput) {
            nameInput.value = button.dataset.employeeName || '';
            emailInput.value = button.dataset.employeeEmail || '';
            phoneInput.value = button.dataset.employeePhone || '';
            jobTitleInput.value = button.dataset.employeeJobTitle || '';
            departmentSelect.value = button.dataset.departmentId || '';
            activeInput.checked = button.dataset.employeeActive === '1';
        }
    };

    modalElement.addEventListener('show.bs.modal', (event) => {
        if (event.relatedTarget) {
            populateForm(event.relatedTarget);
            return;
        }

        populateForm(findButton(idInput.value), true);
    });

    modalElement.addEventListener('hidden.bs.modal', () => {
        form.action = '#';
        idInput.value = '';
        nameInput.value = '';
        emailInput.value = '';
        phoneInput.value = '';
        jobTitleInput.value = '';
        departmentSelect.value = '';
        activeInput.checked = false;
    });
});
</script>
@endpush

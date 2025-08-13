@extends('master')

@section('title', 'Employees List')

@section('styles')
    <style>
        /* Bootstrap 5 Pagination Styling */
        .pagination {
            margin-top: 1rem;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
        .pagination .page-item {
            margin: 0 2px;
        }
        .pagination .page-link {
            font-size: 0.9rem;
            padding: 0.5rem 0.75rem;
            border-radius: 0.375rem;
            color: #0d6efd;
            background-color: #fff;
            border: 1px solid #dee2e6;
            transition: all 0.2s ease-in-out;
            text-decoration: none;
        }
        .pagination .page-link:hover {
            color: #005cbf;
            background-color: #e9ecef;
            border-color: #dee2e6;
        }
        .pagination .page-item.active .page-link {
            color: #fff;
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
        .pagination .page-item.disabled .page-link {
            color: #6c757d;
            background-color: #fff;
            border-color: #dee2e6;
            cursor: not-allowed;
        }
        #pagination {
            display: flex;
            justify-content: center;
        }
    </style>
@endsection

@section('content')
<div class="container">
    <h1>Employees</h1>

    <!-- Button trigger modal for creating employee -->
    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
        Add Employee
    </button>

    <!-- Search & Filter -->
    <form id="employeeFilterForm" class="mb-3 d-flex">
        <input type="text" name="search" class="form-control me-2" placeholder="Search by name/email" value="{{ request('search') }}">
        <select name="department_id" class="form-select me-2">
            <option value="">All Departments</option>
            @foreach($departments as $department)
                <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                    {{ $department->name }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-secondary">Filter</button>
    </form>

    <!-- Employee Table -->
    <div id="employeesTableWrapper">
        <table class="table table-bordered" id="employeesTable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Department</th>
                    <th>Salary</th>
                    <th>Joining Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Table content will be loaded via AJAX -->
            </tbody>
        </table>
        <div id="pagination"></div>
    </div>
</div>

<!-- Add Employee Modal -->
<div class="modal fade" id="addEmployeeModal" tabindex="-1" aria-labelledby="addEmployeeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="addEmployeeForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addEmployeeModalLabel">Add Employee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="department_id" class="form-label">Department</label>
                        <select name="department_id" id="department_id" class="form-select" required>
                            <option value="">Select Department</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="salary" class="form-label">Salary</label>
                        <input type="number" name="salary" id="salary" class="form-control" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="joining_date" class="form-label">Joining Date</label>
                        <input type="date" name="joining_date" id="joining_date" class="form-control" required>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Employee</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Employee Modal -->
<div class="modal fade" id="editEmployeeModal" tabindex="-1" aria-labelledby="editEmployeeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editEmployeeForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="edit_employee_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="editEmployeeModalLabel">Edit Employee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Name</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Email</label>
                        <input type="email" name="email" id="edit_email" class="form-control" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_department_id" class="form-label">Department</label>
                        <select name="department_id" id="edit_department_id" class="form-select" required>
                            <option value="">Select Department</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_salary" class="form-label">Salary</label>
                        <input type="number" name="salary" id="edit_salary" class="form-control" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_joining_date" class="form-label">Joining Date</label>
                        <input type="date" name="joining_date" id="edit_joining_date" class="form-control" required>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Employee</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Debug CSRF token
        console.log('CSRF Token:', $('meta[name="csrf-token"]').attr('content'));

        // Set up CSRF token for all AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function() {
            // Load employees table on page load
            loadEmployees();

            // Handle search/filter form submission
            $('#employeeFilterForm').on('submit', function(e) {
                e.preventDefault();
                loadEmployees();
            });

            // Handle create form submission
            $('#addEmployeeForm').on('submit', function(e) {
                e.preventDefault();
                
                const form = $(this);
                const submitButton = form.find('button[type="submit"]');
                submitButton.prop('disabled', true);

                $.ajax({
                    url: "{{ route('employees.store') }}",
                    type: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        if (response.success) {
                            $('#addEmployeeModal').modal('hide');
                            form[0].reset();
                            loadEmployees();
                            showAlert('success', response.message);
                        } else {
                            showAlert('error', response.message || 'Failed to create employee.');
                        }
                    },
                    error: function(xhr) {
                        console.log('Create Error:', xhr.responseJSON);
                        if (xhr.status === 419) {
                            showAlert('error', 'CSRF token mismatch. Please refresh the page and try again.');
                        } else {
                            const errors = xhr.responseJSON?.errors;
                            if (errors) {
                                Object.keys(errors).forEach(field => {
                                    const input = form.find(`[name="${field}"]`);
                                    input.addClass('is-invalid');
                                    input.next('.invalid-feedback').text(errors[field][0]);
                                });
                            } else {
                                showAlert('error', xhr.responseJSON?.message || 'Failed to create employee. Please try again.');
                            }
                        }
                    },
                    complete: function() {
                        submitButton.prop('disabled', false);
                    }
                });
            });

            // Handle edit form submission
            $('#editEmployeeForm').on('submit', function(e) {
                e.preventDefault();
                
                const form = $(this);
                const submitButton = form.find('button[type="submit"]');
                const employeeId = $('#edit_employee_id').val();
                submitButton.prop('disabled', true);

                $.ajax({
                    url: "{{ route('employees.update', ':id') }}".replace(':id', employeeId),
                    type: 'PUT',
                    data: form.serialize(),
                    success: function(response) {
                        if (response.success) {
                            $('#editEmployeeModal').modal('hide');
                            form[0].reset();
                            loadEmployees();
                            showAlert('success', response.message);
                        } else {
                            showAlert('error', response.message || 'Failed to update employee.');
                        }
                    },
                    error: function(xhr) {
                        console.log('Update Error:', xhr.responseJSON);
                        if (xhr.status === 419) {
                            showAlert('error', 'CSRF token mismatch. Please refresh the page and try again.');
                        } else {
                            const errors = xhr.responseJSON?.errors;
                            if (errors) {
                                Object.keys(errors).forEach(field => {
                                    const input = form.find(`[name="${field}"]`);
                                    input.addClass('is-invalid');
                                    input.next('.invalid-feedback').text(errors[field][0]);
                                });
                            } else {
                                showAlert('error', xhr.responseJSON?.message || 'Failed to update employee. Please try again.');
                            }
                        }
                    },
                    complete: function() {
                        submitButton.prop('disabled', false);
                    }
                });
            });

            // Clear validation errors when modals are closed
            $('#addEmployeeModal, #editEmployeeModal').on('hidden.bs.modal', function() {
                const form = $(this).find('form');
                form.find('.is-invalid').removeClass('is-invalid');
                form.find('.invalid-feedback').text('');
                form[0].reset();
            });

            // Handle edit button click
            $(document).on('click', '.edit-employee', function(e) {
                e.preventDefault();
                const employeeId = $(this).data('id');

                $.ajax({
                    url: "{{ route('employees.show', ':id') }}".replace(':id', employeeId),
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success && response.employee) {
                            $('#edit_employee_id').val(response.employee.id);
                            $('#edit_name').val(response.employee.name);
                            $('#edit_email').val(response.employee.email);
                            $('#edit_department_id').val(response.employee.department_id);
                            $('#edit_salary').val(response.employee.salary);
                            $('#edit_joining_date').val(response.employee.joining_date);
                            $('#editEmployeeModal').modal('show');
                        } else {
                            showAlert('error', response.message || 'Failed to load employee details.');
                        }
                    },
                    error: function(xhr) {
                        console.log('Edit Error:', xhr.responseJSON);
                        if (xhr.status === 419) {
                            showAlert('error', 'CSRF token mismatch. Please refresh the page and try again.');
                        } else {
                            showAlert('error', xhr.responseJSON?.message || 'Failed to load employee details.');
                        }
                    }
                });
            });

            // Handle delete button click
            $(document).on('click', '.delete-employee', function(e) {
                e.preventDefault();
                if (!confirm('Are you sure you want to delete this employee?')) {
                    return;
                }

                const button = $(this);
                const employeeId = button.data('id');

                $.ajax({
                    url: "{{ route('employees.destroy', ':id') }}".replace(':id', employeeId),
                    type: 'DELETE',
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
                    },
                    success: function(response) {
                        if (response.success) {
                            loadEmployees();
                            showAlert('success', response.message);
                        } else {
                            showAlert('error', response.message || 'Failed to delete employee.');
                        }
                    },
                    error: function(xhr) {
                        console.log('Delete Error:', xhr.responseJSON);
                        if (xhr.status === 419) {
                            showAlert('error', 'CSRF token mismatch. Please refresh the page and try again.');
                        } else {
                            showAlert('error', xhr.responseJSON?.message || 'Failed to delete employee.');
                        }
                    }
                });
            });

            // Handle pagination click
            $(document).on('click', '#pagination a', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                loadEmployees(url);
            });

            function loadEmployees(url = "{{ route('employees.index') }}") {
                const formData = $('#employeeFilterForm').serialize();
                $.ajax({
                    url: url,
                    type: 'GET',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        console.log('Load Employees Response:', response);
                        if (response.success) {
                            updateTable(response.employees, response.links);
                        } else {
                            showAlert('error', response.message || 'Failed to load employees.');
                        }
                    },
                    error: function(xhr) {
                        console.log('Load Employees Error:', xhr.responseJSON);
                        if (xhr.status === 419) {
                            showAlert('error', 'CSRF token mismatch. Please refresh the page and try again.');
                        } else {
                            showAlert('error', xhr.responseJSON?.message || 'Failed to load employees.');
                        }
                    }
                });
            }

            function updateTable(employees, links) {
                console.log('Pagination Links:', links);
                const tbody = $('#employeesTable tbody');
                tbody.empty();

                if (employees.data.length === 0) {
                    tbody.append('<tr><td colspan="6" class="text-center">No Employees</td></tr>');
                } else {
                    $.each(employees.data, function(index, employee) {
                        const row = `
                            <tr>
                                <td>${employee.name}</td>
                                <td>${employee.email}</td>
                                <td>${employee.department ? employee.department.name : 'N/A'}</td>
                                <td>${employee.salary}</td>
                                <td>${employee.joining_date}</td>
                                <td>
                                    <button class="btn btn-sm btn-warning edit-employee" data-id="${employee.id}">Edit</button>
                                    <button class="btn btn-sm btn-danger delete-employee" data-id="${employee.id}">Delete</button>
                                </td>
                            </tr>
                        `;
                        tbody.append(row);
                    });
                }

                $('#pagination').html(links || '');
            }

            function showAlert(type, message) {
                const alert = `
                    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                $('body').append(alert);
                setTimeout(() => $('.alert').alert('close'), 3000);
            }
        });
    </script>
@endsection
@extends('master')

@section('title', 'Departments List')

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
    <h1>Departments</h1>

    <!-- Button trigger modal for creating department -->
    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createDepartmentModal">
        Add Department
    </button>

    <!-- Create Department Modal -->
    <div class="modal fade" id="createDepartmentModal" tabindex="-1" aria-labelledby="createDepartmentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="createDepartmentForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="createDepartmentModalLabel">Add Department</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Department Name</label>
                            <input type="text" name="name" id="name" class="form-control" required>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Save Department</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Department Modal -->
    <div class="modal fade" id="editDepartmentModal" tabindex="-1" aria-labelledby="editDepartmentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editDepartmentForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="edit_department_id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editDepartmentModalLabel">Edit Department</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Department Name</label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Update Department</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Department Table -->
    <div id="departmentsTableWrapper">
        <table class="table table-bordered" id="departmentsTable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Employees Count</th>
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
            // Load departments table on page load
            loadDepartments();

            // Handle create form submission
            $('#createDepartmentForm').on('submit', function(e) {
                e.preventDefault();
                
                const form = $(this);
                const submitButton = form.find('button[type="submit"]');
                submitButton.prop('disabled', true);

                $.ajax({
                    url: "{{ route('departments.store') }}",
                    type: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        if (response.success) {
                            $('#createDepartmentModal').modal('hide');
                            form[0].reset();
                            loadDepartments();
                            showAlert('success', response.message);
                        } else {
                            showAlert('error', response.message || 'Failed to create department.');
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
                                showAlert('error', xhr.responseJSON?.message || 'Failed to create department. Please try again.');
                            }
                        }
                    },
                    complete: function() {
                        submitButton.prop('disabled', false);
                    }
                });
            });

            // Handle edit form submission
            $('#editDepartmentForm').on('submit', function(e) {
                e.preventDefault();
                
                const form = $(this);
                const submitButton = form.find('button[type="submit"]');
                const departmentId = $('#edit_department_id').val();
                submitButton.prop('disabled', true);

                $.ajax({
                    url: "{{ route('departments.update', ':id') }}".replace(':id', departmentId),
                    type: 'PUT',
                    data: form.serialize(),
                    success: function(response) {
                        if (response.success) {
                            $('#editDepartmentModal').modal('hide');
                            form[0].reset();
                            loadDepartments();
                            showAlert('success', response.message);
                        } else {
                            showAlert('error', response.message || 'Failed to update department.');
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
                                showAlert('error', xhr.responseJSON?.message || 'Failed to update department. Please try again.');
                            }
                        }
                    },
                    complete: function() {
                        submitButton.prop('disabled', false);
                    }
                });
            });

            // Clear validation errors when modals are closed
            $('#createDepartmentModal, #editDepartmentModal').on('hidden.bs.modal', function() {
                const form = $(this).find('form');
                form.find('.is-invalid').removeClass('is-invalid');
                form.find('.invalid-feedback').text('');
                form[0].reset();
            });

            // Handle edit button click
            $(document).on('click', '.edit-department', function(e) {
                e.preventDefault();
                const departmentId = $(this).data('id');

                $.ajax({
                    url: "{{ route('departments.show', ':id') }}".replace(':id', departmentId),
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success && response.department) {
                            $('#edit_department_id').val(response.department.id);
                            $('#edit_name').val(response.department.name);
                            $('#editDepartmentModal').modal('show');
                        } else {
                            showAlert('error', response.message || 'Failed to load department details.');
                        }
                    },
                    error: function(xhr) {
                        console.log('Edit Error:', xhr.responseJSON);
                        if (xhr.status === 419) {
                            showAlert('error', 'CSRF token mismatch. Please refresh the page and try again.');
                        } else {
                            showAlert('error', xhr.responseJSON?.message || 'Failed to load department details.');
                        }
                    }
                });
            });

            // Handle delete button click
            $(document).on('click', '.delete-department', function(e) {
                e.preventDefault();
                if (!confirm('Are you sure you want to delete this department?')) {
                    return;
                }

                const button = $(this);
                const departmentId = button.data('id');

                $.ajax({
                    url: "{{ route('departments.destroy', ':id') }}".replace(':id', departmentId),
                    type: 'DELETE',
                    success: function(response) {
                        if (response.success) {
                            loadDepartments();
                            showAlert('success', response.message);
                        } else {
                            showAlert('error', response.message || 'Failed to delete department.');
                        }
                    },
                    error: function(xhr) {
                        console.log('Delete Error:', xhr.responseJSON);
                        if (xhr.status === 419) {
                            showAlert('error', 'CSRF token mismatch. Please refresh the page and try again.');
                        } else {
                            showAlert('error', xhr.responseJSON?.message || 'Failed to delete department.');
                        }
                    }
                });
            });

            // Handle pagination click
            $(document).on('click', '#pagination a', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                loadDepartments(url);
            });

            function loadDepartments(url = "{{ route('departments.index') }}") {
                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        console.log('Load Departments Response:', response);
                        if (response.success) {
                            updateTable(response.departments, response.links);
                        } else {
                            showAlert('error', response.message || 'Failed to load departments.');
                        }
                    },
                    error: function(xhr) {
                        console.log('Load Departments Error:', xhr.responseJSON);
                        if (xhr.status === 419) {
                            showAlert('error', 'CSRF token mismatch. Please refresh the page and try again.');
                        } else {
                            showAlert('error', xhr.responseJSON?.message || 'Failed to load departments.');
                        }
                    }
                });
            }

            function updateTable(departments, links) {
                console.log('Pagination Links:', links);
                const tbody = $('#departmentsTable tbody');
                tbody.empty();

                if (departments.data.length === 0) {
                    tbody.append('<tr><td colspan="3" class="text-center">No Departments</td></tr>');
                } else {
                    $.each(departments.data, function(index, department) {
                        const row = `
                            <tr>
                                <td>${department.name}</td>
                                <td>${department.employees_count || 0}</td>
                                <td>
                                    <button class="btn btn-sm btn-warning edit-department" data-id="${department.id}">Edit</button>
                                    <button class="btn btn-sm btn-danger delete-department" data-id="${department.id}">Delete</button>
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
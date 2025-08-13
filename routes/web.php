<?php

use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('master');
});

Route::resource('departments', DepartmentController::class)->names([
    'index'   => 'departments.index',
    'create'  => 'departments.create',
    'store'   => 'departments.store',
    'show'    => 'departments.show',
    'edit'    => 'departments.edit',
    'update'  => 'departments.update',
    'destroy' => 'departments.destroy',
]);

Route::resource('employees', EmployeeController::class)->names([
    'index'   => 'employees.index',
    'create'  => 'employees.create',
    'store'   => 'employees.store',
    'show'    => 'employees.show',
    'edit'    => 'employees.edit',
    'update'  => 'employees.update',
    'destroy' => 'employees.destroy',
]);


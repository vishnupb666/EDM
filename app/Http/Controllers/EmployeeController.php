<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use App\Models\Employees;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Employees::with('department');

        // Search by name/email
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by department
        if ($department_id = $request->input('department_id')) {
            $query->where('department_id', $department_id);
        }

        $employees = $query->paginate(5)
                         ->appends(['search' => $search]);;
        $departments = Department::all();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'employees' => $employees,
                'links' => $employees->links()->toHtml()
            ]);
        }

        return view('employee.index', compact('employees', 'departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departments = Department::all();
        return view('employees.create', compact('departments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'department_id' => 'required|exists:departments,id',
            'salary' => 'required|numeric|min:0',
            'joining_date' => 'required|date|before:today',
        ]);

        try {
            Employees::create($validated);
            return response()->json([
                'success' => true,
                'message' => 'Employee created successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating employee: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create employee.'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        try {
            $employee = Employees::with('department')->findOrFail($id);
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'employee' => $employee
                ]);
            }
            return view('employees.show', compact('employee'));
        } catch (\Exception $e) {
            Log::error('Error fetching employee: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee not found.'
                ], 404);
            }
            return redirect()->route('employees.index')
                ->with('error', 'Employee not found.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $employee = Employees::findOrFail($id);
            $departments = Department::all();
            return view('employees.edit', compact('employee', 'departments'));
        } catch (\Exception $e) {
            Log::error('Error fetching employee for edit: ' . $e->getMessage());
            return redirect()->route('employees.index')
                ->with('error', 'Employee not found.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $id,
            'department_id' => 'required|exists:departments,id',
            'salary' => 'required|numeric|min:0',
            'joining_date' => 'required|date|before:today',
        ]);

        try {
            $employee = Employees::findOrFail($id);
            $employee->update($validated);
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Employee updated successfully.'
                ]);
            }
            return redirect()->route('employees.index')
                ->with('success', 'Employee updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating employee: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update employee.'
                ], 500);
            }
            return redirect()->back()
                ->with('error', 'Failed to update employee. Please try again.')
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $employee = Employees::findOrFail($id);
            $employee->delete();
            return response()->json([
                'success' => true,
                'message' => 'Employee deleted successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting employee: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete employee.'
            ], 500);
        }
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $departments = Department::withCount('employees')
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%");
            })
            ->paginate(10);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'departments' => $departments,
                'links' => $departments->links()->toHtml()
            ]);
        }

        return view('department.index', compact('departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('department.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments',
        ]);

        try {
            Department::create($validated);
            return response()->json([
                'success' => true,
                'message' => 'Department created successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating department: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create department.'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        try {
            $department = Department::findOrFail($id);
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'department' => $department
                ]);
            }
            return view('department.show', compact('department'));
        } catch (\Exception $e) {
            Log::error('Error fetching department: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Department not found.'
                ], 404);
            }
            return redirect()->route('departments.index')
                ->with('error', 'Department not found.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $department = Department::findOrFail($id);
            return view('department.edit', compact('department'));
        } catch (\Exception $e) {
            Log::error('Error fetching department for edit: ' . $e->getMessage());
            return redirect()->route('departments.index')
                ->with('error', 'Department not found.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $id,
        ]);

        try {
            $department = Department::findOrFail($id);
            $department->update($validated);
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Department updated successfully.'
                ]);
            }
            return redirect()->route('departments.index')
                ->with('success', 'Department updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating department: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update department.'
                ], 500);
            }
            return redirect()->back()
                ->with('error', 'Failed to update department. Please try again.')
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $department = Department::findOrFail($id);
            
            if ($department->employees()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete department with associated employees.'
                ], 400);
            }

            $department->delete();
            return response()->json([
                'success' => true,
                'message' => 'Department deleted successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting department: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete department.'
            ], 500);
        }
    }
}
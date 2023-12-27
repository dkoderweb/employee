<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Http\Requests\EmployeeRequest;  
use App\Models\Department;
use Yajra\DataTables\DataTables;
use App\Http\Resources\EmployeeResource;  
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $departments = Department::all();
        
        if ($request->ajax()) {
            $data = Employee::with('departments')->get();
            
            return DataTables::of($data)  
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a href="#" class="edit btn btn-primary btn-sm employeeEdit" data-id="' . $row->id . '">Edit</a>
                    <a href="' . route('employee.destroy', $row->id) . '" class="delete btn btn-danger btn-sm delete-confirm">Delete</a>';
                    
                    return $actionBtn;
                })
                ->addColumn('departments', function ($employee) {
                    return implode(', ', $employee->departments->pluck('name')->toArray());
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        
        return view('home', compact('departments'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\EmployeeRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'required|string|max:15|unique:employees,phone',
            'departments' => 'required|array',
            'departments.*' => 'exists:departments,id',
        ]);
        $employee = Employee::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
        ]);

        $employee->departments()->sync($request->input('departments'));

        return response()->json([
            'success' => 'Employee added successfully.',
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function show(Employee $employee)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function edit(Employee $employee)
    {
        // Fetch the employee details along with associated departments
        $employee = $employee->load('departments');
    
        // Return the employee details in JSON format
        return response()->json([
            'data' => $employee,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\EmployeeRequest  $request
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Employee $employee)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'phone' => 'required|string|max:15|unique:employees,phone,' . $employee->id,
            'departments' => 'required|array',
            'departments.*' => 'exists:departments,id',
        ]);
        
        $employee->update([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
        ]);

        $employee->departments()->sync($request->input('departments'));

        return response()->json([
            'success' => 'Employee updated successfully.',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy(Employee $employee)
    {
        $employee->delete();

        return response()->json([
            'success' => 'Employee deleted successfully.',
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;    

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Department::select('*');
            return Datatables::of($data)
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a href="#" class="edit btn btn-primary btn-sm departmentEdit" data-id="' . $row->id . '">Edit</a>
                    <a href="' . route('department.destroy', $row->id) . '" class="delete btn btn-danger btn-sm delete-confirm">Delete</a>';

                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    
        return view('department.index');
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:departments',
        ]);
    
        $department = Department::create([
            'name' => $request->input('name'),
        ]);
    
        return response()->json([ 
            'success' => 'Department added successfully.',
        ]);
    }
    

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function show(Department $department)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function edit(Department $department)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Department $department)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $department->id,
        ]);
    
        $department->update([
            'name' => $request->input('name'),
        ]);
    
        return response()->json([
            'success' => 'Department updated successfully.',
        ]);
    }
    

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function destroy(Department $department)
    {
        $department->delete();
    
        return response()->json([
            'success' => 'Department deleted successfully.',
        ]);
    }
}

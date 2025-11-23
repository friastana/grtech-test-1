<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeStoreRequest;
use App\Models\Employee;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EmployeeController extends Controller
{
    public function index()
    {
        return Inertia::render('Employee/Index');
    }

    public function create()
    {
        return Inertia::render('Employee/Create');
    }

    public function store(EmployeeStoreRequest $request)
    {
        Employee::create([
            'full_name' => $request->full_name,
            'company_id' => $request->company_id,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        return redirect()->route('employee.index');
    }

    public function list(Request $request)
    {
        $query = Employee::query();

        // Sort
        if ($request->sortField && $request->sortOrder) {
            $field = $request->sortField;
            $order = $request->sortOrder === 'ascend' ? 'asc' : 'desc';

            $query->orderBy($field, $order);
        }

        // Pagination
        $perPage = $request->results ? $request->results : 10;

        $employees = $query->paginate($perPage);

        // Results
        $results = [];

        foreach ($employees->items() as $employee) {
            $results[] = [
                ...$employee->toArray(),
                'company' => $employee->company->toArray(),
            ];
        }

        return json_encode([
            'results' => $results,
            'total' => $employees->total(),
        ]);
    }

    public function update(EmployeeStoreRequest $request, Employee $employee)
    {
        $employee->full_name = $request->full_name;
        $employee->company_id = $request->company_id;
        $employee->email = $request->email;
        $employee->phone = $request->phone;

        $employee->save();

        return redirect()->route('employee.index');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();

        return redirect()->route('employee.index');
    }
}

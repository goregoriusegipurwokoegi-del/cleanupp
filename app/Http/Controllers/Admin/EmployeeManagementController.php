<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeManagementController extends Controller
{
    public function index()
    {
        $employees = User::where('role', 'employee')->latest()->get();
        return view('admin.employees.index', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'work_start' => 'nullable|date_format:H:i',
            'work_end' => 'nullable|date_format:H:i',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'password_plain' => $request->password,
            'role' => 'employee',
            'work_start' => $request->work_start ?? '09:00',
            'work_end' => $request->work_end ?? '17:00',
        ]);

        return back()->with('success', 'Karyawan baru berhasil ditambahkan.');
    }

    public function update(Request $request, User $employee)
    {
        if ($employee->role !== 'employee') {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $employee->id,
            'password' => 'nullable|string|min:8',
            'work_start' => 'nullable|date_format:H:i',
            'work_end' => 'nullable|date_format:H:i',
        ]);

        $employee->name = $request->name;
        $employee->email = $request->email;
        $employee->work_start = $request->work_start ?? '09:00';
        $employee->work_end = $request->work_end ?? '17:00';
        
        if ($request->password) {
            $employee->password = Hash::make($request->password);
            $employee->password_plain = $request->password;
        }

        $employee->save();

        return back()->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function attendance(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        $attendances = \App\Models\Attendance::with('user')
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->get();

        $employees = User::where('role', 'employee')->get();

        return view('admin.employees.attendance', compact('attendances', 'employees', 'startDate', 'endDate'));
    }

    public function destroy(User $employee)
    {
        if ($employee->role !== 'employee') {
            abort(403);
        }
        
        $employee->delete();
        return back()->with('success', 'Data karyawan telah dihapus.');
    }
}

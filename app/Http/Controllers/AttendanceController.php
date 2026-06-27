<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index()
    {
        // Admin View
        $attendances = Attendance::with('user')->latest('date')->get();
        return view('admin.attendance.index', compact('attendances'));
    }

    public function clockIn()
    {
        $today = now()->format('Y-m-d');
        $attendance = Attendance::firstOrCreate(
            ['user_id' => Auth::id(), 'date' => $today],
            ['clock_in' => now()->format('H:i:s')]
        );

        return back()->with('success', 'Absen masuk berhasil dilakukan jam ' . $attendance->clock_in);
    }

    public function clockOut()
    {
        $today = now()->format('Y-m-d');
        /** @var Attendance|null $attendance */
        $attendance = Attendance::where(['user_id' => Auth::id(), 'date' => $today])->first();

        if (!$attendance) {
            return back()->with('error', 'Anda belum absen masuk hari ini.');
        }

        if ($attendance->clock_out) {
            return back()->with('error', 'Anda sudah absen keluar hari ini.');
        }

        $attendance->update(['clock_out' => now()->format('H:i:s')]);

        return back()->with('success', 'Absen keluar berhasil dilakukan jam ' . $attendance->clock_out);
    }

    public function employeeAttendance()
    {
        $todayAttendance = Attendance::where([
            'user_id' => Auth::id(),
            'date' => now()->toDateString()
        ])->first();

        $isClockedIn = !is_null($todayAttendance);
        $isClockedOut = $todayAttendance && !is_null($todayAttendance->clock_out);

        $attendances = Attendance::where(['user_id' => Auth::id()])
            ->orderBy('date', 'desc')
            ->get();

        return view('employee.attendance.index', compact('todayAttendance', 'isClockedIn', 'isClockedOut', 'attendances'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoanController extends Controller
{
    public function index()
    {
        if (Auth::user()->role == 'admin') {
            $loans = Loan::with('user')->latest()->get();
            return view('admin.loans.index', compact('loans'));
        }
        
        $loans = Loan::where('user_id', Auth::id())->latest()->get();
        return view('employee.loans.index', compact('loans'));
    }

    public function store(Request $request)
    {
        // Check if there's already a pending loan
        $pendingLoan = Loan::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->first();

        if ($pendingLoan) {
            return back()->with('error', 'Anda masih memiliki permintaan kasbon yang sedang diproses. Harap tunggu konfirmasi admin.');
        }

        $request->validate([
            'amount' => 'required|integer|min:1000',
            'reason' => 'required|string',
        ]);

        $loan = Loan::create([
            'user_id' => Auth::id(),
            'amount' => $request->amount,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        // Notify Admins
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            /** @var User $admin */
            $admin->notify(new \App\Notifications\AppNotification([
                'title' => 'Permintaan Kasbon Baru',
                'message' => Auth::user()->name . ' mengajukan kasbon sebesar Rp' . number_format($request->amount),
                'icon' => 'activity',
                'color' => 'yellow',
                'url' => route('admin.loans.index', [], false),
                'type' => 'loan_request',
            ]));
        }

        return back()->with('success', 'Permintaan kasbon berhasil dikirim.');
    }

    public function updateStatus(Request $request, Loan $loan)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'admin_note' => 'nullable|string',
        ]);

        $loan->update([
            'status' => $request->status,
            'admin_note' => $request->admin_note,
        ]);

        // Notify Employee
        /** @var User $employee */
        $employee = $loan->user;
        $employee->notify(new \App\Notifications\AppNotification([
            'title' => 'Update Status Kasbon',
            'message' => 'Permintaan kasbon Anda senilai Rp' . number_format($loan->amount) . ' telah ' . ($request->status == 'approved' ? 'DISETUJUI' : 'DITOLAK'),
            'icon' => 'activity',
            'color' => $request->status == 'approved' ? 'green' : 'red',
            'url' => route('employee.loans.index', [], false),
            'type' => 'loan_update',
        ]));

        return back()->with('success', 'Status kasbon berhasil diperbarui.');
    }
}

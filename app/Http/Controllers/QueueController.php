<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class QueueController extends Controller
{
    /**
     * Halaman display antrian publik (TV/monitor outlet)
     */
    public function display()
    {
        return view('queue.display');
    }

    /**
     * Halaman antrian versi dashboard admin/karyawan
     */
    public function dashboardIndex()
    {
        return view('queues.dashboard-index');
    }

    /**
     * API: Ambil data antrian terkini (untuk polling real-time)
     */
    public function getData()
    {
        // Antrian yang sedang diproses
        $processing = Order::with(['user', 'service'])
            ->whereIn('status', ['processing', 'washing', 'drying', 'finishing'])
            ->latest()
            ->limit(8)
            ->get()
            ->map(fn($o) => [
                'queue_number' => $o->queue_number,
                'name'         => $o->user->name,
                'service'      => $o->service->name,
                'category'     => $o->service->category,
                'status'       => $o->status,
            ]);

        // Antrian siap diambil
        $ready = Order::with(['user', 'service'])
            ->where('status', 'ready')
            ->latest()
            ->limit(8)
            ->get()
            ->map(fn($o) => [
                'queue_number' => $o->queue_number,
                'name'         => $o->user->name,
                'service'      => $o->service->name,
                'category'     => $o->service->category,
                'status'       => 'ready',
            ]);

        // Antrian menunggu (pending)
        $pending = Order::with(['user', 'service'])
            ->where('status', 'pending')
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn($o) => [
                'queue_number' => $o->queue_number,
                'name'         => $o->user->name,
                'service'      => $o->service->name,
                'category'     => $o->service->category,
                'status'       => 'pending',
            ]);

        return response()->json([
            'processing' => $processing,
            'ready'      => $ready,
            'pending'    => $pending,
            'time'       => now()->format('H:i:s'),
            'date'       => now()->translatedFormat('l, d F Y'),
        ]);
    }

    /**
     * Halaman cek antrian untuk pelanggan (cek by nomor antrian)
     */
    public function check(Request $request)
    {
        $queueNumber = $request->input('q');
        $order = null;

        if ($queueNumber) {
            $order = Order::with(['user', 'service'])
                ->where('queue_number', strtoupper($queueNumber))
                ->first();
        }

        return view('queue.check', compact('order', 'queueNumber'));
    }
}

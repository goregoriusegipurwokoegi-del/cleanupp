<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Service;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::whereNotNull('rating')
            ->with(['user', 'service', 'employee']);

        // Filters
        if ($request->rating) {
            $query->where('rating', $request->rating);
        }
        if ($request->service_id) {
            $query->where('service_id', $request->service_id);
        }

        $testimonials = $query->latest()->paginate(10);

        // Stats
        $avgRating = Order::whereNotNull('rating')->avg('rating') ?? 0;
        $totalTestimonials = Order::whereNotNull('rating')->count();
        
        $satisfactionStats = [
            '5' => Order::where('rating', 5)->count(),
            '4' => Order::where('rating', 4)->count(),
            '3' => Order::where('rating', 3)->count(),
            '2' => Order::where('rating', 2)->count(),
            '1' => Order::where('rating', 1)->count(),
        ];

        $services = Service::all();

        return view('admin.testimonials.index', compact(
            'testimonials', 
            'avgRating', 
            'totalTestimonials', 
            'satisfactionStats', 
            'services'
        ));
    }
}

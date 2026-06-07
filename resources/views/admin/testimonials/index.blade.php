@extends('layouts.premium-dashboard')

@section('page_title', 'Manajemen Testimoni')

@section('content')
<div class="container mx-auto px-4 py-4">
    <!-- Header & Stats -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-8">
        <div class="lg:col-span-8">
            
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-6">
                <div class="glass-card p-4 text-center">
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Rating Rata-rata</p>
                    <div class="flex items-center justify-center gap-2">
                        <span class="text-3xl font-black text-white">{{ number_format($avgRating, 1) }}</span>
                        <div class="flex text-amber-500">
                            @for($i=1; $i<=5; $i++)
                                <svg class="w-4 h-4 {{ $i <= round($avgRating) ? 'fill-current' : 'text-gray-700' }}" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"></path></svg>
                            @endfor
                        </div>
                    </div>
                </div>
                <div class="glass-card p-4 text-center">
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Total Ulasan</p>
                    <span class="text-3xl font-black text-white">{{ $totalTestimonials }}</span>
                </div>
                <div class="glass-card p-4 text-center">
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Loyalitas</p>
                    <span class="text-3xl font-black text-emerald-500">{{ $totalTestimonials > 0 ? round(($satisfactionStats['5'] + $satisfactionStats['4']) / $totalTestimonials * 100) : 0 }}%</span>
                </div>
            </div>
        </div>

        <div class="lg:col-span-4">
            <div class="glass-card p-4 h-full">
                <h4 class="text-sm font-bold text-white mb-4">Distribusi Rating</h4>
                <div class="space-y-3">
                    @foreach(['5','4','3','2','1'] as $star)
                        <div class="flex items-center gap-3">
                            <span class="text-xs text-gray-400 w-4">{{ $star }}⭐</span>
                            <div class="flex-grow h-2 bg-gray-800 rounded-full overflow-hidden">
                                <div class="h-full bg-primary" style="width: {{ $totalTestimonials > 0 ? ($satisfactionStats[$star] / $totalTestimonials * 100) : 0 }}%"></div>
                            </div>
                            <span class="text-xs text-gray-500 w-8 text-right">{{ $satisfactionStats[$star] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="glass-card p-4 mb-6">
        <form action="{{ route('admin.testimonials.index') }}" method="GET" class="flex flex-wrap gap-4">
            <div class="flex-grow min-w-[150px]">
                <select name="rating" class="w-full bg-gray-800 border-none text-white text-sm p-3 rounded-xl focus:ring-1 focus:ring-primary outline-none cursor-pointer">
                    <option value="">Semua Rating</option>
                    @for($i=5; $i>=1; $i--)
                        <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>{{ $i }} Bintang</option>
                    @endfor
                </select>
            </div>
            <div class="flex-grow min-w-[150px]">
                <select name="service_id" class="w-full bg-gray-800 border-none text-white text-sm p-3 rounded-xl focus:ring-1 focus:ring-primary outline-none cursor-pointer">
                    <option value="">Semua Layanan</option>
                    @foreach($services as $service)
                        <option value="{{ $service->id }}" {{ request('service_id') == $service->id ? 'selected' : '' }}>{{ $service->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-primary text-bg font-bold px-8 py-3 rounded-xl hover:scale-105 transition-transform">
                Filter
            </button>
        </form>
    </div>

    <!-- Testimonials List -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @forelse($testimonials as $testimonial)
            <div class="glass-card p-4 hover:border-primary/30 transition-all">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary to-secondary flex items-center justify-center text-white font-bold">
                            {{ strtoupper(substr($testimonial->user->name, 0, 1)) }}
                        </div>
                        <div>
                            <h4 class="text-white font-bold text-sm">{{ $testimonial->user->name }}</h4>
                            <p class="text-[10px] text-gray-500">{{ $testimonial->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    <div class="flex text-amber-500">
                        @for($i=1; $i<=5; $i++)
                            <svg class="w-3 h-3 {{ $i <= $testimonial->rating ? 'fill-current' : 'text-gray-800' }}" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"></path></svg>
                        @endfor
                    </div>
                </div>

                <div class="bg-black/20 p-3 rounded-xl mb-4 border border-white/5">
                    <p class="text-gray-300 text-xs italic">"{{ $testimonial->review }}"</p>
                </div>

                <div class="flex flex-wrap gap-4 text-[10px]">
                    <div class="flex flex-col">
                        <span class="text-gray-600 font-bold uppercase mb-1">Layanan</span>
                        <span class="text-gray-300">{{ $testimonial->service->name }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-gray-600 font-bold uppercase mb-1">Karyawan</span>
                        <span class="text-primary font-bold">{{ $testimonial->employee ? $testimonial->employee->name : 'N/A' }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-gray-600 font-bold uppercase mb-1">Sepatu</span>
                        <span class="text-gray-300">{{ $testimonial->shoe_name }}</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="md:col-span-2 py-20 text-center glass-card">
                <p class="text-gray-500">Belum ada testimoni yang masuk.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $testimonials->links() }}
    </div>
</div>

<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.03);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 20px;
    }
</style>
@endsection

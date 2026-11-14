@extends('layouts.app')

@section('title', 'My Orders - Bombay Fashion Amritsar')

@section('content')
<div class="py-10 bg-festive-ivory/50 min-h-[60vh]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-serif font-bold text-festive-plum">My Order History</h1>
                <p class="text-xs text-festive-plum/70 mt-1">Track current shipments and view past purchases from Bombay Fashion.</p>
            </div>
            <a href="{{ route('orders.track') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-festive-rose hover:underline">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Track by Order Number
            </a>
        </div>

        @if($orders->isEmpty())
            {{-- Clean Empty State - STRICT NO FAKE DATA --}}
            <div class="glass-card rounded-3xl p-12 text-center border border-white/80 bg-white/80 max-w-lg mx-auto shadow-md">
                <div class="w-20 h-20 rounded-full bg-festive-blush mx-auto flex items-center justify-center text-festive-rose mb-5">
                    <svg class="w-10 h-10 stroke-current" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-serif font-bold text-festive-plum mb-2">No Orders Placed Yet</h2>
                <p class="text-sm text-festive-plum/70 mb-6">
                    You haven't placed any orders yet. Discover our latest Amritsar festive wear, sarees, and uniforms.
                </p>
                <a href="{{ route('products.index') }}" class="inline-flex items-center px-6 py-3 rounded-xl bg-festive-rose text-white text-xs font-bold shadow-md hover:bg-festive-rose/90 transition-all">
                    Start Shopping
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach($orders as $ord)
                    <div class="glass-card rounded-3xl p-6 border border-white/80 bg-white/80 shadow-sm hover:shadow-md transition-all">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-festive-plum/10 gap-3">
                            <div class="flex flex-wrap items-center gap-3">
                                <span class="font-mono font-bold text-festive-plum text-sm">#{{ $ord->order_number }}</span>
                                <span class="text-xs text-festive-plum/50">•</span>
                                <span class="text-xs text-festive-plum/70">{{ $ord->created_at->format('d M Y, h:i A') }}</span>
                            </div>

                            <div class="flex items-center gap-2">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
                                        'confirmed' => 'bg-blue-100 text-blue-800 border-blue-200',
                                        'packed' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                                        'shipped' => 'bg-purple-100 text-purple-800 border-purple-200',
                                        'delivered' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                        'cancelled' => 'bg-rose-100 text-rose-800 border-rose-200',
                                        'returned' => 'bg-gray-100 text-gray-800 border-gray-200',
                                    ];
                                @endphp
                                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider border {{ $statusColors[$ord->status] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ ucfirst($ord->status) }}
                                </span>
                            </div>
                        </div>

                        {{-- Items row --}}
                        <div class="py-4 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                            <div class="space-y-1 text-xs text-festive-plum">
                                <span class="font-bold">{{ $ord->items->count() }} {{ Str::plural('Item', $ord->items->count()) }}:</span>
                                <p class="text-festive-plum/70 truncate max-w-xl">
                                    {{ $ord->items->pluck('product_name')->implode(', ') }}
                                </p>
                            </div>

                            <div class="flex items-center gap-4 w-full md:w-auto justify-between md:justify-end">
                                <div>
                                    <span class="text-[10px] text-festive-plum/50 uppercase block">Total</span>
                                    <span class="font-serif font-bold text-base text-festive-rose">
                                        ₹{{ number_format($ord->grand_total, 2) }}
                                    </span>
                                </div>

                                <div class="flex items-center gap-2">
                                    <a 
                                        href="{{ route('orders.show', $ord->order_number) }}" 
                                        class="px-4 py-2 rounded-xl text-xs font-bold bg-festive-rose text-white hover:bg-festive-rose/90 shadow transition-all"
                                    >
                                        View Details
                                    </a>
                                    <a 
                                        href="{{ route('orders.invoice', $ord->order_number) }}" 
                                        class="p-2 rounded-xl text-xs font-medium border border-festive-plum/20 text-festive-plum hover:bg-festive-blush transition-all"
                                        title="Download Invoice"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="mt-8">
                    {{ $orders->links() }}
                </div>
            </div>
        @endif

    </div>
</div>
@endsection

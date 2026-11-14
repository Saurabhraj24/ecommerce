@extends('layouts.app')

@section('title', 'My Wishlist - Bombay Fashion Amritsar')

@section('content')
<div class="py-10 bg-festive-ivory/50 min-h-[60vh]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-8">
            <h1 class="text-3xl font-serif font-bold text-festive-plum">My Wishlist</h1>
            <p class="text-xs text-festive-plum/70 mt-1">Saved clothing pieces and favorites from Bombay Fashion.</p>
        </div>

        @if($wishlists->isEmpty())
            {{-- Clean Empty State - STRICT NO FAKE DATA --}}
            <div class="glass-card rounded-3xl p-12 text-center border border-white/80 bg-white/80 max-w-lg mx-auto shadow-md">
                <div class="w-20 h-20 rounded-full bg-festive-blush mx-auto flex items-center justify-center text-festive-rose mb-5">
                    <svg class="w-10 h-10 fill-current" viewBox="0 0 24 24">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-serif font-bold text-festive-plum mb-2">Your Wishlist is Empty</h2>
                <p class="text-sm text-festive-plum/70 mb-6">
                    Tap the heart icon on any product or saree to save it for later when planning your visit or order.
                </p>
                <a href="{{ route('products.index') }}" class="inline-flex items-center px-6 py-3 rounded-xl bg-festive-rose text-white text-xs font-bold shadow-md hover:bg-festive-rose/90 transition-all">
                    Explore Collections
                </a>
            </div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                @foreach($wishlists as $item)
                    @if($item->product)
                        <div class="relative group">
                            @include('products.partials.card', ['product' => $item->product])
                            
                            {{-- Wishlist Remove overlay --}}
                            <form action="{{ route('wishlist.toggle') }}" method="POST" class="mt-2 text-center">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $item->product->id }}">
                                <button type="submit" class="text-xs text-rose-600 hover:underline font-semibold">
                                    Remove from Wishlist
                                </button>
                            </form>
                        </div>
                    @endif
                @endforeach
            </div>

            <div class="mt-8">
                {{ $wishlists->links() }}
            </div>
        @endif

    </div>
</div>
@endsection

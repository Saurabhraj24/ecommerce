@extends('layouts.app')

@section('title', 'Create Account - Bombay Fashion Amritsar')

@section('content')
<div class="py-16 bg-festive-ivory/50 min-h-[75vh] flex items-center justify-center">
    <div class="max-w-md w-full mx-auto px-4">
        
        <div class="glass-card rounded-3xl p-8 sm:p-10 border border-white/80 bg-white/90 shadow-xl">
            
            <div class="text-center mb-8">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-festive-blush text-festive-rose mb-3">
                    New Customer Registration
                </span>
                <h1 class="text-2xl sm:text-3xl font-serif font-bold text-festive-plum">
                    Join Bombay Fashion
                </h1>
                <p class="text-xs text-festive-plum/70 mt-1">
                    Create an account to track orders and save your favorites
                </p>
            </div>

            <form action="{{ route('register.post') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-festive-plum mb-1">Full Name *</label>
                    <input 
                        type="text" 
                        name="name" 
                        value="{{ old('name') }}" 
                        required 
                        autofocus
                        class="w-full text-xs rounded-xl border-festive-plum/20 bg-white p-3 text-festive-plum focus:ring-festive-rose"
                        placeholder="e.g. Jaswinder Singh"
                    >
                </div>

                <div>
                    <label class="block text-xs font-semibold text-festive-plum mb-1">Email Address *</label>
                    <input 
                        type="email" 
                        name="email" 
                        value="{{ old('email') }}" 
                        required 
                        class="w-full text-xs rounded-xl border-festive-plum/20 bg-white p-3 text-festive-plum focus:ring-festive-rose"
                        placeholder="you@example.com"
                    >
                </div>

                <div>
                    <label class="block text-xs font-semibold text-festive-plum mb-1">Phone Number (optional)</label>
                    <input 
                        type="tel" 
                        name="phone" 
                        value="{{ old('phone') }}" 
                        class="w-full text-xs rounded-xl border-festive-plum/20 bg-white p-3 text-festive-plum focus:ring-festive-rose"
                        placeholder="10-digit mobile number"
                    >
                </div>

                <div>
                    <label class="block text-xs font-semibold text-festive-plum mb-1">Password *</label>
                    <input 
                        type="password" 
                        name="password" 
                        required 
                        class="w-full text-xs rounded-xl border-festive-plum/20 bg-white p-3 text-festive-plum focus:ring-festive-rose"
                        placeholder="Minimum 8 characters"
                    >
                </div>

                <div>
                    <label class="block text-xs font-semibold text-festive-plum mb-1">Confirm Password *</label>
                    <input 
                        type="password" 
                        name="password_confirmation" 
                        required 
                        class="w-full text-xs rounded-xl border-festive-plum/20 bg-white p-3 text-festive-plum focus:ring-festive-rose"
                        placeholder="Re-enter password"
                    >
                </div>

                <div class="pt-2">
                    <button 
                        type="submit" 
                        class="w-full py-3.5 px-6 rounded-xl font-bold text-xs uppercase tracking-wider bg-festive-rose text-white shadow-lg shadow-festive-rose/25 hover:bg-festive-rose/90 hover:scale-[1.01] active:scale-[0.99] transition-all"
                    >
                        Create Account
                    </button>
                </div>
            </form>

            <div class="mt-8 pt-6 border-t border-festive-plum/10 text-center text-xs text-festive-plum/70">
                Already registered with Bombay Fashion? 
                <a href="{{ route('login') }}" class="font-bold text-festive-rose hover:underline ml-1">
                    Sign In
                </a>
            </div>

        </div>

    </div>
</div>
@endsection

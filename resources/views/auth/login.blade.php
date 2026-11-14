@extends('layouts.app')

@section('title', 'Login - Bombay Fashion Amritsar')

@section('content')
<div class="py-16 bg-festive-ivory/50 min-h-[75vh] flex items-center justify-center">
    <div class="max-w-md w-full mx-auto px-4">
        
        <div class="glass-card rounded-3xl p-8 sm:p-10 border border-white/80 bg-white/90 shadow-xl">
            
            <div class="text-center mb-8">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-festive-blush text-festive-rose mb-3">
                    Customer & Staff Portal
                </span>
                <h1 class="text-2xl sm:text-3xl font-serif font-bold text-festive-plum">
                    Welcome Back
                </h1>
                <p class="text-xs text-festive-plum/70 mt-1">
                    Log in to your Bombay Fashion account
                </p>
            </div>

            <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-festive-plum mb-1">Email Address *</label>
                    <input 
                        type="email" 
                        name="email" 
                        value="{{ old('email') }}" 
                        required 
                        autofocus
                        class="w-full text-xs rounded-xl border-festive-plum/20 bg-white p-3 text-festive-plum focus:ring-festive-rose focus:border-festive-rose"
                        placeholder="you@example.com"
                    >
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-semibold text-festive-plum">Password *</label>
                        <a href="{{ route('password.request') }}" class="text-xs text-festive-rose hover:underline font-medium">
                            Forgot password?
                        </a>
                    </div>
                    <input 
                        type="password" 
                        name="password" 
                        required 
                        class="w-full text-xs rounded-xl border-festive-plum/20 bg-white p-3 text-festive-plum focus:ring-festive-rose focus:border-festive-rose"
                        placeholder="••••••••"
                    >
                </div>

                <div class="flex items-center">
                    <label class="flex items-center gap-2 text-xs text-festive-plum cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded text-festive-rose focus:ring-festive-rose">
                        <span>Keep me logged in</span>
                    </label>
                </div>

                <div class="pt-2">
                    <button 
                        type="submit" 
                        class="w-full py-3.5 px-6 rounded-xl font-bold text-xs uppercase tracking-wider bg-festive-rose text-white shadow-lg shadow-festive-rose/25 hover:bg-festive-rose/90 hover:scale-[1.01] active:scale-[0.99] transition-all"
                    >
                        Sign In
                    </button>
                </div>
            </form>

            <div class="mt-8 pt-6 border-t border-festive-plum/10 text-center text-xs text-festive-plum/70">
                Don't have an account yet? 
                <a href="{{ route('register') }}" class="font-bold text-festive-rose hover:underline ml-1">
                    Register Here
                </a>
            </div>

        </div>

    </div>
</div>
@endsection

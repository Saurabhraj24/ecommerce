@extends('layouts.app')

@section('title', 'Set New Password - Bombay Fashion Amritsar')

@section('content')
<div class="py-16 bg-festive-ivory/50 min-h-[70vh] flex items-center justify-center">
    <div class="max-w-md w-full mx-auto px-4">
        
        <div class="glass-card rounded-3xl p-8 sm:p-10 border border-white/80 bg-white/90 shadow-xl">
            
            <div class="text-center mb-8">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-festive-blush text-festive-rose mb-3">
                    Security Update
                </span>
                <h1 class="text-2xl sm:text-3xl font-serif font-bold text-festive-plum">
                    Choose New Password
                </h1>
                <p class="text-xs text-festive-plum/70 mt-1">
                    Enter your new password to regain access
                </p>
            </div>

            <form action="{{ route('password.update') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div>
                    <label class="block text-xs font-semibold text-festive-plum mb-1">Email Address *</label>
                    <input 
                        type="email" 
                        name="email" 
                        value="{{ old('email', $email) }}" 
                        required 
                        class="w-full text-xs rounded-xl border-festive-plum/20 bg-white p-3 text-festive-plum focus:ring-festive-rose"
                    >
                </div>

                <div>
                    <label class="block text-xs font-semibold text-festive-plum mb-1">New Password *</label>
                    <input 
                        type="password" 
                        name="password" 
                        required 
                        class="w-full text-xs rounded-xl border-festive-plum/20 bg-white p-3 text-festive-plum focus:ring-festive-rose"
                        placeholder="Minimum 8 characters"
                    >
                </div>

                <div>
                    <label class="block text-xs font-semibold text-festive-plum mb-1">Confirm New Password *</label>
                    <input 
                        type="password" 
                        name="password_confirmation" 
                        required 
                        class="w-full text-xs rounded-xl border-festive-plum/20 bg-white p-3 text-festive-plum focus:ring-festive-rose"
                        placeholder="Re-enter new password"
                    >
                </div>

                <div class="pt-2">
                    <button 
                        type="submit" 
                        class="w-full py-3.5 px-6 rounded-xl font-bold text-xs uppercase tracking-wider bg-festive-rose text-white shadow-lg shadow-festive-rose/25 hover:bg-festive-rose/90 transition-all"
                    >
                        Update Password
                    </button>
                </div>
            </form>

        </div>

    </div>
</div>
@endsection

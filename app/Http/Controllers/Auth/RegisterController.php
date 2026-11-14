<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {}

    public function showRegistrationForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'regex:/^[0-9+\s\-]{10,15}$/'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $userRole = Role::where('name', Role::USER)->firstOrFail();

        $user = User::create([
            'role_id' => $userRole->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'is_active' => true,
        ]);

        Auth::login($user);

        // Merge guest cart
        $this->cartService->getCart();

        ActivityLog::log('user_registered', User::class, $user->id, ['email' => $user->email]);

        return redirect()->route('home')->with('success', 'Registration successful! Welcome to Bombay Fashion, Amritsar.');
    }
}

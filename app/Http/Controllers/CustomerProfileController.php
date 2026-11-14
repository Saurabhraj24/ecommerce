<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class CustomerProfileController extends Controller
{
    public function profile(): View
    {
        $user = Auth::user();
        return view('profile.index', compact('user'));
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['required', 'string', 'regex:/^[0-9+\s\-]{10,15}$/'],
        ]);

        $user->update($validated);

        return back()->with('success', 'Profile details updated successfully.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user->update(['password' => Hash::make($validated['password'])]);

        return back()->with('success', 'Password changed successfully.');
    }

    public function addresses(): View
    {
        $addresses = Auth::user()->addresses()->latest()->get();
        return view('profile.addresses', compact('addresses'));
    }

    public function storeAddress(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|regex:/^[0-9+\s\-]{10,15}$/',
            'alternate_phone' => 'nullable|string|max:20',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'landmark' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|regex:/^[0-9]{6}$/',
            'address_type' => 'required|in:home,work,other',
            'is_default' => 'nullable|boolean',
        ]);

        $userId = Auth::id();

        if (!empty($validated['is_default'])) {
            Address::where('user_id', $userId)->update(['is_default' => false]);
        }

        Address::create([
            'user_id' => $userId,
            'full_name' => $validated['full_name'],
            'phone' => $validated['phone'],
            'alternate_phone' => $validated['alternate_phone'] ?? null,
            'address_line1' => $validated['address_line1'],
            'address_line2' => $validated['address_line2'] ?? null,
            'landmark' => $validated['landmark'] ?? null,
            'city' => $validated['city'],
            'state' => $validated['state'],
            'postal_code' => $validated['postal_code'],
            'address_type' => $validated['address_type'],
            'is_default' => !empty($validated['is_default']) || (Address::where('user_id', $userId)->count() === 0),
        ]);

        return back()->with('success', 'New delivery address saved.');
    }

    public function deleteAddress(int $id): RedirectResponse
    {
        $address = Address::where('user_id', Auth::id())->findOrFail($id);
        $wasDefault = $address->is_default;
        $address->delete();

        if ($wasDefault) {
            $first = Address::where('user_id', Auth::id())->first();
            if ($first) {
                $first->update(['is_default' => true]);
            }
        }

        return back()->with('info', 'Address removed from your address book.');
    }

    public function setDefaultAddress(int $id): RedirectResponse
    {
        $userId = Auth::id();
        Address::where('user_id', $userId)->update(['is_default' => false]);
        Address::where('user_id', $userId)->where('id', $id)->update(['is_default' => true]);

        return back()->with('success', 'Default delivery address updated.');
    }
}

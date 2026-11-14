<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class StaffController extends Controller
{
    public function index(): View
    {
        $staffRole = Role::where('name', Role::STAFF)->first();
        $adminRole = Role::where('name', Role::ADMIN)->first();

        $staffMembers = User::whereIn('role_id', [$staffRole?->id, $adminRole?->id])
            ->with('role')
            ->latest()
            ->paginate(15);

        return view('admin.staff.index', compact('staffMembers'));
    }

    public function create(): View
    {
        $roles = Role::whereIn('name', [Role::STAFF, Role::ADMIN])->get();
        $availablePermissions = User::availablePermissions();
        return view('admin.staff.create', compact('roles', 'availablePermissions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'employee_id' => 'nullable|string|max:50|unique:users,employee_id',
            'designation' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'date_of_joining' => 'nullable|date',
            'address' => 'nullable|string|max:500',
            'emergency_contact_name' => 'nullable|string|max:100',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'salary' => 'nullable|numeric|min:0',
            'bank_name' => 'nullable|string|max:100',
            'bank_account_no' => 'nullable|string|max:50',
            'bank_ifsc' => 'nullable|string|max:25',
            'upi_id' => 'nullable|string|max:75',
            'weekly_off' => 'nullable|string|max:20',
            'shift_timings' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1000',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|in:' . implode(',', array_keys(User::availablePermissions())),
            'password' => ['required', Password::min(8)],
        ]);

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        $selectedRole = Role::find($validated['role_id']);
        $isAdminRole = $selectedRole?->name === Role::ADMIN;

        $user = User::create([
            'role_id' => $validated['role_id'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'avatar' => $avatarPath,
            'employee_id' => $validated['employee_id'] ?? null,
            'designation' => $validated['designation'] ?? null,
            'department' => $validated['department'] ?? null,
            'date_of_joining' => $validated['date_of_joining'] ?? null,
            'address' => $validated['address'] ?? null,
            'emergency_contact_name' => $validated['emergency_contact_name'] ?? null,
            'emergency_contact_phone' => $validated['emergency_contact_phone'] ?? null,
            'salary' => $validated['salary'] ?? null,
            'bank_name' => $validated['bank_name'] ?? null,
            'bank_account_no' => $validated['bank_account_no'] ?? null,
            'bank_ifsc' => $validated['bank_ifsc'] ?? null,
            'upi_id' => $validated['upi_id'] ?? null,
            'weekly_off' => $validated['weekly_off'] ?? 'Sunday',
            'shift_timings' => $validated['shift_timings'] ?? '10:00 AM - 08:30 PM',
            'notes' => $validated['notes'] ?? null,
            'permissions' => $isAdminRole ? null : ($validated['permissions'] ?? []),
            'password' => Hash::make($validated['password']),
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        ActivityLog::log('staff_created', User::class, $user->id, ['name' => $user->name, 'email' => $user->email, 'designation' => $user->designation]);

        return redirect()->route('admin.staff.index')->with('success', 'Staff account created successfully with assigned permissions.');
    }

    public function edit(User $staff): View
    {
        $roles = Role::whereIn('name', [Role::STAFF, Role::ADMIN])->get();
        $availablePermissions = User::availablePermissions();
        return view('admin.staff.edit', compact('staff', 'roles', 'availablePermissions'));
    }

    public function update(Request $request, User $staff): RedirectResponse
    {
        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($staff->id)],
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'employee_id' => ['nullable', 'string', 'max:50', Rule::unique('users', 'employee_id')->ignore($staff->id)],
            'designation' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'date_of_joining' => 'nullable|date',
            'address' => 'nullable|string|max:500',
            'emergency_contact_name' => 'nullable|string|max:100',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'salary' => 'nullable|numeric|min:0',
            'bank_name' => 'nullable|string|max:100',
            'bank_account_no' => 'nullable|string|max:50',
            'bank_ifsc' => 'nullable|string|max:25',
            'upi_id' => 'nullable|string|max:75',
            'weekly_off' => 'nullable|string|max:20',
            'shift_timings' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1000',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|in:' . implode(',', array_keys(User::availablePermissions())),
            'password' => ['nullable', Password::min(8)],
            'is_active' => 'nullable|boolean',
        ]);

        $selectedRole = Role::find($validated['role_id']);
        $isAdminRole = $selectedRole?->name === Role::ADMIN;

        $updates = [
            'role_id' => $validated['role_id'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'employee_id' => $validated['employee_id'] ?? null,
            'designation' => $validated['designation'] ?? null,
            'department' => $validated['department'] ?? null,
            'date_of_joining' => $validated['date_of_joining'] ?? null,
            'address' => $validated['address'] ?? null,
            'emergency_contact_name' => $validated['emergency_contact_name'] ?? null,
            'emergency_contact_phone' => $validated['emergency_contact_phone'] ?? null,
            'salary' => $validated['salary'] ?? null,
            'bank_name' => $validated['bank_name'] ?? null,
            'bank_account_no' => $validated['bank_account_no'] ?? null,
            'bank_ifsc' => $validated['bank_ifsc'] ?? null,
            'upi_id' => $validated['upi_id'] ?? null,
            'weekly_off' => $validated['weekly_off'] ?? 'Sunday',
            'shift_timings' => $validated['shift_timings'] ?? '10:00 AM - 08:30 PM',
            'notes' => $validated['notes'] ?? null,
            'permissions' => $isAdminRole ? null : ($request->input('permissions', [])),
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($request->hasFile('avatar')) {
            $updates['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        if (!empty($validated['password'])) {
            $updates['password'] = Hash::make($validated['password']);
        }

        $staff->update($updates);

        ActivityLog::log('staff_updated', User::class, $staff->id, ['email' => $staff->email, 'designation' => $staff->designation]);

        return redirect()->route('admin.staff.index')->with('success', 'Staff profile & permissions updated successfully.');
    }

    public function destroy(User $staff): RedirectResponse
    {
        if ($staff->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own admin account.');
        }

        $name = $staff->name;
        $staff->delete();

        ActivityLog::log('staff_deleted', User::class, null, ['name' => $name]);

        return back()->with('info', "Staff account '{$name}' removed.");
    }
}

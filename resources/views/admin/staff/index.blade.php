@extends('layouts.admin')

@section('title', 'Manage Staff - Bombay Fashion Admin')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-serif font-bold text-plum">Staff & Admin Team</h1>
            <p class="text-xs text-plum/70 mt-1">Manage showroom sales executives, master tailors, cashiers, and store administrators with complete profiles.</p>
        </div>
        <a 
            href="{{ route('admin.staff.create') }}" 
            class="px-4 py-2.5 rounded-xl text-xs font-bold bg-primary text-white shadow hover:bg-primary-700 transition-all flex items-center gap-1.5 self-start sm:self-auto cursor-pointer"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add New Staff Account
        </a>
    </div>

    <div class="glass-card rounded-3xl border border-white/80 bg-white/90 shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-plum/10 text-plum/60 font-semibold uppercase tracking-wider text-[10px] bg-plum/5">
                        <th class="py-3.5 px-4">Staff Member & Role</th>
                        <th class="py-3.5 px-4">Employee ID</th>
                        <th class="py-3.5 px-4">Department / Section</th>
                        <th class="py-3.5 px-4">Direct Contact</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-plum/5">
                    @foreach($staffMembers as $staff)
                        <tr class="hover:bg-plum/5 transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <img 
                                        src="{{ $staff->avatar_url }}" 
                                        alt="{{ $staff->name }}" 
                                        class="w-10 h-10 rounded-xl object-cover border border-plum/10 shadow-xs shrink-0"
                                    >
                                    <div>
                                        <div class="flex items-center gap-1.5">
                                            <span class="font-bold text-plum text-sm">{{ $staff->name }}</span>
                                            @if($staff->id === auth()->id())
                                                <span class="text-[9px] text-white bg-primary px-1.5 py-0.2 rounded font-bold uppercase tracking-wider">You</span>
                                            @endif
                                        </div>
                                        <span class="text-[11px] text-plum/60 block">
                                            {{ $staff->designation ?: ($staff->isAdmin() ? 'Super Administrator' : 'Showroom Associate') }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <td class="py-3.5 px-4 font-mono font-bold text-plum/70 text-[11px]">
                                {{ $staff->employee_id ?: '—' }}
                            </td>

                            <td class="py-3.5 px-4">
                                @if($staff->department)
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-semibold bg-plum/5 text-plum border border-plum/10">
                                        {{ $staff->department }}
                                    </span>
                                @else
                                    <span class="text-plum/40 italic">General Showroom</span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 text-plum/80">
                                <span class="font-medium block text-plum">{{ $staff->phone ?: '—' }}</span>
                                <span class="text-[11px] text-plum/60 font-mono">{{ $staff->email }}</span>
                            </td>

                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $staff->is_active ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-rose-100 text-rose-800 border border-rose-200' }}">
                                    {{ $staff->is_active ? 'Active' : 'Disabled' }}
                                </span>
                            </td>

                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a 
                                        href="{{ route('admin.staff.edit', $staff->id) }}" 
                                        class="p-2 rounded-xl border border-plum/20 text-plum hover:bg-plum/5 hover:text-primary transition-all"
                                        title="Edit Full Profile"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>

                                    @if($staff->id !== auth()->id())
                                        <form action="{{ route('admin.staff.destroy', $staff->id) }}" method="POST" onsubmit="return confirm('Remove staff member {{ $staff->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button 
                                                type="submit" 
                                                class="p-2 rounded-xl border border-rose-200 text-rose-600 hover:bg-rose-50 transition-all cursor-pointer"
                                                title="Remove Account"
                                            >
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($staffMembers->hasPages())
            <div class="p-4 border-t border-plum/10">
                {{ $staffMembers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

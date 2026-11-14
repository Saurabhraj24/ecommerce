@extends('layouts.admin')

@section('title', 'Manage Categories - Bombay Fashion Admin')

@section('content')
<div class="space-y-6">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-serif font-bold text-plum">Categories & Department Structure</h1>
            <p class="text-xs text-plum/70 mt-1">Manage main categories, subcategories, ordering, and Cloudinary category tiles.</p>
        </div>
        <a 
            href="{{ route('admin.categories.create') }}" 
            class="px-4 py-2.5 rounded-xl text-xs font-bold bg-primary text-white shadow-md hover:bg-primary-700 transition-all flex items-center gap-1.5 self-start sm:self-auto"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add New Category
        </a>
    </div>

    <div class="glass-card rounded-3xl border border-white/80 bg-white/85 shadow-md overflow-hidden">
        @if($categories->isEmpty())
            <div class="p-12 text-center text-plum/60 text-xs italic">
                No categories found. Seeders or admin can create categories.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-plum/10 text-plum/60 font-semibold uppercase tracking-wider text-[10px] bg-plum/5">
                            <th class="py-3 px-4">Image</th>
                            <th class="py-3 px-4">Category Name</th>
                            <th class="py-3 px-4">Parent Category</th>
                            <th class="py-3 px-4 text-center">Subcategories</th>
                            <th class="py-3 px-4 text-center">Products</th>
                            <th class="py-3 px-4 text-center">Order</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-plum/5">
                        @foreach($categories as $cat)
                            <tr class="hover:bg-plum/5 transition-colors">
                                <td class="py-3 px-4">
                                    <div class="w-10 h-10 rounded-lg bg-plum/10 overflow-hidden flex-shrink-0 border border-plum/10">
                                        @if($cat->image_url)
                                            <img src="{{ $cat->image_url }}" alt="{{ $cat->name }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-[9px] text-plum/40 font-serif">BF</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-4 font-bold text-plum">
                                    {{ $cat->name }}
                                    <span class="block text-[10px] text-plum/40 font-mono">{{ $cat->slug }}</span>
                                </td>
                                <td class="py-3 px-4 text-plum/70">
                                    @if($cat->parent)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] bg-plum/10 font-medium">
                                            {{ $cat->parent->name }}
                                        </span>
                                    @else
                                        <span class="text-[10px] font-bold uppercase text-primary">Root Category</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center font-bold text-plum">
                                    {{ $cat->children->count() }}
                                </td>
                                <td class="py-3 px-4 text-center font-bold text-primary">
                                    {{ $cat->products->count() }}
                                </td>
                                <td class="py-3 px-4 text-center font-mono">
                                    {{ $cat->display_order }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $cat->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-200 text-gray-700' }}">
                                        {{ $cat->is_active ? 'Active' : 'Disabled' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a 
                                            href="{{ route('admin.categories.edit', $cat->id) }}" 
                                            class="p-1.5 rounded-lg border border-plum/20 text-plum hover:bg-plum/5"
                                            title="Edit Category"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>

                                        @if($cat->products->count() === 0 && $cat->children->count() === 0)
                                            <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('Delete this category?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 rounded-lg border border-rose-200 text-rose-600 hover:bg-rose-50" title="Delete">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
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

            <div class="p-4 border-t border-plum/10">
                {{ $categories->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

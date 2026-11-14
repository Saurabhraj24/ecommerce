<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Services\CloudinaryImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(
        protected CloudinaryImageService $cloudinaryService
    ) {}

    public function index(): View
    {
        $categories = Category::with(['parent', 'children', 'products'])
            ->orderBy('parent_id')
            ->orderBy('display_order')
            ->paginate(20);

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        $parentCategories = Category::root()->orderBy('name')->get();
        return view('admin.categories.create', compact('parentCategories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string|max:1000',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $slug = Category::generateUniqueSlug($validated['name']);
        $imageUrl = null;
        $imagePublicId = null;

        if ($request->hasFile('image')) {
            $uploadRes = $this->cloudinaryService->upload($request->file('image'), 'bombay_fashion/categories');
            $imageUrl = $uploadRes['secure_url'];
            $imagePublicId = $uploadRes['public_id'];
        }

        $category = Category::create([
            'parent_id' => $validated['parent_id'] ?? null,
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'image_url' => $imageUrl,
            'image_public_id' => $imagePublicId,
            'display_order' => $validated['display_order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        ActivityLog::log('category_created', Category::class, $category->id, ['name' => $category->name]);

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully!');
    }

    public function edit(Category $category): View
    {
        $parentCategories = Category::root()
            ->where('id', '!=', $category->id)
            ->orderBy('name')
            ->get();

        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string|max:1000',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        if ($request->hasFile('image')) {
            if ($category->image_public_id) {
                $this->cloudinaryService->delete($category->image_public_id);
            }
            $uploadRes = $this->cloudinaryService->upload($request->file('image'), 'bombay_fashion/categories');
            $category->image_url = $uploadRes['secure_url'];
            $category->image_public_id = $uploadRes['public_id'];
        }

        $category->update([
            'parent_id' => $validated['parent_id'] ?? null,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'display_order' => $validated['display_order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        ActivityLog::log('category_updated', Category::class, $category->id, ['name' => $category->name]);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully!');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->count() > 0) {
            return back()->with('error', 'Cannot delete this category because it contains active products. Reassign the products first.');
        }

        if ($category->children()->count() > 0) {
            return back()->with('error', 'Cannot delete category with subcategories.');
        }

        if ($category->image_public_id) {
            $this->cloudinaryService->delete($category->image_public_id);
        }

        $name = $category->name;
        $category->delete();

        ActivityLog::log('category_deleted', Category::class, null, ['name' => $name]);

        return back()->with('info', "Category '{$name}' deleted.");
    }
}

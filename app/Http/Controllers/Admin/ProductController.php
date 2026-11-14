<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\SchoolUniform;
use App\Models\StockLog;
use App\Services\CloudinaryImageService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        protected CloudinaryImageService $cloudinaryService
    ) {}

    public function index(Request $request): View
    {
        $query = Product::with(['category', 'primaryImage', 'images', 'variants']);

        if ($request->filled('q')) {
            $keyword = trim($request->input('q'));
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('sku', 'like', "%{$keyword}%")
                  ->orWhere('fabric', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->filled('season')) {
            $query->where('season', $request->input('season'));
        }

        if ($request->filled('stock_status')) {
            if ($request->input('stock_status') === 'low') {
                $query->where('stock', '<=', 5);
            } elseif ($request->input('stock_status') === 'out') {
                $query->where('stock', '<=', 0);
            }
        }

        $products = $query->latest()->paginate(15)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:products,sku',
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'season' => 'required|in:winter,summer,all_season',
            'fabric' => 'nullable|string|max:100',
            'stock' => 'required|integer|min:0',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'is_new_arrival' => 'nullable|boolean',
            'is_best_seller' => 'nullable|boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',

            // Optional School Uniform Details
            'is_uniform' => 'nullable|boolean',
            'school_name' => 'required_if:is_uniform,1|nullable|string|max:255',
            'class_grade' => 'required_if:is_uniform,1|nullable|string|max:100',
            'gender' => 'required_if:is_uniform,1|nullable|in:boys,girls,unisex',
            'item_type' => 'required_if:is_uniform,1|nullable|string|max:100',

            // Optional Variants
            'variants.*.size' => 'nullable|string|max:50',
            'variants.*.color' => 'nullable|string|max:50',
            'variants.*.color_code' => 'nullable|string|max:10',
            'variants.*.sku' => 'nullable|string|max:100|distinct|unique:product_variants,sku',
            'variants.*.additional_price' => 'nullable|numeric|min:0',
            'variants.*.stock' => 'nullable|integer|min:0',
        ]);

        return DB::transaction(function () use ($request, $validated) {
            $user = auth()->user();

            $product = Product::create([
                'category_id' => $validated['category_id'],
                'name' => $validated['name'],
                'slug' => $slug,
                'sku' => strtoupper($validated['sku']),
                'short_description' => $validated['short_description'] ?? null,
                'description' => $validated['description'] ?? null,
                'price' => $validated['price'],
                'discount_price' => $validated['discount_price'] ?? null,
                'season' => $validated['season'],
                'fabric' => $validated['fabric'] ?? null,
                'stock' => $validated['stock'],
                'is_active' => $request->boolean('is_active', true),
                'is_featured' => $request->boolean('is_featured'),
                'is_new_arrival' => $request->boolean('is_new_arrival'),
                'is_best_seller' => $request->boolean('is_best_seller'),
                'created_by_id' => $user?->id,
                'creator_name' => $user?->name,
                'creator_email' => $user?->email,
                'meta_title' => $validated['meta_title'] ?? null,
                'meta_description' => $validated['meta_description'] ?? null,
            ]);

            // Save initial stock log
            StockLog::create([
                'product_id' => $product->id,
                'product_variant_id' => null,
                'user_id' => auth()->id(),
                'type' => 'manual_adjustment',
                'quantity_change' => $product->stock,
                'old_quantity' => 0,
                'new_quantity' => $product->stock,
                'reason' => 'Initial product creation',
            ]);

            // Handle Cloudinary Image Uploads
            if ($request->hasFile('images')) {
                $images = $request->file('images');
                foreach ($images as $index => $imageFile) {
                    $uploadRes = $this->cloudinaryService->upload($imageFile, 'bombay_fashion/products');

                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_url' => $uploadRes['secure_url'],
                        'public_id' => $uploadRes['public_id'],
                        'is_primary' => ($index === 0),
                        'display_order' => $index,
                    ]);
                }
            }

            // Handle School Uniform details if checked
            if ($request->boolean('is_uniform')) {
                SchoolUniform::create([
                    'product_id' => $product->id,
                    'school_name' => $validated['school_name'],
                    'class_grade' => $validated['class_grade'],
                    'gender' => $validated['gender'] ?? 'unisex',
                    'item_type' => $validated['item_type'] ?? 'Uniform Item',
                ]);
            }

            // Handle Variants if submitted
            if (!empty($validated['variants'])) {
                foreach ($validated['variants'] as $vData) {
                    if (!empty($vData['size']) || !empty($vData['color'])) {
                        $variantSku = !empty($vData['sku']) ? strtoupper($vData['sku']) : ($product->sku . '-' . Str::random(4));
                        $vStock = (int) ($vData['stock'] ?? 0);

                        $variant = ProductVariant::create([
                            'product_id' => $product->id,
                            'size' => $vData['size'] ?? null,
                            'color' => $vData['color'] ?? null,
                            'color_code' => $vData['color_code'] ?? null,
                            'sku' => $variantSku,
                            'additional_price' => $vData['additional_price'] ?? 0.00,
                            'stock' => $vStock,
                            'is_active' => true,
                        ]);

                        StockLog::create([
                            'product_id' => $product->id,
                            'product_variant_id' => $variant->id,
                            'user_id' => auth()->id(),
                            'type' => 'manual_adjustment',
                            'quantity_change' => $vStock,
                            'old_quantity' => 0,
                            'new_quantity' => $vStock,
                            'reason' => 'Initial variant creation',
                        ]);
                    }
                }
            }

            ActivityLog::log('product_created', Product::class, $product->id, [
                'name' => $product->name,
                'sku' => $product->sku,
            ]);

            return redirect()->route('admin.products.index')->with('success', 'Product created successfully!');
        });
    }

    public function edit(Product $product): View
    {
        $product->load(['images', 'allVariants', 'schoolUniform']);
        $categories = Category::orderBy('name')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:products,sku,' . $product->id,
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'season' => 'required|in:winter,summer,all_season',
            'fabric' => 'nullable|string|max:100',
            'stock' => 'required|integer|min:0',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'is_new_arrival' => 'nullable|boolean',
            'is_best_seller' => 'nullable|boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'new_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',

            // School Uniform
            'is_uniform' => 'nullable|boolean',
            'school_name' => 'required_if:is_uniform,1|nullable|string|max:255',
            'class_grade' => 'required_if:is_uniform,1|nullable|string|max:100',
            'gender' => 'required_if:is_uniform,1|nullable|in:boys,girls,unisex',
            'item_type' => 'required_if:is_uniform,1|nullable|string|max:100',
        ]);

        return DB::transaction(function () use ($request, $validated, $product) {
            $oldStock = $product->stock;
            $newStock = (int) $validated['stock'];

            $user = auth()->user();

            $product->update([
                'category_id' => $validated['category_id'],
                'name' => $validated['name'],
                'sku' => strtoupper($validated['sku']),
                'short_description' => $validated['short_description'] ?? null,
                'description' => $validated['description'] ?? null,
                'price' => $validated['price'],
                'discount_price' => $validated['discount_price'] ?? null,
                'season' => $validated['season'],
                'fabric' => $validated['fabric'] ?? null,
                'stock' => $newStock,
                'is_active' => $request->boolean('is_active', true),
                'is_featured' => $request->boolean('is_featured'),
                'is_new_arrival' => $request->boolean('is_new_arrival'),
                'is_best_seller' => $request->boolean('is_best_seller'),
                'updated_by_id' => $user?->id,
                'updater_name' => $user?->name,
                'meta_title' => $validated['meta_title'] ?? null,
                'meta_description' => $validated['meta_description'] ?? null,
            ]);

            // Log stock adjustment if changed
            if ($oldStock !== $newStock) {
                StockLog::create([
                    'product_id' => $product->id,
                    'product_variant_id' => null,
                    'user_id' => auth()->id(),
                    'type' => 'manual_adjustment',
                    'quantity_change' => $newStock - $oldStock,
                    'old_quantity' => $oldStock,
                    'new_quantity' => $newStock,
                    'reason' => 'Product updated by staff',
                ]);
            }

            // Upload additional images
            if ($request->hasFile('new_images')) {
                $currentMaxOrder = $product->images()->max('display_order') ?? 0;
                $hasPrimary = $product->images()->where('is_primary', true)->exists();

                foreach ($request->file('new_images') as $i => $file) {
                    $uploadRes = $this->cloudinaryService->upload($file, 'bombay_fashion/products');

                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_url' => $uploadRes['secure_url'],
                        'public_id' => $uploadRes['public_id'],
                        'is_primary' => (!$hasPrimary && $i === 0),
                        'display_order' => $currentMaxOrder + $i + 1,
                    ]);
                }
            }

            // Update School Uniform details
            if ($request->boolean('is_uniform')) {
                SchoolUniform::updateOrCreate(
                    ['product_id' => $product->id],
                    [
                        'school_name' => $validated['school_name'],
                        'class_grade' => $validated['class_grade'],
                        'gender' => $validated['gender'] ?? 'unisex',
                        'item_type' => $validated['item_type'] ?? 'Uniform Item',
                    ]
                );
            } else {
                $product->schoolUniform()?->delete();
            }

            ActivityLog::log('product_updated', Product::class, $product->id, [
                'name' => $product->name,
            ]);

            return redirect()->route('admin.products.index')->with('success', 'Product updated successfully!');
        });
    }

    public function destroy(Product $product): RedirectResponse
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Sirf Store Owner/Admin hi products delete kar sakte hain.');
        }

        $name = $product->name;
        $product->delete();

        ActivityLog::log('product_deleted', Product::class, $product->id, ['name' => $name]);

        return back()->with('info', "Product '{$name}' has been moved to trash.");
    }

    public function deleteImage(int $imageId): RedirectResponse
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Sirf Store Owner/Admin hi product images delete kar sakte hain.');
        }

        $image = ProductImage::findOrFail($imageId);
        $productId = $image->product_id;
        $image->delete(); // Automatically destroys from Cloudinary

        // If primary was deleted, promote another image
        $remaining = ProductImage::where('product_id', $productId)->first();
        if ($remaining && !ProductImage::where('product_id', $productId)->where('is_primary', true)->exists()) {
            $remaining->update(['is_primary' => true]);
        }

        return back()->with('success', 'Image removed from Cloudinary and product.');
    }

    public function setPrimaryImage(int $imageId): RedirectResponse
    {
        $image = ProductImage::findOrFail($imageId);
        ProductImage::where('product_id', $image->product_id)->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);

        return back()->with('success', 'Primary image updated.');
    }

    public function toggleActive(Product $product): RedirectResponse
    {
        $product->update(['is_active' => !$product->is_active]);
        $status = $product->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Product has been {$status}.");
    }

    public function toggleFeatured(Product $product): RedirectResponse
    {
        $product->update(['is_featured' => !$product->is_featured]);
        return back()->with('success', 'Featured status updated.');
    }
}

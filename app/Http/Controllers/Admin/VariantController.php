<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VariantController extends Controller
{
    public function store(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'size' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:50',
            'color_code' => 'nullable|string|max:10',
            'sku' => 'nullable|string|max:100|unique:product_variants,sku',
            'additional_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        $sku = !empty($validated['sku']) ? strtoupper($validated['sku']) : ($product->sku . '-' . Str::random(4));
        $stock = (int) $validated['stock'];

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'size' => $validated['size'] ?? null,
            'color' => $validated['color'] ?? null,
            'color_code' => $validated['color_code'] ?? null,
            'sku' => $sku,
            'additional_price' => $validated['additional_price'] ?? 0.00,
            'stock' => $stock,
            'is_active' => true,
        ]);

        StockLog::create([
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'user_id' => auth()->id(),
            'type' => 'manual_adjustment',
            'quantity_change' => $stock,
            'old_quantity' => 0,
            'new_quantity' => $stock,
            'reason' => 'Variant added by admin',
        ]);

        return back()->with('success', 'Product variant added successfully.');
    }

    public function update(Request $request, ProductVariant $variant): RedirectResponse
    {
        $validated = $request->validate([
            'size' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:50',
            'color_code' => 'nullable|string|max:10',
            'sku' => 'required|string|max:100|unique:product_variants,sku,' . $variant->id,
            'additional_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $oldStock = $variant->stock;
        $newStock = (int) $validated['stock'];

        $variant->update([
            'size' => $validated['size'] ?? null,
            'color' => $validated['color'] ?? null,
            'color_code' => $validated['color_code'] ?? null,
            'sku' => strtoupper($validated['sku']),
            'additional_price' => $validated['additional_price'] ?? 0.00,
            'stock' => $newStock,
            'is_active' => $request->boolean('is_active', true),
        ]);

        if ($oldStock !== $newStock) {
            StockLog::create([
                'product_id' => $variant->product_id,
                'product_variant_id' => $variant->id,
                'user_id' => auth()->id(),
                'type' => 'manual_adjustment',
                'quantity_change' => $newStock - $oldStock,
                'old_quantity' => $oldStock,
                'new_quantity' => $newStock,
                'reason' => 'Variant stock adjusted by staff',
            ]);
        }

        return back()->with('success', 'Variant updated.');
    }

    public function destroy(ProductVariant $variant): RedirectResponse
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Sirf Store Owner/Admin hi product variants delete kar sakte hain.');
        }

        $variant->delete();
        return back()->with('info', 'Variant removed.');
    }
}

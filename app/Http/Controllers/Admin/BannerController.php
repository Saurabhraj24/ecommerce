<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Banner;
use App\Services\CloudinaryImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BannerController extends Controller
{
    public function __construct(
        protected CloudinaryImageService $cloudinaryService
    ) {}

    public function index(): View
    {
        $banners = Banner::orderBy('position')
            ->orderBy('display_order')
            ->paginate(15);

        return view('admin.banners.index', compact('banners'));
    }

    public function create(): View
    {
        return view('admin.banners.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'link_url' => 'nullable|string|max:255',
            'button_text' => 'nullable|string|max:50',
            'position' => 'required|in:hero,middle,popup',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $uploadRes = $this->cloudinaryService->upload($request->file('image'), 'bombay_fashion/banners');

        $banner = Banner::create([
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? null,
            'link_url' => $validated['link_url'] ?? null,
            'button_text' => $validated['button_text'] ?? null,
            'image_url' => $uploadRes['secure_url'],
            'public_id' => $uploadRes['public_id'],
            'position' => $validated['position'],
            'display_order' => $validated['display_order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        ActivityLog::log('banner_created', Banner::class, $banner->id, ['title' => $banner->title]);

        return redirect()->route('admin.banners.index')->with('success', 'Banner uploaded to Cloudinary successfully!');
    }

    public function edit(Banner $banner): View
    {
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'link_url' => 'nullable|string|max:255',
            'button_text' => 'nullable|string|max:50',
            'position' => 'required|in:hero,middle,popup',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        if ($request->hasFile('image')) {
            if ($banner->public_id) {
                $this->cloudinaryService->delete($banner->public_id);
            }
            $uploadRes = $this->cloudinaryService->upload($request->file('image'), 'bombay_fashion/banners');
            $banner->image_url = $uploadRes['secure_url'];
            $banner->public_id = $uploadRes['public_id'];
        }

        $banner->update([
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? null,
            'link_url' => $validated['link_url'] ?? null,
            'button_text' => $validated['button_text'] ?? null,
            'position' => $validated['position'],
            'display_order' => $validated['display_order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        ActivityLog::log('banner_updated', Banner::class, $banner->id, ['title' => $banner->title]);

        return redirect()->route('admin.banners.index')->with('success', 'Banner updated successfully!');
    }

    public function destroy(Banner $banner): RedirectResponse
    {
        $title = $banner->title;
        $banner->delete(); // Automatically triggers Cloudinary cleanup

        ActivityLog::log('banner_deleted', Banner::class, null, ['title' => $title]);

        return back()->with('info', "Banner '{$title}' deleted.");
    }
}

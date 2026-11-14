<?php

namespace App\Services;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CloudinaryImageService
{
    /**
     * Upload an image file to Cloudinary (or local storage fallback if not configured).
     *
     * @param UploadedFile $file
     * @param string $folder
     * @return array{secure_url: string, public_id: string}
     */
    public function upload(UploadedFile $file, string $folder = 'bombay_fashion/products'): array
    {
        $hasCloudinaryConfig = !empty(config('cloudinary.cloud_url')) || (!empty(env('CLOUDINARY_URL')) || !empty(env('CLOUDINARY_API_KEY')));

        if ($hasCloudinaryConfig) {
            try {
                $uploaded = Cloudinary::upload($file->getRealPath(), [
                    'folder' => $folder,
                    'transformation' => [
                        'quality' => 'auto',
                        'fetch_format' => 'auto',
                    ],
                ]);

                return [
                    'secure_url' => $uploaded->getSecurePath(),
                    'public_id' => $uploaded->getPublicId(),
                ];
            } catch (\Throwable $e) {
                Log::error('Cloudinary upload error, falling back to public disk: ' . $e->getMessage());
            }
        }

        // Local public storage fallback
        $path = $file->store($folder, 'public');
        $publicId = 'local_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '_' . uniqid();

        return [
            'secure_url' => asset('storage/' . $path),
            'public_id' => $publicId,
        ];
    }

    /**
     * Delete an asset from Cloudinary or local storage.
     *
     * @param string $publicId
     * @return bool
     */
    public function delete(string $publicId): bool
    {
        if (empty($publicId)) {
            return false;
        }

        if (str_starts_with($publicId, 'local_')) {
            return true;
        }

        try {
            Cloudinary::destroy($publicId);
            return true;
        } catch (\Throwable $e) {
            Log::warning("Failed to delete Cloudinary asset {$publicId}: " . $e->getMessage());
            return false;
        }
    }
}

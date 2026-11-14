<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            [
                'title' => 'Handcrafted Bridal & Banarasi Silk Sarees',
                'subtitle' => 'Timeless elegance crafted for royal Indian weddings and grand celebrations. Explore pure silk weaves and bridal trousseau.',
                'button_text' => 'Explore Bridal Sarees',
                'link_url' => '/products?category=sarees',
                'image_url' => '/images/banners/banner1.jpg',
                'public_id' => 'local_banner_1',
                'position' => 'hero',
                'display_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Festive Punjabi Suits & Designer Anarkalis',
                'subtitle' => 'Traditional Amritsari phulkari dupattas, intricate zari embroidery, and regal festive wear for all celebrations.',
                'button_text' => 'Shop Festive Suits',
                'link_url' => '/products?category=women',
                'image_url' => '/images/banners/banner2.jpg',
                'public_id' => 'local_banner_2',
                'position' => 'hero',
                'display_order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Certified School Uniforms for Amritsar Schools',
                'subtitle' => 'Official tailored uniforms for top schools across Amritsar. Premium fabrics, blazers, shirts, skirts, trousers, and accessories.',
                'button_text' => 'Find School Uniform',
                'link_url' => '/uniforms',
                'image_url' => '/images/banners/banner3.jpg',
                'public_id' => 'local_banner_3',
                'position' => 'hero',
                'display_order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($banners as $bannerData) {
            Banner::updateOrCreate(
                ['title' => $bannerData['title']],
                $bannerData
            );
        }
    }
}

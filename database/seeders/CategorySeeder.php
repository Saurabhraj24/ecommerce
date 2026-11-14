<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Men',
                'description' => 'Men\'s ethnic wear, shirts, trousers, kurtas, jackets and daily wear collection.',
                'display_order' => 1,
                'children' => [
                    ['name' => 'Kurtas & Kurta Sets', 'description' => 'Festive and casual kurtas for men.'],
                    ['name' => 'Formal & Casual Shirts', 'description' => 'Smart shirts for all occasions.'],
                    ['name' => 'Trousers & Jeans', 'description' => 'Comfortable bottoms and denim.'],
                    ['name' => 'Jackets & Blazers', 'description' => 'Nehru jackets, blazers, and winter coats.'],
                ],
            ],
            [
                'name' => 'Women',
                'description' => 'Women\'s Indian suits, lehengas, kurtis, western wear, and bridal collections.',
                'display_order' => 2,
                'children' => [
                    ['name' => 'Salwar Suits & Anarkalis', 'description' => 'Punjabi suits, unstitched dress materials, and designer readymades.'],
                    ['name' => 'Kurtis & Tunics', 'description' => 'Daily and festive kurtis.'],
                    ['name' => 'Lehengas & Bridal Wear', 'description' => 'Wedding, party, and festive lehengas.'],
                    ['name' => 'Dupattas & Shawls', 'description' => 'Phulkari, banarasi, and pashmina shawls.'],
                ],
            ],
            [
                'name' => 'Sarees',
                'description' => 'Handpicked Sarees: Silk, Cotton, Designer, and Bridal collections.',
                'display_order' => 3,
                'children' => [
                    ['name' => 'Silk Sarees', 'description' => 'Pure silk, Banarasi silk, Kanjivaram, and Tussar.'],
                    ['name' => 'Cotton Sarees', 'description' => 'Breathable pure cotton, chanderi, and linen sarees.'],
                    ['name' => 'Designer Sarees', 'description' => 'Contemporary organza, georgette, and sequin work sarees.'],
                    ['name' => 'Bridal Sarees', 'description' => 'Heavy embroidered wedding and trousseau sarees.'],
                ],
            ],
            [
                'name' => 'Children',
                'description' => 'Festive and daily wear clothing for boys, girls, and infants.',
                'display_order' => 4,
                'children' => [
                    ['name' => 'Boys Wear', 'description' => 'Kurta pyjamas, suits, t-shirts, and pants for boys.'],
                    ['name' => 'Girls Wear', 'description' => 'Frocks, lehengas, suits, and dresses for girls.'],
                    ['name' => 'Infants (0-2 Years)', 'description' => 'Soft cotton sets, rompers, and baby essentials.'],
                ],
            ],
            [
                'name' => 'School Uniforms',
                'description' => 'Official school uniforms for Amritsar schools by school name, grade, and gender.',
                'display_order' => 5,
                'children' => [
                    ['name' => 'Shirts & Tops', 'description' => 'White and school-colored shirts.'],
                    ['name' => 'Trousers, Shorts & Skirts', 'description' => 'Pants, half-pants, and pleated skirts.'],
                    ['name' => 'Blazers & Sweaters', 'description' => 'Winter school blazers, cardigans, and pullovers.'],
                    ['name' => 'Tracksuits & House T-Shirts', 'description' => 'Sports uniforms and activity wear.'],
                    ['name' => 'Ties, Belts & Socks', 'description' => 'School accessories and essentials.'],
                ],
            ],
            [
                'name' => 'Winter Wear',
                'description' => 'Cozy shawls, cardigans, jackets, thermal wear, and woolens for the Punjab winter.',
                'display_order' => 6,
                'children' => [
                    ['name' => 'Men\'s Woolens', 'description' => 'Jackets, sweaters, and thermals for men.'],
                    ['name' => 'Women\'s Cardigans & Shawls', 'description' => 'Fine wool cardigans, stoles, and lohis.'],
                    ['name' => 'Kids Winter Wear', 'description' => 'Warm hoodies, thermals, and caps for kids.'],
                ],
            ],
            [
                'name' => 'Summer Wear',
                'description' => 'Light cottons, linens, and breezy casual wear designed for hot Indian summers.',
                'display_order' => 7,
                'children' => [
                    ['name' => 'Pure Cotton Collection', 'description' => 'Breathable 100% cotton garments.'],
                    ['name' => 'Linen & Blend Wear', 'description' => 'Comfortable airy fabrics for high humidity.'],
                ],
            ],
        ];

        foreach ($categories as $catData) {
            $children = $catData['children'] ?? [];
            unset($catData['children']);

            $catData['slug'] = Str::slug($catData['name']);
            $catData['is_active'] = true;

            $parent = Category::firstOrCreate(['slug' => $catData['slug']], $catData);

            foreach ($children as $childIndex => $childData) {
                $childData['parent_id'] = $parent->id;
                $childData['slug'] = Str::slug($parent->name . '-' . $childData['name']);
                $childData['display_order'] = $childIndex + 1;
                $childData['is_active'] = true;

                Category::firstOrCreate(['slug' => $childData['slug']], $childData);
            }
        }
    }
}

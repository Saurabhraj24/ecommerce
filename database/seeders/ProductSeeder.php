<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\SchoolUniform;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // 3 products for each subcategory
        $itemsBySubcategory = [
            // ==================== MEN ====================
            'men-kurtas-kurta-sets' => [
                [
                    'name' => 'Royal Lucknowi Chikankari Silk Kurta Set',
                    'price' => 2999.00,
                    'discount_price' => 2299.00,
                    'season' => 'all_season',
                    'fabric' => 'Silk Blend',
                    'short_description' => 'Exquisite hand-finished Chikankari embroidery with matching churidar pyjama in ivory gold.',
                    'colors' => [['Ivory', '#FFFFF0'], ['Gold', '#D4AF37']],
                    'sizes' => ['38', '40', '42', '44'],
                    'images' => [
                        'https://images.unsplash.com/photo-1597983073493-88cd35cf93b0?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1617127365659-c47fa864d8bc?auto=format&fit=crop&w=800&q=80'
                    ],
                    'featured' => true,
                    'new_arrival' => true,
                ],
                [
                    'name' => 'Maroon Embroidered Wedding Sherwani Kurta',
                    'price' => 4500.00,
                    'discount_price' => 3799.00,
                    'season' => 'winter',
                    'fabric' => 'Raw Silk',
                    'short_description' => 'Festive deep maroon festive kurta with intricate mandarin collar thread embroidery.',
                    'colors' => [['Maroon', '#800000']],
                    'sizes' => ['40', '42', '44'],
                    'images' => [
                        'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1597983073493-88cd35cf93b0?auto=format&fit=crop&w=800&q=80'
                    ],
                    'best_seller' => true,
                ],
                [
                    'name' => 'Summer Breathable Pure Linen Short Kurta',
                    'price' => 1499.00,
                    'discount_price' => 1199.00,
                    'season' => 'summer',
                    'fabric' => '100% Pure Linen',
                    'short_description' => 'Casual pastel peach short kurta for casual outings and semi-formal summer gatherings.',
                    'colors' => [['Peach', '#FFE5B4'], ['White', '#FFFFFF']],
                    'sizes' => ['M', 'L', 'XL'],
                    'images' => [
                        'https://images.unsplash.com/photo-1602810318383-e386cc2a3ccf?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1617127365659-c47fa864d8bc?auto=format&fit=crop&w=800&q=80'
                    ],
                ],
            ],

            'men-formal-casual-shirts' => [
                [
                    'name' => 'Classic White Royal Oxford Formal Shirt',
                    'price' => 1799.00,
                    'discount_price' => 1399.00,
                    'season' => 'all_season',
                    'fabric' => 'Egyptian Cotton',
                    'short_description' => 'Crisp tailored luxury cotton shirt with spread collar and wrinkle-resistant finish.',
                    'colors' => [['White', '#FFFFFF']],
                    'sizes' => ['39', '40', '42', '44'],
                    'images' => [
                        'https://images.unsplash.com/photo-1620012253295-c15c429f6dd5?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1602810318383-e386cc2a3ccf?auto=format&fit=crop&w=800&q=80'
                    ],
                    'best_seller' => true,
                ],
                [
                    'name' => 'Sky Blue Micro-Check Smart Casual Shirt',
                    'price' => 1599.00,
                    'discount_price' => null,
                    'season' => 'summer',
                    'fabric' => '100% Breathable Cotton',
                    'short_description' => 'Lightweight everyday casual shirt crafted for warm afternoons and office wear.',
                    'colors' => [['Sky Blue', '#87CEEB']],
                    'sizes' => ['M', 'L', 'XL'],
                    'images' => [
                        'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1620012253295-c15c429f6dd5?auto=format&fit=crop&w=800&q=80'
                    ],
                ],
                [
                    'name' => 'Midnight Navy Mandarin Collar Linen Shirt',
                    'price' => 1999.00,
                    'discount_price' => 1599.00,
                    'season' => 'all_season',
                    'fabric' => 'Pure Linen Blend',
                    'short_description' => 'Contemporary bandhgala style collar shirt ideal for evening dinners and gatherings.',
                    'colors' => [['Navy Blue', '#000080']],
                    'sizes' => ['M', 'L', 'XL', 'XXL'],
                    'images' => [
                        'https://images.unsplash.com/photo-1603252109303-2751441dd157?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?auto=format&fit=crop&w=800&q=80'
                    ],
                    'featured' => true,
                ],
            ],

            'men-trousers-jeans' => [
                [
                    'name' => 'Tailored Italian Fit Chino Trousers',
                    'price' => 2199.00,
                    'discount_price' => 1699.00,
                    'season' => 'all_season',
                    'fabric' => 'Cotton Elastane Stretch',
                    'short_description' => 'Premium 4-way stretch smart chino trousers with interior gripper waistband.',
                    'colors' => [['Beige', '#F5F5DC'], ['Khaki', '#C3B091']],
                    'sizes' => ['30', '32', '34', '36'],
                    'images' => [
                        'https://images.unsplash.com/photo-1473966968600-fa801b869a1a?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?auto=format&fit=crop&w=800&q=80'
                    ],
                ],
                [
                    'name' => 'Vintage Indigo Comfort Stretch Denim Jeans',
                    'price' => 2499.00,
                    'discount_price' => 1999.00,
                    'season' => 'all_season',
                    'fabric' => 'Heavy Denim Cotton',
                    'short_description' => 'Classic 5-pocket denim jeans featuring authentic washed fading and reinforced stitching.',
                    'colors' => [['Indigo Blue', '#3F51B5']],
                    'sizes' => ['32', '34', '36', '38'],
                    'images' => [
                        'https://images.unsplash.com/photo-1542272604-780c96856592?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1473966968600-fa801b869a1a?auto=format&fit=crop&w=800&q=80'
                    ],
                    'best_seller' => true,
                ],
                [
                    'name' => 'Formal Charcoal Pleated Gurkha Trousers',
                    'price' => 2299.00,
                    'discount_price' => null,
                    'season' => 'winter',
                    'fabric' => 'Poly-Viscose Wool Blend',
                    'short_description' => 'Distinguished high-waisted formal trousers with double forward pleats.',
                    'colors' => [['Charcoal Grey', '#36454F']],
                    'sizes' => ['30', '32', '34', '36'],
                    'images' => [
                        'https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1542272604-780c96856592?auto=format&fit=crop&w=800&q=80'
                    ],
                ],
            ],

            'men-jackets-blazers' => [
                [
                    'name' => 'Handcrafted Banarasi Brocade Nehru Jacket',
                    'price' => 3800.00,
                    'discount_price' => 2999.00,
                    'season' => 'winter',
                    'fabric' => 'Pure Silk Brocade',
                    'short_description' => 'Grand gold woven bandi jacket with brass filigree buttons for weddings and festive functions.',
                    'colors' => [['Royal Gold', '#FFD700']],
                    'sizes' => ['38', '40', '42', '44'],
                    'images' => [
                        'https://images.unsplash.com/photo-1507679799987-c73779587ccf?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1594938298603-c8148c4dae35?auto=format&fit=crop&w=800&q=80'
                    ],
                    'featured' => true,
                ],
                [
                    'name' => 'Velvet Embroidered Jodhpuri Bandhgala Blazer',
                    'price' => 6999.00,
                    'discount_price' => 5499.00,
                    'season' => 'winter',
                    'fabric' => 'Italian Micro Velvet',
                    'short_description' => 'Regal evening blazer in midnight sapphire with antique silver zardozi neckwork.',
                    'colors' => [['Sapphire Blue', '#0F52BA']],
                    'sizes' => ['40', '42', '44'],
                    'images' => [
                        'https://images.unsplash.com/photo-1594938298603-c8148c4dae35?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1507679799987-c73779587ccf?auto=format&fit=crop&w=800&q=80'
                    ],
                ],
                [
                    'name' => 'Structured Summer Linen Blazer in Oatmeal',
                    'price' => 3499.00,
                    'discount_price' => 2799.00,
                    'season' => 'summer',
                    'fabric' => '100% Pure Linen',
                    'short_description' => 'Light, unlined soft tailored jacket for breezy summer ceremonies and daytime parties.',
                    'colors' => [['Oatmeal Beige', '#E0D8B0']],
                    'sizes' => ['38', '40', '42'],
                    'images' => [
                        'https://images.unsplash.com/photo-1593030761757-71fae45fa0e7?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1507679799987-c73779587ccf?auto=format&fit=crop&w=800&q=80'
                    ],
                ],
            ],

            // ==================== WOMEN ====================
            'women-salwar-suits-anarkalis' => [
                [
                    'name' => 'Traditional Amritsari Phulkari Handcrafted Salwar Suit',
                    'price' => 3999.00,
                    'discount_price' => 3199.00,
                    'season' => 'all_season',
                    'fabric' => 'Pure Georgette & Silk',
                    'short_description' => 'Authentic Punjab heritage phulkari silk thread embroidery with matching patiala salwar.',
                    'colors' => [['Hot Pink', '#FF69B4'], ['Mustard Yellow', '#FFDB58']],
                    'sizes' => ['M', 'L', 'XL', 'XXL'],
                    'images' => [
                        'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?auto=format&fit=crop&w=800&q=80'
                    ],
                    'featured' => true,
                    'best_seller' => true,
                ],
                [
                    'name' => 'Royal Emerald Floor-Length Anarkali Gown Suit',
                    'price' => 5499.00,
                    'discount_price' => 4499.00,
                    'season' => 'all_season',
                    'fabric' => 'Chanderi Silk',
                    'short_description' => 'Flowing 56-kali anarkali featuring hand-embellished gota patti border work.',
                    'colors' => [['Emerald Green', '#50C878']],
                    'sizes' => ['S', 'M', 'L', 'XL'],
                    'images' => [
                        'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=800&q=80'
                    ],
                ],
                [
                    'name' => 'Daily Wear Pure Cotton Printed Punjabi Patiala Suit',
                    'price' => 1899.00,
                    'discount_price' => 1499.00,
                    'season' => 'summer',
                    'fabric' => '100% Cambric Cotton',
                    'short_description' => 'Cool, lightweight everyday breathable suit material with soft mulmul dupatta.',
                    'colors' => [['Pastel Peach', '#FFE5B4']],
                    'sizes' => ['M', 'L', 'XL'],
                    'images' => [
                        'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=800&q=80'
                    ],
                ],
            ],

            'women-kurtis-tunics' => [
                [
                    'name' => 'Handblock Bagru Print A-Line Cotton Kurti',
                    'price' => 1299.00,
                    'discount_price' => 999.00,
                    'season' => 'summer',
                    'fabric' => '100% Pure Cotton',
                    'short_description' => 'Artisanal vegetable-dye block printed cotton kurti with three-quarter sleeves and side pocket.',
                    'colors' => [['Indigo', '#4B0082'], ['Rust', '#B7410E']],
                    'sizes' => ['S', 'M', 'L', 'XL'],
                    'images' => [
                        'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=800&q=80'
                    ],
                    'new_arrival' => true,
                ],
                [
                    'name' => 'Mirror Work Rayon Festive Straight Kurti',
                    'price' => 1599.00,
                    'discount_price' => 1249.00,
                    'season' => 'all_season',
                    'fabric' => 'Slub Rayon',
                    'short_description' => 'Comfortable drape tunic embellished with authentic Rajasthani mirror accents.',
                    'colors' => [['Ruby Red', '#E0115F']],
                    'sizes' => ['M', 'L', 'XL', 'XXL'],
                    'images' => [
                        'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?auto=format&fit=crop&w=800&q=80'
                    ],
                ],
                [
                    'name' => 'Embroidered Georgette Front-Slit Long Kurti',
                    'price' => 1999.00,
                    'discount_price' => 1599.00,
                    'season' => 'all_season',
                    'fabric' => 'Faux Georgette',
                    'short_description' => 'Designer front-slit kurti paired with inner slip, adorned with gold sequin florals.',
                    'colors' => [['Teal Blue', '#008080']],
                    'sizes' => ['M', 'L', 'XL'],
                    'images' => [
                        'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=800&q=80'
                    ],
                ],
            ],

            'women-lehengas-bridal-wear' => [
                [
                    'name' => 'Royal Crimson Zardozi Handcrafted Bridal Lehenga',
                    'price' => 24999.00,
                    'discount_price' => 19999.00,
                    'season' => 'all_season',
                    'fabric' => 'Raw Silk & Velvet',
                    'short_description' => 'Magnificent wedding lehenga adorned with authentic dabka, zardozi, and micro-pearl embroidery.',
                    'colors' => [['Royal Crimson', '#990000']],
                    'sizes' => ['Free Size (Semi-Stitched)'],
                    'images' => [
                        'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?auto=format&fit=crop&w=800&q=80'
                    ],
                    'featured' => true,
                ],
                [
                    'name' => 'Pastel Mint Floral Organza Reception Lehenga',
                    'price' => 14999.00,
                    'discount_price' => 11999.00,
                    'season' => 'summer',
                    'fabric' => 'Pure Organza & Silk',
                    'short_description' => 'Dreamy modern reception lehenga featuring lightweight organza flares and mirror-worked blouse.',
                    'colors' => [['Mint Green', '#98FF98']],
                    'sizes' => ['Free Size (Semi-Stitched)'],
                    'images' => [
                        'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=800&q=80'
                    ],
                ],
                [
                    'name' => 'Sequined Velvet Sangeet Lehenga Choli',
                    'price' => 16500.00,
                    'discount_price' => 13499.00,
                    'season' => 'winter',
                    'fabric' => 'Micro Velvet',
                    'short_description' => 'Heavy sequined flared skirt with padded blouse and sheer net dupatta for winter parties.',
                    'colors' => [['Plum Wine', '#581845']],
                    'sizes' => ['Free Size (Semi-Stitched)'],
                    'images' => [
                        'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?auto=format&fit=crop&w=800&q=80'
                    ],
                ],
            ],

            'women-dupattas-shawls' => [
                [
                    'name' => 'Authentic Amritsari Bagh Phulkari Heavy Dupatta',
                    'price' => 2499.00,
                    'discount_price' => 1999.00,
                    'season' => 'all_season',
                    'fabric' => 'Pure Chanderi Silk',
                    'short_description' => 'Signature full-coverage Punjab Bagh phulkari handwork with scallop zari borders.',
                    'colors' => [['Multi Red', '#D72638']],
                    'sizes' => ['2.5 Meters'],
                    'images' => [
                        'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?auto=format&fit=crop&w=800&q=80'
                    ],
                    'best_seller' => true,
                ],
                [
                    'name' => 'Kashmiri Hand Embroidered Soft Pashmina Shawl',
                    'price' => 3800.00,
                    'discount_price' => 2999.00,
                    'season' => 'winter',
                    'fabric' => 'Fine Wool & Cashmere',
                    'short_description' => 'Ultra soft warming shawl featuring traditional Kashmiri Sozni needlepoint border craft.',
                    'colors' => [['Walnut Beige', '#D1B894']],
                    'sizes' => ['Standard Shawl (1x2m)'],
                    'images' => [
                        'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=800&q=80'
                    ],
                ],
                [
                    'name' => 'Pure Banarasi Katan Silk Zari Brocade Dupatta',
                    'price' => 2199.00,
                    'discount_price' => null,
                    'season' => 'all_season',
                    'fabric' => 'Banarasi Katan Silk',
                    'short_description' => 'Woven floral jaal pattern in antique gold zari across rich royal magenta background.',
                    'colors' => [['Festive Magenta', '#C2185B']],
                    'sizes' => ['2.4 Meters'],
                    'images' => [
                        'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?auto=format&fit=crop&w=800&q=80'
                    ],
                ],
            ],

            // ==================== SAREES ====================
            'sarees-silk-sarees' => [
                [
                    'name' => 'Pure Banarasi Katan Silk Saree with Gold Zari',
                    'price' => 8999.00,
                    'discount_price' => 7499.00,
                    'season' => 'all_season',
                    'fabric' => '100% Pure Katan Silk',
                    'short_description' => 'Heirloom weave with rich kadwa floral motifs, grand pallu, and matching unstitched blouse.',
                    'colors' => [['Royal Maroon', '#800000'], ['Gold', '#D4AF37']],
                    'sizes' => ['6.3 Meters with Blouse'],
                    'images' => [
                        'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?auto=format&fit=crop&w=800&q=80'
                    ],
                    'featured' => true,
                    'best_seller' => true,
                ],
                [
                    'name' => 'Kanjivaram Rich Woven Temple Border Silk Saree',
                    'price' => 11500.00,
                    'discount_price' => 9899.00,
                    'season' => 'all_season',
                    'fabric' => 'Mulberry Silk',
                    'short_description' => 'South Indian bridal heritage saree in peacock teal with contrast crimson zari border.',
                    'colors' => [['Peacock Teal', '#004953']],
                    'sizes' => ['6.3 Meters with Blouse'],
                    'images' => [
                        'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=800&q=80'
                    ],
                ],
                [
                    'name' => 'Handpainted Madhubani Tussar Silk Saree',
                    'price' => 6499.00,
                    'discount_price' => 5299.00,
                    'season' => 'all_season',
                    'fabric' => 'Desi Tussar Silk',
                    'short_description' => 'Artisanal hand-drawn traditional folk motifs on natural textured golden tussar drape.',
                    'colors' => [['Mustard Ochre', '#CC7722']],
                    'sizes' => ['6.3 Meters with Blouse'],
                    'images' => [
                        'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?auto=format&fit=crop&w=800&q=80'
                    ],
                ],
            ],

            'sarees-cotton-sarees' => [
                [
                    'name' => 'Handloom Chanderi Cotton Silk Zari Border Saree',
                    'price' => 2499.00,
                    'discount_price' => 1899.00,
                    'season' => 'summer',
                    'fabric' => 'Chanderi Cotton Silk',
                    'short_description' => 'Featherlight summer luxury saree with subtle gold zari booti work and sheer elegance.',
                    'colors' => [['Pastel Pink', '#FFD1DC'], ['Pista Green', '#93C572']],
                    'sizes' => ['6.3 Meters with Blouse'],
                    'images' => [
                        'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=800&q=80'
                    ],
                    'new_arrival' => true,
                ],
                [
                    'name' => 'Breathable Mulmul Pure Cotton Daily Saree',
                    'price' => 1499.00,
                    'discount_price' => 1199.00,
                    'season' => 'summer',
                    'fabric' => '100% Mulmul Cotton',
                    'short_description' => 'Soft, skin-friendly handblock floral printed saree crafted for daily wear and teachers.',
                    'colors' => [['Indigo Blue', '#00416A']],
                    'sizes' => ['5.5 Meters'],
                    'images' => [
                        'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?auto=format&fit=crop&w=800&q=80'
                    ],
                ],
                [
                    'name' => 'Bengal Handloom Tant Pure Cotton Saree',
                    'price' => 1799.00,
                    'discount_price' => null,
                    'season' => 'summer',
                    'fabric' => '100% Fine Cotton',
                    'short_description' => 'Crisp traditional woven tant saree with broad temple border and decorative pallu.',
                    'colors' => [['Off White & Red', '#FAF0E6']],
                    'sizes' => ['5.5 Meters'],
                    'images' => [
                        'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=800&q=80'
                    ],
                ],
            ],

            'sarees-designer-sarees' => [
                [
                    'name' => 'Floral Embroidered Sheer Organza Designer Saree',
                    'price' => 4500.00,
                    'discount_price' => 3699.00,
                    'season' => 'summer',
                    'fabric' => 'Pure Organza Silk',
                    'short_description' => 'Delicate pastel lilac organza drape with hand-embroidered cutwork scalloped edges.',
                    'colors' => [['Lilac', '#C8A2C8']],
                    'sizes' => ['6.3 Meters with Blouse'],
                    'images' => [
                        'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?auto=format&fit=crop&w=800&q=80'
                    ],
                    'featured' => true,
                ],
                [
                    'name' => 'Midnight Sequin Work Georgette Cocktail Saree',
                    'price' => 3999.00,
                    'discount_price' => 3199.00,
                    'season' => 'all_season',
                    'fabric' => 'Heavy Faux Georgette',
                    'short_description' => 'Shimmering tone-on-tone micro sequins embroidered saree for cocktail parties and sangeet.',
                    'colors' => [['Midnight Black', '#1B1B1B']],
                    'sizes' => ['6.3 Meters with Blouse'],
                    'images' => [
                        'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=800&q=80'
                    ],
                ],
                [
                    'name' => 'Ready-to-Wear Pleated Lycra Saree with Belt',
                    'price' => 2999.00,
                    'discount_price' => 2499.00,
                    'season' => 'all_season',
                    'fabric' => 'Lycra Shimmer',
                    'short_description' => 'One-minute pre-stitched party saree with matching designer embroidered waist belt.',
                    'colors' => [['Emerald Green', '#046307']],
                    'sizes' => ['Free Size Ready to Wear'],
                    'images' => [
                        'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?auto=format&fit=crop&w=800&q=80'
                    ],
                ],
            ],

            'sarees-bridal-sarees' => [
                [
                    'name' => 'Heritage Scarlet Red Zardozi Bridal Silk Saree',
                    'price' => 15999.00,
                    'discount_price' => 12999.00,
                    'season' => 'all_season',
                    'fabric' => 'Pure Katan Silk & Velvet',
                    'short_description' => 'Regal bridal wedding saree decorated with gold bullion wire zardozi border and heavy pallu.',
                    'colors' => [['Scarlet Red', '#FF2400']],
                    'sizes' => ['6.5 Meters with Heavy Blouse Piece'],
                    'images' => [
                        'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?auto=format&fit=crop&w=800&q=80'
                    ],
                    'featured' => true,
                ],
                [
                    'name' => 'Golden Brocade Trousseau Silk Saree',
                    'price' => 12500.00,
                    'discount_price' => 10499.00,
                    'season' => 'all_season',
                    'fabric' => 'Tissue Silk Brocade',
                    'short_description' => 'Radiant all-over metallic gold brocade weave crafted for bride’s trousseau collection.',
                    'colors' => [['Warm Gold', '#E5A93C']],
                    'sizes' => ['6.3 Meters with Blouse'],
                    'images' => [
                        'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=800&q=80'
                    ],
                ],
                [
                    'name' => 'Royal Fuchsia Banarasi Meenakari Wedding Saree',
                    'price' => 13999.00,
                    'discount_price' => 11499.00,
                    'season' => 'all_season',
                    'fabric' => 'Pure Silk',
                    'short_description' => 'Multi-colored enamel thread meenakari detailing woven into rich festive fuchsia katan silk.',
                    'colors' => [['Fuchsia Pink', '#FF00FF']],
                    'sizes' => ['6.3 Meters with Blouse'],
                    'images' => [
                        'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?auto=format&fit=crop&w=800&q=80'
                    ],
                ],
            ],

            // ==================== CHILDREN ====================
            'children-boys-wear' => [
                [
                    'name' => 'Boys Festive Jacquard Kurta Pyjama with Waistcoat',
                    'price' => 1899.00,
                    'discount_price' => 1499.00,
                    'season' => 'all_season',
                    'fabric' => 'Silk Jacquard',
                    'short_description' => '3-piece ethnic celebration set: printed jacket, silk kurta, and elasticated churidar.',
                    'colors' => [['Royal Blue', '#4169E1']],
                    'sizes' => ['3-4 Yrs', '5-6 Yrs', '7-8 Yrs', '9-10 Yrs'],
                    'images' => [
                        'https://images.unsplash.com/photo-1519238263530-99bdd11df2ea?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?auto=format&fit=crop&w=800&q=80'
                    ],
                    'featured' => true,
                ],
                [
                    'name' => 'Boys Smart Cotton Party Shirt & Chino Set',
                    'price' => 1499.00,
                    'discount_price' => 1199.00,
                    'season' => 'summer',
                    'fabric' => '100% Pure Cotton',
                    'short_description' => 'Dapper collar button-up printed shirt with comfortable stretch cotton shorts/chinos.',
                    'colors' => [['White & Tan', '#EEDC82']],
                    'sizes' => ['4-5 Yrs', '6-7 Yrs', '8-9 Yrs'],
                    'images' => [
                        'https://images.unsplash.com/photo-1503919545889-aef636e10ad4?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1519238263530-99bdd11df2ea?auto=format&fit=crop&w=800&q=80'
                    ],
                ],
                [
                    'name' => 'Boys Traditional Dhoti Kurta Set in Yellow',
                    'price' => 1299.00,
                    'discount_price' => 999.00,
                    'season' => 'all_season',
                    'fabric' => 'Cotton Silk',
                    'short_description' => 'Festive yellow kurta with readymade stitched pleated dhoti for puja and cultural school events.',
                    'colors' => [['Haldi Yellow', '#FFAE42']],
                    'sizes' => ['2-3 Yrs', '4-5 Yrs', '6-7 Yrs'],
                    'images' => [
                        'https://images.unsplash.com/photo-1519238263530-99bdd11df2ea?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1503919545889-aef636e10ad4?auto=format&fit=crop&w=800&q=80'
                    ],
                ],
            ],

            'children-girls-wear' => [
                [
                    'name' => 'Girls Festive Mirror Work Floral Lehenga Choli',
                    'price' => 2299.00,
                    'discount_price' => 1799.00,
                    'season' => 'all_season',
                    'fabric' => 'Georgette & Net',
                    'short_description' => 'Colorful twirl-friendly layered lehenga with foil mirror accents and soft lining.',
                    'colors' => [['Coral Pink', '#F88379']],
                    'sizes' => ['3-4 Yrs', '5-6 Yrs', '7-8 Yrs', '9-10 Yrs'],
                    'images' => [
                        'https://images.unsplash.com/photo-1518831959646-742c3a14ebf7?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=800&q=80'
                    ],
                    'featured' => true,
                ],
                [
                    'name' => 'Girls Embroidered Net Birthday Party Frock',
                    'price' => 1699.00,
                    'discount_price' => 1349.00,
                    'season' => 'all_season',
                    'fabric' => 'Soft Tulle & Satin',
                    'short_description' => 'Princess gown frock with delicate lace bodice and flared volume skirt for birthdays.',
                    'colors' => [['Lavender', '#E6E6FA']],
                    'sizes' => ['2-3 Yrs', '4-5 Yrs', '6-7 Yrs'],
                    'images' => [
                        'https://images.unsplash.com/photo-1518831959646-742c3a14ebf7?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1503919545889-aef636e10ad4?auto=format&fit=crop&w=800&q=80'
                    ],
                ],
                [
                    'name' => 'Girls Traditional Punjabi Patiala Suit in Red',
                    'price' => 1499.00,
                    'discount_price' => 1199.00,
                    'season' => 'all_season',
                    'fabric' => 'Cotton Silk',
                    'short_description' => 'Bright red festive suit with gota lace borders and matching miniature net dupatta.',
                    'colors' => [['Festive Red', '#FF0000']],
                    'sizes' => ['3-4 Yrs', '5-6 Yrs', '7-8 Yrs'],
                    'images' => [
                        'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1518831959646-742c3a14ebf7?auto=format&fit=crop&w=800&q=80'
                    ],
                ],
            ],

            'children-infants-0-2-years' => [
                [
                    'name' => 'Baby Boy Organic Cotton Kurta Dhoti Set',
                    'price' => 999.00,
                    'discount_price' => 799.00,
                    'season' => 'all_season',
                    'fabric' => '100% Organic Cotton',
                    'short_description' => 'Ultra gentle hypoallergenic baby ethnic outfit with easy snap-button shoulder closure.',
                    'colors' => [['Mint & Cream', '#F5FFFA']],
                    'sizes' => ['0-6 Months', '6-12 Months', '12-18 Months'],
                    'images' => [
                        'https://images.unsplash.com/photo-1519689680058-324335c77eba?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1519238263530-99bdd11df2ea?auto=format&fit=crop&w=800&q=80'
                    ],
                    'best_seller' => true,
                ],
                [
                    'name' => 'Infant Soft Cotton Rompers (Pack of 3)',
                    'price' => 1199.00,
                    'discount_price' => 899.00,
                    'season' => 'summer',
                    'fabric' => '100% Breathable Cotton',
                    'short_description' => '3 pastel printed daily onesies with nickel-free crotch snap buttons for diaper changes.',
                    'colors' => [['Pastel Mix', '#E0FFFF']],
                    'sizes' => ['0-3 M', '3-6 M', '6-12 M'],
                    'images' => [
                        'https://images.unsplash.com/photo-1519689680058-324335c77eba?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1518831959646-742c3a14ebf7?auto=format&fit=crop&w=800&q=80'
                    ],
                ],
                [
                    'name' => 'Warm Knitted Infant Hooded Sweater Set',
                    'price' => 1299.00,
                    'discount_price' => 999.00,
                    'season' => 'winter',
                    'fabric' => 'Soft Baby Wool',
                    'short_description' => 'Cozy fleece lined knitted cardigan sweater with matching booties and cap.',
                    'colors' => [['Baby Blue', '#89CFF0'], ['Baby Pink', '#F4C2C2']],
                    'sizes' => ['3-6 Months', '6-12 Months', '12-24 Months'],
                    'images' => [
                        'https://images.unsplash.com/photo-1519689680058-324335c77eba?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1519238263530-99bdd11df2ea?auto=format&fit=crop&w=800&q=80'
                    ],
                ],
            ],

            // ==================== SCHOOL UNIFORMS ====================
            'school-uniforms-shirts-tops' => [
                [
                    'name' => 'Official White Collared School Uniform Shirt',
                    'price' => 699.00,
                    'discount_price' => 549.00,
                    'season' => 'all_season',
                    'fabric' => '65% Poly 35% Cotton',
                    'short_description' => 'Durable easy-iron school shirt with reinforced collar and double-stitched seams.',
                    'colors' => [['Pure White', '#FFFFFF']],
                    'sizes' => ['28', '30', '32', '34', '36', '38'],
                    'images' => [
                        '/images/banners/banner3.jpg',
                        'https://images.unsplash.com/photo-1620012253295-c15c429f6dd5?auto=format&fit=crop&w=800&q=80'
                    ],
                    'uniform' => [
                        'school_name' => 'DAV Public School, Amritsar',
                        'class_grade' => 'Class 1st - 5th',
                        'gender' => 'unisex',
                        'item_type' => 'Shirt',
                    ],
                    'best_seller' => true,
                ],
                [
                    'name' => 'Sky Blue Half-Sleeve Summer School Shirt',
                    'price' => 649.00,
                    'discount_price' => null,
                    'season' => 'summer',
                    'fabric' => 'Cotton Blend',
                    'short_description' => 'Official summer sky-blue uniform shirt tailored for Spring Dale Senior School students.',
                    'colors' => [['Sky Blue', '#87CEEB']],
                    'sizes' => ['30', '32', '34', '36'],
                    'images' => [
                        '/images/banners/banner3.jpg',
                        'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?auto=format&fit=crop&w=800&q=80'
                    ],
                    'uniform' => [
                        'school_name' => 'Spring Dale Senior School',
                        'class_grade' => 'Class 6th - 10th',
                        'gender' => 'boys',
                        'item_type' => 'Shirt',
                    ],
                ],
                [
                    'name' => 'Light Grey Formal Senior School Shirt',
                    'price' => 749.00,
                    'discount_price' => 599.00,
                    'season' => 'all_season',
                    'fabric' => 'Spun Cotton Poly',
                    'short_description' => 'Approved senior secondary uniform shirt for Delhi Public School, Amritsar.',
                    'colors' => [['Light Grey', '#D3D3D3']],
                    'sizes' => ['34', '36', '38', '40'],
                    'images' => [
                        '/images/banners/banner3.jpg',
                        'https://images.unsplash.com/photo-1620012253295-c15c429f6dd5?auto=format&fit=crop&w=800&q=80'
                    ],
                    'uniform' => [
                        'school_name' => 'Delhi Public School, Amritsar',
                        'class_grade' => 'Class 11th - 12th',
                        'gender' => 'unisex',
                        'item_type' => 'Shirt',
                    ],
                ],
            ],

            'school-uniforms-trousers-shorts-skirts' => [
                [
                    'name' => 'Navy Blue Pleated School Uniform Trousers',
                    'price' => 899.00,
                    'discount_price' => 749.00,
                    'season' => 'all_season',
                    'fabric' => 'Durable Poly-Viscose',
                    'short_description' => 'Long-lasting school trousers with expandable elastic back and sturdy zip fly.',
                    'colors' => [['Navy Blue', '#000080']],
                    'sizes' => ['28', '30', '32', '34', '36'],
                    'images' => [
                        '/images/banners/banner3.jpg',
                        'https://images.unsplash.com/photo-1473966968600-fa801b869a1a?auto=format&fit=crop&w=800&q=80'
                    ],
                    'uniform' => [
                        'school_name' => 'DAV Public School, Amritsar',
                        'class_grade' => 'Class 6th - 10th',
                        'gender' => 'boys',
                        'item_type' => 'Trouser',
                    ],
                ],
                [
                    'name' => 'Grey Pleated Girls School Uniform Skirt',
                    'price' => 799.00,
                    'discount_price' => 649.00,
                    'season' => 'all_season',
                    'fabric' => 'Wrinkle-Free Suiting Fabric',
                    'short_description' => 'Neatly creased knife pleated skirt with side zip and button pocket.',
                    'colors' => [['Charcoal Grey', '#4A4A4A']],
                    'sizes' => ['26', '28', '30', '32'],
                    'images' => [
                        '/images/banners/banner3.jpg',
                        'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?auto=format&fit=crop&w=800&q=80'
                    ],
                    'uniform' => [
                        'school_name' => 'Spring Dale Senior School',
                        'class_grade' => 'Class 1st - 5th',
                        'gender' => 'girls',
                        'item_type' => 'Skirt',
                    ],
                ],
                [
                    'name' => 'Junior Boys Navy School Half Pants',
                    'price' => 599.00,
                    'discount_price' => 499.00,
                    'season' => 'summer',
                    'fabric' => 'Heavy Twill Poly-Cotton',
                    'short_description' => 'Comfortable elastic-waist shorts for primary classes with reinforced belt loops.',
                    'colors' => [['Navy Blue', '#000080']],
                    'sizes' => ['22', '24', '26', '28'],
                    'images' => [
                        '/images/banners/banner3.jpg',
                        'https://images.unsplash.com/photo-1473966968600-fa801b869a1a?auto=format&fit=crop&w=800&q=80'
                    ],
                    'uniform' => [
                        'school_name' => 'DAV Public School, Amritsar',
                        'class_grade' => 'Class 1st - 5th',
                        'gender' => 'boys',
                        'item_type' => 'Trouser',
                    ],
                ],
            ],

            'school-uniforms-blazers-sweaters' => [
                [
                    'name' => 'Official Maroon Woolen Winter School Blazer',
                    'price' => 2499.00,
                    'discount_price' => 1999.00,
                    'season' => 'winter',
                    'fabric' => 'Wool Rich Blend',
                    'short_description' => 'Heavy tailored winter blazer with satin lining and school crest pocket patch option.',
                    'colors' => [['Maroon', '#800000']],
                    'sizes' => ['30', '32', '34', '36', '38'],
                    'images' => [
                        '/images/banners/banner3.jpg',
                        'https://images.unsplash.com/photo-1507679799987-c73779587ccf?auto=format&fit=crop&w=800&q=80'
                    ],
                    'uniform' => [
                        'school_name' => 'Spring Dale Senior School',
                        'class_grade' => 'Class 6th - 10th',
                        'gender' => 'unisex',
                        'item_type' => 'Blazer',
                    ],
                    'featured' => true,
                ],
                [
                    'name' => 'V-Neck Navy Blue School Pullover Sweater',
                    'price' => 999.00,
                    'discount_price' => 799.00,
                    'season' => 'winter',
                    'fabric' => '100% Cashmilon Wool',
                    'short_description' => 'Warm, anti-pilling full sleeve sweater with contrast ribbed school color cuffs.',
                    'colors' => [['Navy Blue', '#000080']],
                    'sizes' => ['28', '30', '32', '34', '36'],
                    'images' => [
                        '/images/banners/banner3.jpg',
                        'https://images.unsplash.com/photo-1620012253295-c15c429f6dd5?auto=format&fit=crop&w=800&q=80'
                    ],
                    'uniform' => [
                        'school_name' => 'DAV Public School, Amritsar',
                        'class_grade' => 'Class 1st - 5th',
                        'gender' => 'unisex',
                        'item_type' => 'Blazer',
                    ],
                ],
                [
                    'name' => 'Sleeveless Winter Cardigan with School Trim',
                    'price' => 799.00,
                    'discount_price' => 649.00,
                    'season' => 'winter',
                    'fabric' => 'Acrylic Wool Blend',
                    'short_description' => 'Lightweight sleeveless sweater for mild Amritsar autumn and spring mornings.',
                    'colors' => [['Bottle Green', '#006A4E']],
                    'sizes' => ['30', '32', '34', '36'],
                    'images' => [
                        '/images/banners/banner3.jpg',
                        'https://images.unsplash.com/photo-1507679799987-c73779587ccf?auto=format&fit=crop&w=800&q=80'
                    ],
                    'uniform' => [
                        'school_name' => 'Delhi Public School, Amritsar',
                        'class_grade' => 'Class 6th - 10th',
                        'gender' => 'unisex',
                        'item_type' => 'Blazer',
                    ],
                ],
            ],

            'school-uniforms-tracksuits-house-t-shirts' => [
                [
                    'name' => 'School House Color Sports Polo T-Shirt',
                    'price' => 499.00,
                    'discount_price' => 399.00,
                    'season' => 'all_season',
                    'fabric' => 'Dri-Fit Breathable Mesh',
                    'short_description' => 'House activity sports polo in Red, Blue, Green, and Yellow with school emblem print.',
                    'colors' => [['House Red', '#FF0000'], ['House Blue', '#0000FF'], ['House Green', '#008000'], ['House Yellow', '#FFFF00']],
                    'sizes' => ['28', '30', '32', '34', '36'],
                    'images' => [
                        '/images/banners/banner3.jpg',
                        'https://images.unsplash.com/photo-1503919545889-aef636e10ad4?auto=format&fit=crop&w=800&q=80'
                    ],
                    'uniform' => [
                        'school_name' => 'DAV Public School, Amritsar',
                        'class_grade' => 'Class 1st - 5th',
                        'gender' => 'unisex',
                        'item_type' => 'Tracksuit',
                    ],
                ],
                [
                    'name' => 'Fleece-Lined Athletic School Tracksuit Set',
                    'price' => 1499.00,
                    'discount_price' => 1199.00,
                    'season' => 'winter',
                    'fabric' => 'Micro-Peach Fleece',
                    'short_description' => 'Matching zip jacket and track pant with dual piping for annual sports day and PT periods.',
                    'colors' => [['Navy & White', '#000080']],
                    'sizes' => ['30', '32', '34', '36', '38'],
                    'images' => [
                        '/images/banners/banner3.jpg',
                        'https://images.unsplash.com/photo-1519238263530-99bdd11df2ea?auto=format&fit=crop&w=800&q=80'
                    ],
                    'uniform' => [
                        'school_name' => 'Spring Dale Senior School',
                        'class_grade' => 'Class 6th - 10th',
                        'gender' => 'unisex',
                        'item_type' => 'Tracksuit',
                    ],
                ],
                [
                    'name' => 'Quick-Dry Sports Jersey Shorts Set',
                    'price' => 699.00,
                    'discount_price' => 549.00,
                    'season' => 'summer',
                    'fabric' => 'Polyester Knit',
                    'short_description' => 'Lightweight athletic sports shorts with elastic drawstring and side ventilation panels.',
                    'colors' => [['Black & Yellow', '#000000']],
                    'sizes' => ['28', '30', '32', '34'],
                    'images' => [
                        '/images/banners/banner3.jpg',
                        'https://images.unsplash.com/photo-1503919545889-aef636e10ad4?auto=format&fit=crop&w=800&q=80'
                    ],
                    'uniform' => [
                        'school_name' => 'Delhi Public School, Amritsar',
                        'class_grade' => 'Class 6th - 10th',
                        'gender' => 'unisex',
                        'item_type' => 'Tracksuit',
                    ],
                ],
            ],

            'school-uniforms-ties-belts-socks' => [
                [
                    'name' => 'Woven Striped School Uniform Tie',
                    'price' => 199.00,
                    'discount_price' => 149.00,
                    'season' => 'all_season',
                    'fabric' => 'Jacquard Polyester',
                    'short_description' => 'Approved striped school tie with adjustable elastic band or classic knot option.',
                    'colors' => [['Maroon & Gold', '#800000']],
                    'sizes' => ['Standard Length'],
                    'images' => [
                        '/images/banners/banner3.jpg',
                        'https://images.unsplash.com/photo-1620012253295-c15c429f6dd5?auto=format&fit=crop&w=800&q=80'
                    ],
                    'uniform' => [
                        'school_name' => 'Spring Dale Senior School',
                        'class_grade' => 'Class 1st - 5th',
                        'gender' => 'unisex',
                        'item_type' => 'Tie',
                    ],
                ],
                [
                    'name' => 'Durable Adjustable School Uniform Belt',
                    'price' => 199.00,
                    'discount_price' => 149.00,
                    'season' => 'all_season',
                    'fabric' => 'High-Density Nylon Webbing',
                    'short_description' => 'Heavy-duty webbing belt with engraved metal school buckle mechanism.',
                    'colors' => [['Navy Blue', '#000080']],
                    'sizes' => ['Free Size (Adjustable)'],
                    'images' => [
                        '/images/banners/banner3.jpg',
                        'https://images.unsplash.com/photo-1473966968600-fa801b869a1a?auto=format&fit=crop&w=800&q=80'
                    ],
                    'uniform' => [
                        'school_name' => 'DAV Public School, Amritsar',
                        'class_grade' => 'Class 6th - 10th',
                        'gender' => 'unisex',
                        'item_type' => 'Belt',
                    ],
                ],
                [
                    'name' => 'Cotton-Rich Cushioned School Socks (Pack of 3)',
                    'price' => 299.00,
                    'discount_price' => 249.00,
                    'season' => 'all_season',
                    'fabric' => '80% Combed Cotton, 20% Elastane',
                    'short_description' => 'Anti-odor breathable school socks with reinforced heel and toe for all-day comfort.',
                    'colors' => [['White with Dual Stripe', '#FFFFFF']],
                    'sizes' => ['Small (Shoe 1-3)', 'Medium (Shoe 4-7)', 'Large (Shoe 8-10)'],
                    'images' => [
                        '/images/banners/banner3.jpg',
                        'https://images.unsplash.com/photo-1542272604-780c96856592?auto=format&fit=crop&w=800&q=80'
                    ],
                    'uniform' => [
                        'school_name' => 'Delhi Public School, Amritsar',
                        'class_grade' => 'Class 1st - 5th',
                        'gender' => 'unisex',
                        'item_type' => 'Socks',
                    ],
                ],
            ],

            // ==================== WINTER WEAR ====================
            'winter-wear-mens-woolens' => [
                [
                    'name' => 'Premium Lambswool Cable Knit Winter Sweater',
                    'price' => 2899.00,
                    'discount_price' => 2299.00,
                    'season' => 'winter',
                    'fabric' => '100% Pure Lambswool',
                    'short_description' => 'Timeless crew-neck cable pattern sweater offering superior warmth in North Indian winters.',
                    'colors' => [['Forest Green', '#228B22'], ['Charcoal', '#36454F']],
                    'sizes' => ['M', 'L', 'XL', 'XXL'],
                    'images' => [
                        'https://images.unsplash.com/photo-1620012253295-c15c429f6dd5?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1602810318383-e386cc2a3ccf?auto=format&fit=crop&w=800&q=80'
                    ],
                    'featured' => true,
                ],
                [
                    'name' => 'Heavy Quilted Windproof Winter Puffer Jacket',
                    'price' => 3999.00,
                    'discount_price' => 3199.00,
                    'season' => 'winter',
                    'fabric' => 'High-Density Nylon & Down Fill',
                    'short_description' => 'Thermal insulated water-resistant puffer with detachable hood and fleece-lined pockets.',
                    'colors' => [['Matte Black', '#000000']],
                    'sizes' => ['L', 'XL', 'XXL'],
                    'images' => [
                        'https://images.unsplash.com/photo-1544923246-77307dd654cb?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1507679799987-c73779587ccf?auto=format&fit=crop&w=800&q=80'
                    ],
                ],
                [
                    'name' => 'Ultra Warm Thermal Innerwear Top & Bottom Set',
                    'price' => 1299.00,
                    'discount_price' => 999.00,
                    'season' => 'winter',
                    'fabric' => 'Poly-Cotton Thermal Knit',
                    'short_description' => 'Body-hugging featherweight thermal base layer retaining body heat down to 2°C.',
                    'colors' => [['Dark Grey', '#A9A9A9']],
                    'sizes' => ['M', 'L', 'XL'],
                    'images' => [
                        'https://images.unsplash.com/photo-1602810318383-e386cc2a3ccf?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1620012253295-c15c429f6dd5?auto=format&fit=crop&w=800&q=80'
                    ],
                ],
            ],

            'winter-wear-womens-cardigans-shawls' => [
                [
                    'name' => 'Cashmere Blend Open-Front Longline Cardigan',
                    'price' => 2499.00,
                    'discount_price' => 1999.00,
                    'season' => 'winter',
                    'fabric' => 'Cashmere & Wool Blend',
                    'short_description' => 'Elegant draped cardigan with ribbed cuffs and roomy deep pockets for winter evenings.',
                    'colors' => [['Camel Tan', '#C19A6B']],
                    'sizes' => ['Free Size', 'L', 'XL'],
                    'images' => [
                        'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=800&q=80'
                    ],
                    'best_seller' => true,
                ],
                [
                    'name' => 'Traditional Amritsari Pure Woolen Lohi Shawl',
                    'price' => 1899.00,
                    'discount_price' => 1499.00,
                    'season' => 'winter',
                    'fabric' => '100% Pure Fine Wool',
                    'short_description' => 'Heritage Punjab lohi with woven borders, ideal for chilly morning temple visits.',
                    'colors' => [['Natural Cream', '#FFFDD0']],
                    'sizes' => ['Large Wrap (1.25x2.5m)'],
                    'images' => [
                        'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?auto=format&fit=crop&w=800&q=80'
                    ],
                ],
                [
                    'name' => 'Embroidered Kashmiri Woolen Poncho Cape',
                    'price' => 2199.00,
                    'discount_price' => null,
                    'season' => 'winter',
                    'fabric' => 'Soft Boiled Wool',
                    'short_description' => 'Slip-on poncho with fringed hemline and intricate Aari floral embroidery.',
                    'colors' => [['Deep Burgundy', '#800020']],
                    'sizes' => ['Free Size'],
                    'images' => [
                        'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=800&q=80'
                    ],
                ],
            ],

            'winter-wear-kids-winter-wear' => [
                [
                    'name' => 'Kids Sherpa-Lined Warm Fleece Hoodie Jacket',
                    'price' => 1499.00,
                    'discount_price' => 1199.00,
                    'season' => 'winter',
                    'fabric' => 'Thermal Fleece & Sherpa',
                    'short_description' => 'Super cozy zip jacket with soft plush lining and wind-resistant outer shell.',
                    'colors' => [['Red Plaid', '#CC0000']],
                    'sizes' => ['4-5 Yrs', '6-7 Yrs', '8-9 Yrs', '10-11 Yrs'],
                    'images' => [
                        'https://images.unsplash.com/photo-1519238263530-99bdd11df2ea?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1503919545889-aef636e10ad4?auto=format&fit=crop&w=800&q=80'
                    ],
                    'featured' => true,
                ],
                [
                    'name' => 'Kids Winter Thermal Innerwear Set (Top + Bottom)',
                    'price' => 899.00,
                    'discount_price' => 699.00,
                    'season' => 'winter',
                    'fabric' => 'Cotton Quilted Thermal',
                    'short_description' => 'Soft non-itchy winter thermals keeping active kids warm during winter school hours.',
                    'colors' => [['Off White', '#F8F8FF']],
                    'sizes' => ['3-4 Yrs', '5-6 Yrs', '7-8 Yrs'],
                    'images' => [
                        'https://images.unsplash.com/photo-1518831959646-742c3a14ebf7?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1519238263530-99bdd11df2ea?auto=format&fit=crop&w=800&q=80'
                    ],
                ],
                [
                    'name' => 'Cute Knitted Bear Ears Beanie & Scarf Set',
                    'price' => 599.00,
                    'discount_price' => 449.00,
                    'season' => 'winter',
                    'fabric' => 'Soft Spun Acrylic',
                    'short_description' => 'Playful winter cap and warm muffler set with fleece lining for ears and neck protection.',
                    'colors' => [['Mustard Yellow', '#FFDB58'], ['Soft Pink', '#FFB6C1']],
                    'sizes' => ['2-6 Yrs', '7-12 Yrs'],
                    'images' => [
                        'https://images.unsplash.com/photo-1519689680058-324335c77eba?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1519238263530-99bdd11df2ea?auto=format&fit=crop&w=800&q=80'
                    ],
                ],
            ],

            // ==================== SUMMER WEAR ====================
            'summer-wear-pure-cotton-collection' => [
                [
                    'name' => 'Pure Mulmul Breathable Summer Floral Kurta',
                    'price' => 1399.00,
                    'discount_price' => 1099.00,
                    'season' => 'summer',
                    'fabric' => '100% Mulmul Cotton',
                    'short_description' => 'Light, breezy natural dyed cotton kurta designed to keep you cool in hot Amritsar summers.',
                    'colors' => [['Pastel Turquoise', '#40E0D0']],
                    'sizes' => ['S', 'M', 'L', 'XL'],
                    'images' => [
                        'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=800&q=80'
                    ],
                    'featured' => true,
                ],
                [
                    'name' => 'Breezy Handloom Cotton Resort Collar Shirt',
                    'price' => 1499.00,
                    'discount_price' => 1199.00,
                    'season' => 'summer',
                    'fabric' => '100% Pure Handloom Cotton',
                    'short_description' => 'Relaxed camp collar casual shirt with wooden button details and airy open weave.',
                    'colors' => [['Ivory White', '#FFFFF0']],
                    'sizes' => ['M', 'L', 'XL'],
                    'images' => [
                        'https://images.unsplash.com/photo-1602810318383-e386cc2a3ccf?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?auto=format&fit=crop&w=800&q=80'
                    ],
                ],
                [
                    'name' => 'Lightweight Cotton Casual Drawstring Trousers',
                    'price' => 1299.00,
                    'discount_price' => null,
                    'season' => 'summer',
                    'fabric' => '100% Cotton Poplin',
                    'short_description' => 'Elasticated drawstring waist loose-fit trousers for relaxed summer evenings.',
                    'colors' => [['Sand Khaki', '#C3B091']],
                    'sizes' => ['30', '32', '34', '36'],
                    'images' => [
                        'https://images.unsplash.com/photo-1473966968600-fa801b869a1a?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?auto=format&fit=crop&w=800&q=80'
                    ],
                ],
            ],

            'summer-wear-linen-blend-wear' => [
                [
                    'name' => '100% Pure European Flax Linen Relaxed Shirt',
                    'price' => 2299.00,
                    'discount_price' => 1799.00,
                    'season' => 'summer',
                    'fabric' => '100% European Linen',
                    'short_description' => 'Naturally breathable premium linen shirt designed for high temperature and humidity.',
                    'colors' => [['Sage Green', '#9DC183'], ['Sky Blue', '#87CEEB']],
                    'sizes' => ['M', 'L', 'XL', 'XXL'],
                    'images' => [
                        'https://images.unsplash.com/photo-1603252109303-2751441dd157?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?auto=format&fit=crop&w=800&q=80'
                    ],
                    'best_seller' => true,
                ],
                [
                    'name' => 'Linen Blend Summer Chino Walking Shorts',
                    'price' => 1399.00,
                    'discount_price' => 1099.00,
                    'season' => 'summer',
                    'fabric' => '55% Linen, 45% Cotton',
                    'short_description' => 'Comfort-fit tailored shorts with side slant pockets and breathable weave.',
                    'colors' => [['Natural Flax', '#E5D3B3']],
                    'sizes' => ['30', '32', '34', '36'],
                    'images' => [
                        'https://images.unsplash.com/photo-1473966968600-fa801b869a1a?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1542272604-780c96856592?auto=format&fit=crop&w=800&q=80'
                    ],
                ],
                [
                    'name' => 'Classic Linen Nehru Jacket for Summer Weddings',
                    'price' => 2799.00,
                    'discount_price' => 2199.00,
                    'season' => 'summer',
                    'fabric' => 'Pure Linen with Cotton Lining',
                    'short_description' => 'Breathable sleeveless waist jacket with mandarin collar and coconut shell buttons.',
                    'colors' => [['Powder Blue', '#B0E0E6']],
                    'sizes' => ['38', '40', '42', '44'],
                    'images' => [
                        'https://images.unsplash.com/photo-1507679799987-c73779587ccf?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1594938298603-c8148c4dae35?auto=format&fit=crop&w=800&q=80'
                    ],
                ],
            ],
        ];

        $globalProductIndex = 1;

        foreach ($itemsBySubcategory as $subcatSlug => $productsList) {
            $category = Category::where('slug', $subcatSlug)->first();

            if (!$category) {
                // If subcategory not found, try finding by matching name or parent
                $category = Category::where('slug', 'like', "%{$subcatSlug}%")->first();
            }

            if (!$category) {
                continue;
            }

            foreach ($productsList as $pData) {
                $sku = sprintf('BF-%04d', $globalProductIndex);
                $slug = Str::slug($pData['name']);

                // Ensure unique slug
                if (Product::where('slug', $slug)->exists()) {
                    $slug = $slug . '-' . $globalProductIndex;
                }

                $product = Product::updateOrCreate(
                    ['sku' => $sku],
                    [
                        'category_id' => $category->id,
                        'name' => $pData['name'],
                        'slug' => $slug,
                        'price' => $pData['price'],
                        'discount_price' => $pData['discount_price'] ?? null,
                        'season' => $pData['season'] ?? 'all_season',
                        'fabric' => $pData['fabric'] ?? 'Cotton Blend',
                        'short_description' => $pData['short_description'],
                        'description' => $pData['short_description'] . "\n\nAvailable exclusively at Bombay Fashion, Mahila College Road, Amritsar. Premium craftsmanship, quality guaranteed.",
                        'stock' => 25,
                        'is_active' => true,
                        'is_featured' => $pData['featured'] ?? false,
                        'is_new_arrival' => $pData['new_arrival'] ?? false,
                        'is_best_seller' => $pData['best_seller'] ?? false,
                        'meta_title' => $pData['name'] . ' - Bombay Fashion Amritsar',
                        'meta_description' => Str::limit($pData['short_description'], 150),
                    ]
                );

                // Attach Images
                ProductImage::where('product_id', $product->id)->delete();
                $images = $pData['images'] ?? [];
                foreach ($images as $imgIdx => $imgUrl) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_url' => $imgUrl,
                        'public_id' => 'bf_prod_' . $globalProductIndex . '_' . ($imgIdx + 1),
                        'is_primary' => $imgIdx === 0,
                        'display_order' => $imgIdx,
                    ]);
                }

                // Attach Variants
                ProductVariant::where('product_id', $product->id)->delete();
                $colors = $pData['colors'] ?? [['Default', '#C2185B']];
                $sizes = $pData['sizes'] ?? ['Free Size'];

                $variantIndex = 1;
                foreach ($colors as $c) {
                    $colorName = is_array($c) ? $c[0] : $c;
                    $colorCode = is_array($c) ? $c[1] : null;

                    foreach ($sizes as $sz) {
                        ProductVariant::create([
                            'product_id' => $product->id,
                            'size' => $sz,
                            'color' => $colorName,
                            'color_code' => $colorCode,
                            'sku' => $sku . '-V' . $variantIndex,
                            'additional_price' => 0.00,
                            'stock' => 15,
                            'is_active' => true,
                        ]);
                        $variantIndex++;
                    }
                }

                // Attach School Uniform Details if applicable
                if (isset($pData['uniform'])) {
                    SchoolUniform::updateOrCreate(
                        ['product_id' => $product->id],
                        [
                            'school_name' => $pData['uniform']['school_name'],
                            'class_grade' => $pData['uniform']['class_grade'],
                            'gender' => $pData['uniform']['gender'] ?? 'unisex',
                            'item_type' => $pData['uniform']['item_type'] ?? 'Uniform',
                        ]
                    );
                }

                $globalProductIndex++;
            }
        }
    }
}

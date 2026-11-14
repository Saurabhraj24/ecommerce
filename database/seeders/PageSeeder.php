<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'title' => 'About Bombay Fashion',
                'slug' => 'about-us',
                'content' => "<h2>Welcome to Bombay Fashion, Amritsar</h2><p>Located prominently on <strong>Mahila College Road, opposite Mahila College in Amritsar, Punjab</strong>, Bombay Fashion has been a trusted family fashion and uniform store for years. Known for providing the finest fabrics, contemporary designer trends, traditional Punjabi attires, handpicked sarees, and certified school uniforms.</p><h3>Our Promise</h3><p>We believe in high-quality craftsmanship, fair and honest pricing, and genuine Punjabi hospitality. Whether you are shopping for a special wedding function, everyday stylish comfort, seasonal winter shawls and woolens, or school uniforms for your children, Bombay Fashion is your ultimate one-stop shopping destination.</p><p>We are open daily until 9:30 PM and warmly welcome you both online and at our brick-and-mortar showroom in Amritsar.</p>",
                'meta_title' => 'About Bombay Fashion | Amritsar Clothing Store',
                'meta_description' => 'Learn about Bombay Fashion, Amritsar\'s leading store for ethnic wear, sarees, kids clothing, and school uniforms.',
                'is_active' => true,
            ],
            [
                'title' => 'Contact Us',
                'slug' => 'contact-us',
                'content' => "<p>We love to hear from our customers! Visit our physical retail store in Amritsar or reach out through our online inquiry channels.</p><ul><li><strong>Address:</strong> Mahila College Road, opposite Mahila College, Amritsar, Punjab, India</li><li><strong>Store Timings:</strong> Open daily, closes 9:30 PM</li><li><strong>Phone Support:</strong> +91 98765 43210</li><li><strong>WhatsApp:</strong> Available daily for queries and custom stitching assistance</li><li><strong>Email:</strong> care@bombayfashionamritsar.com</li></ul>",
                'meta_title' => 'Contact Bombay Fashion | Amritsar',
                'meta_description' => 'Contact Bombay Fashion store in Amritsar. Visit us on Mahila College Road or send an inquiry online.',
                'is_active' => true,
            ],
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'content' => "<h2>Privacy Policy</h2><p>At Bombay Fashion, accessible from our official website, one of our main priorities is the privacy of our visitors. This Privacy Policy document outlines the types of information collected and recorded by Bombay Fashion and how we use it.</p><h3>Information We Collect</h3><p>When you register, place an order, or submit an inquiry, we collect necessary personal details such as your name, delivery address, phone number, and email address to process orders and provide customer support.</p><h3>Payment Security</h3><p>Online payments are processed securely through certified payment gateways (such as Razorpay). We never store your full credit card numbers or UPI MPIN on our servers.</p>",
                'meta_title' => 'Privacy Policy | Bombay Fashion',
                'meta_description' => 'Read how Bombay Fashion protects customer personal information and transaction privacy.',
                'is_active' => true,
            ],
            [
                'title' => 'Terms & Conditions',
                'slug' => 'terms-conditions',
                'content' => "<h2>Terms & Conditions</h2><p>By accessing or purchasing from Bombay Fashion's online store, you agree to comply with our store terms and conditions. All prices listed are in Indian Rupees (INR) and inclusive of applicable taxes unless stated otherwise.</p><h3>Order Acceptance</h3><p>We reserve the right to verify Cash on Delivery (COD) orders via phone/SMS prior to dispatch to prevent delivery discrepancies.</p>",
                'meta_title' => 'Terms & Conditions | Bombay Fashion',
                'meta_description' => 'Terms and conditions governing online orders and store services at Bombay Fashion Amritsar.',
                'is_active' => true,
            ],
            [
                'title' => 'Return & Exchange Policy',
                'slug' => 'return-policy',
                'content' => "<h2>Return & Exchange Policy</h2><p>We want you to love what you wear! If for any reason you are not completely satisfied with your purchase, you may initiate a return or exchange request within <strong>7 days of delivery</strong>.</p><h3>Eligibility for Returns</h3><ul><li>Items must be unused, unwashed, and with all original tags attached.</li><li>School uniform size exchanges are accepted promptly.</li><li>For COD orders, refunds can be processed directly to your provided bank account/UPI after the returned item passes quality verification.</li></ul>",
                'meta_title' => 'Return & Exchange Policy | Bombay Fashion',
                'meta_description' => 'Hassle-free 7-day return and size exchange policy for clothing and school uniforms at Bombay Fashion.',
                'is_active' => true,
            ],
            [
                'title' => 'Shipping & Delivery Policy',
                'slug' => 'shipping-policy',
                'content' => "<h2>Shipping Policy</h2><p>We deliver all across India through reputable courier partners. Orders are dispatched from our Amritsar store within 24-48 business hours.</p><ul><li><strong>Free Shipping:</strong> On all orders above ₹999.</li><li><strong>Standard Shipping:</strong> A flat ₹50 shipping charge applies to orders under ₹999.</li><li><strong>Delivery Timeline:</strong> Typically 2-4 business days within Punjab and North India, and 4-7 business days across other parts of India.</li><li><strong>Tracking:</strong> Once your order is shipped, you will receive a tracking link to view your package timeline.</li></ul>",
                'meta_title' => 'Shipping & Delivery Policy | Bombay Fashion',
                'meta_description' => 'Fast dispatch from Amritsar across India with free shipping on orders above ₹999.',
                'is_active' => true,
            ],
        ];

        foreach ($pages as $page) {
            Page::firstOrCreate(['slug' => $page['slug']], $page);
        }
    }
}

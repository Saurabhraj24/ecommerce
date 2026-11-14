# Bombay Fashion Amritsar — Cloud Architecture & Development Master Log (`cloud.md`)

> **Store Information**:  
> **Shop Name**: Bombay Fashion  
> **Address**: Mahila College Road, opposite Mahila College, Amritsar, Punjab, India  
> **Timings**: Open daily, closes 9:30 PM  
> **Support Phone**: +91 98765 43210  
> **WhatsApp Support**: +91 98765 43210  
> **Initial Admin Email**: `admin@bombayfashion.com` | Password: `Admin@Bombay123!`  

---

## 1. Project Overview & Business Model

Bombay Fashion is a retail fashion landmark in Amritsar, Punjab, specializing in:
- Handcrafted bridal and designer sarees (Silk, Cotton, Designer, Bridal)
- Punjabi suits, salwar kameez, and anarkalis
- Men's kurtas, sherwanis, formal shirts, and jackets
- Children's wear (boys, girls, infants)
- Certified school uniforms for Amritsar schools (by school name, class, gender, and size)
- Seasonal collections: Winter Wear (shawls, cardigans, thermals) and Summer Wear (light cottons, linens)

Customers can browse, search with live suggestions, add to cart, and checkout with **Razorpay (UPI, Google Pay, PhonePe, Cards, NetBanking)** or **Cash on Delivery (COD)**.

---

## 2. Technology Stack

- **Backend**: Laravel 12 (latest stable), PHP 8.2+
- **Database**: MySQL 8.x with 10 structured migration tables, foreign keys, composite indexes, soft deletes, and decimal precision for currency.
- **Frontend**: Blade templates, Tailwind CSS v4 (`@tailwindcss/vite`), Alpine.js v3, Vite v7
- **Media Storage**: Cloudinary via `cloudinary-labs/cloudinary-laravel` with local storage fallback (`CloudinaryImageService`)
- **Payments**: Razorpay SDK (`razorpay/razorpay`) with server-side HMAC-SHA256 signature verification and Cash on Delivery
- **Invoices**: DomPDF (`barryvdh/laravel-dompdf`) for customer order invoices and warehouse packing slips

---

## 3. How to Open & Test on Your Mobile Phone

### Step 1: Connect your phone to the same Wi-Fi
Make sure your mobile phone and your computer are connected to the same Wi-Fi network.

### Step 2: Ensure server is running on `0.0.0.0`
The Laravel development server is configured to bind to all network interfaces:
```bash
php artisan serve --host=0.0.0.0 --port=8000
```

### Step 3: Open the URL on your mobile browser (Chrome/Safari)
Open your phone's browser and type:
```
http://10.188.147.90:8000
```
*(If your computer's Wi-Fi IP changes in the future, check it by running `ipconfig` in terminal).*

---

## 4. Mobile-Specific Design & UX Optimizations

| Mobile Element | Previous Issue | Solution Applied |
| :--- | :--- | :--- |
| **Sticky Header** | Translucent glass allowed scrolled content to bleed through behind text. | Header made opaque (`bg-white/98 backdrop-blur-xl border-b border-primary-100`) preventing any bleed-through. |
| **Header Sizing & Layout** | Logo and text button "Sign In" were crammed together on mobile, causing "Sign \n In" to wrap awkwardly into two lines. | Header height reduced from `h-20` to `h-16 sm:h-20`. Logo font sized to `text-lg sm:text-2xl`. On mobile, replaced large button with a sleek user avatar icon; full sign-in button retained on tablets/desktops. |
| **Bottom Navigation Bar** | Missing mobile bottom navigation. | Added native app-style bottom navigation bar with 5 thumb-friendly items: **Home**, **Shop (Collections)**, **Uniforms**, **Bag (with live count badge)**, and **Account/Sign In**. |
| **Hero Banner Typography** | Huge `text-4xl sm:text-6xl` caused headline to take over the whole mobile screen. | Adjusted to `text-2xl sm:text-4xl lg:text-5xl` with compact padding `p-5 sm:p-14`. Buttons aligned with responsive `flex-col sm:flex-row`. |
| **Hero Button Arrow SVG** | SVG expanded to full button width due to flex stretch. | Added `shrink-0` and explicit `width="16" height="16"` attributes plus button layout `gap-2` in CSS. |
| **Key Highlights** | Four items appeared flat and vertical with faint borders. | Formatted in an explicit responsive grid (`grid-cols-2 md:grid-cols-4`) with distinct rose/gold/teal tinted icon cards. |
| **Category Tiles** | Without photos, displayed bare `ME` initials in a plain box. | Equipped with bespoke luxury Indian motifs: Kurta icon for Men, Saree drape for Sarees, Graduation cap for School Uniforms, Kidswear icon for Children, Snowflake for Winter, Sun for Summer, and Dress for Women. |
| **Footer & Floating Buttons** | Floating WhatsApp button overlapped footer text on mobile screens. | Added `pb-28 md:pb-12` bottom clearance padding to the footer. Repositioned WhatsApp button to `bottom-20 left-4` so it sits safely above the mobile bottom bar. |

---

## 5. Roles & Access Control (3-Tier RBAC)

1. **User (Customer)**:
   - Self-registration & login
   - Product catalog with live search, filters (category, subcategory, season, size, color, price)
   - Wishlist with add/remove
   - Persistent Cart (guests get cookie cart, auto-merged upon login)
   - Checkout with Razorpay UPI/Cards or Cash on Delivery
   - Live Order Tracking by Order Number (`BF-YYYY-XXXXX`)
   - Order cancellation (allowed before shipping) and 7-day return request
   - Customer review submission for purchased products
   - Address book CRUD and profile password management

2. **Staff**:
   - Access to `/admin` panel with restricted permissions
   - Product management (add/edit clothes, variants, stock, Cloudinary photos)
   - Order status processing: Pending &rarr; Confirmed &rarr; Packed &rarr; Shipped &rarr; Delivered
   - Print packing slips and invoices
   - Stock adjustments with audit trail in `stock_logs`
   - Review moderation (approve/reject customer reviews)
   - *Restricted*: Cannot manage staff accounts, roles, store settings, or view financial revenue reports.

3. **Admin (Store Owner)**:
   - Complete superuser control
   - Everything Staff can do, plus:
   - Category management (add/edit nested subcategories, display order, images)
   - Hero banner management (Cloudinary slide images, links, active dates)
   - Coupon codes (percentage or flat discounts, min order amount, expiry)
   - Staff accounts management (create staff, toggle active status)
   - Store settings (shop address, timings, WhatsApp number, tax rate, shipping rules, COD limits, Razorpay keys)
   - Sales analytics and CSV export reports

---

## 6. Strict Data Rule Compliance

- **No fake data / No Lorem Ipsum**: All UI components check for real database records.
- **Empty States**: If a category or filter returns no products, a clean friendly illustration and message appears: *"No products found matching your selection. Please check back soon or browse other collections."*
- **Seeders**: Create ONLY:
  - 3 Roles (`admin`, `staff`, `user`)
  - 1 Initial Admin (`admin@bombayfashion.com`)
  - 7 Base Categories with Subcategories (Men, Women, Sarees, Children, School Uniforms, Winter Wear, Summer Wear)
  - Store Settings (Bombay Fashion, Amritsar physical store info)
  - 5 CMS Policy Pages (About Us, Contact, Privacy, Terms, Returns, Shipping)

---

## 7. Change Log & Continuous Updates

### [Update: 2026-10-24 21:45] — Mobile Responsiveness & CSS Polish
1. **Header Mobile Redesign**:
   - Fixed header background to `bg-white/98 backdrop-blur-xl` with bottom border to eliminate scroll bleed-through.
   - Reduced mobile header height to `h-16` and scaled logo to prevent layout wrapping.
   - Replaced multi-line wrapping "Sign In" button on mobile with a compact icon button; full pill button preserved on screens &ge; 640px.
2. **Mobile Bottom Navigation Bar**:
   - Created `resources/views/layouts/partials/mobile-nav.blade.php`.
   - 5 dedicated touch tabs: Home, Shop, Uniforms, Bag (with real-time item counter), and Account.
3. **Hero Section Refinement**:
   - Scaled default hero heading from `text-4xl` to `text-2xl sm:text-4xl lg:text-5xl`.
   - Adjusted buttons to `flex-col sm:flex-row` with `justify-center` for balanced finger taps.
4. **Category Tiles Visual Motifs**:
   - Replaced bare two-letter initials with Indian boutique icons for Men, Sarees, Uniforms, Children, Winter, Summer, and Women.
5. **Footer Clearance**:
   - Increased footer bottom padding (`pb-28 md:pb-12`) ensuring floating buttons never obscure text.
6. **Server Configuration**:
   - Bound development server to `0.0.0.0:8000` to allow immediate testing on phones via local Wi-Fi IP (`10.188.147.90:8000`).
7. **Test Suite**:
   - 17/17 feature and unit tests passing with 43 assertions.

### [Update: 2026-10-24 22:00] — Auto-Slide Fix, Grand Luxury Slider & 2-Column Mobile Layout
1. **Auto-Slide Resolved (`slide apne aap nahi ho raha tha`)**:
   - Fixed the carousel interval: Set to a snappy **3.5 seconds** (`3500ms`) so slides transition continuously and automatically.
   - Removed the hover/touch lock (`@mouseenter="stopTimer()"` and `@mouseleave`) which previously paused the timer indefinitely when touched or hovered on mobile devices.
   - Removed the inline `style="display: none;"` that was clashing with Alpine's `x-show` visibility engine.
   - Added `[x-cloak] { display: none !important; }` in `app.css` to prevent flash of unstyled content while keeping transitions buttery smooth.
   - Added manual slide reset (`goTo(index)`) so user taps restart the 3.5s cycle smoothly.
2. **Grand Luxury Editorial Banners**:
   - Enlarged the hero card from `380px` to **`min-h-[450px] sm:min-h-[540px] md:min-h-[580px] lg:min-h-[620px]`**.
   - Generated 3 photorealistic, luxury 16:9 images saved to `public/images/banners/`:
     - **Slide 1**: *Handcrafted Bridal Banarasi Silk Sarees* (`banner1.jpg`) — Royal bride in opulent crimson and gold.
     - **Slide 2**: *Festive Punjabi Suits & Designer Anarkalis* (`banner2.jpg`) — Regal Punjabi suit with phulkari dupatta.
     - **Slide 3**: *Certified School Uniforms of Amritsar* (`banner3.jpg`) — Private school uniforms with blazers and ties.
   - Each slide carries its own editorial title, subtitle, badge, and direct call-to-action button.
3. **Mobile Left & Right (2-Column) Layout**:
   - Changed mobile footer from single vertical list (`grid-cols-1`) to an elegant side-by-side 2-column layout (`grid-cols-2`):
     - **Left Column**: Collections (`Men`, `Women`, `Sarees`, `School Uniforms`).
     - **Right Column**: Customer Care (`Track Order`, `Shipping`, `7-Day Returns`, `Contact Us`).
     - **Full Width Bottom**: Payment Badges (UPI, Razorpay, RuPay, Cards, COD Available).
4. **Clean Mobile Header**:
   - Removed duplicate wrapped "Sign In" button on mobile, keeping the top header clean, sleek, and spacious with logo, wishlist heart, and shopping bag. Mobile sign-in is accessible from the bottom navigation bar and hamburger menu.

### [Update: 2026-10-25 20:15] — Complete Responsive Optimization for Mobile, Minimized Desktop & Fullscreen PC
1. **Product Detail Page (`products/show.blade.php`) Fixes**:
   - **Breadcrumb Overhaul**: Replaced rigid `inline-flex` with responsive `flex-wrap gap-1 text-[11px] sm:text-xs` and added `truncate` with `overflow-hidden`. This fixes the root cause of horizontal blowout on small screens and narrow minimized desktop windows.
   - **Adaptive Card Padding**: Adjusted outer card padding to `p-4 sm:p-6 lg:p-8 rounded-2xl sm:rounded-3xl` with `w-full max-w-full overflow-hidden` preventing content from overflowing container boundaries.
   - **Price & Tax Block Re-alignment**: Replaced rigid `flex items-baseline` with responsive layout: main price + discount badge on top, and taxes/free pickup guarantee cleanly formatted in a dedicated row below. Eliminates awkward wrapping or cutting off of `(Inclusive of all taxes)`.
   - **Single-Column Mobile Value Props**: Changed value highlights to `grid-cols-1 sm:grid-cols-2` so local delivery and COD tags do not squeeze each other on phones.
   - **Ratings & SKU Tags**: Added `flex-wrap` and `shrink-0` to guarantee pills and ratings stars for seamless wrapping at all widths.

2. **Header Sizing on Window Resize / Minimize (`layouts/partials/header.blade.php`)**:
   - Added `shrink-0` and responsive `space-x-1.5 sm:space-x-3` to right-hand icons so they never clip or push off the screen edge.
   - Brand logo title styled with `truncate min-w-0` so even on narrow 320px screens or half-screen windows, the header stays balanced.
   - Desktop search input dynamically constrained to `w-44 lg:w-60` to ensure smooth scaling between 768px and 1200px.
   - Guest "Sign In" button displays a neat icon pill on mobile / minimized viewports and full text label on &ge; 640px screens.

3. **Floating WhatsApp & Back to Top Re-positioning**:
   - Moved WhatsApp button from left (`bottom-20 left-4`) to bottom-right (`bottom-20 md:bottom-6 right-4 md:right-6`). This completely solves the issue where the button was hovering directly over product prices and details on mobile.
   - Placed Back to Top button cleanly above WhatsApp (`bottom-36 md:bottom-22 right-4 md:right-6`) avoiding overlapping widgets.
   - Enabled drawer cart opening via both `@open-cart` and `@open-cart-drawer` events in `cart-drawer.blade.php`.

4. **Global Breadcrumbs Across All Pages**:
   - Updated `products/index.blade.php` and `pages/show.blade.php` breadcrumbs to use `flex-wrap gap-1` and prevent container stretching.

5. **Quality & Validation**:
   - Recompiled assets with Vite (`npm run build`) with zero errors.
   - Full test suite verified green with 17/17 passing tests (`artisan test`).

### [Update: 2026-10-25 20:55] — Verified Customer Reviews with Photo Uploads & Sizing/Quality Feedback
1. **Delivered-Only Verification Rule (`order_status === 'delivered'`)**:
   - Only customers who have ordered and received the item (order status explicitly marked as `delivered`) are eligible to write a review.
   - Customers with orders still in transit see an informational delivery progress notice with a direct link to track order.
   - Customers who haven't purchased see an authentic buyer policy notice explaining that only verified buyers can review.
   - Guest visitors see a friendly sign-in prompt.

2. **One Review per Product with In-Place Edit**:
   - Customers can submit a review once per product.
   - If they have already reviewed, their existing review is showcased in a dedicated "Your Verified Product Review" card showing its moderation status (`✓ Published & Live` or `⏳ In Moderation`).
   - A toggle button ("Update Review / Photos") allows them to edit their star rating, title, fit feedback, quality rating, comment, or add photos.

3. **Customer Photo Uploads & Detail Specifications**:
   - **Photo Upload**: Supports uploading up to 3 high-resolution product photos (garment, stitching, or fit) with real-time browser preview thumbnails. Files are uploaded via `CloudinaryImageService` to `bombay_fashion/reviews`.
   - **Fit Feedback**: Customers select between `Runs Small`, `True to Size`, and `Runs Large`.
   - **Quality Feedback**: Customers rate fabric and stitching as `Exceptional`, `Good Quality`, or `Standard`.
   - **Photo Gallery & Lightbox**: Customer photo thumbnails appear on published reviews with a click-to-zoom modal.

4. **Database & Schema Updates**:
   - Migration `2026_10_25_000001_add_images_and_details_to_reviews_table.php` added `images` (json), `fit_feedback` (string), `quality_rating` (string), and `verified_purchase` (boolean) to the `reviews` table.
   - Updated `App\Models\Review` with fillable and cast attributes.

5. **Orders Integration & Moderation**:
   - On the customer's Order Details page (`/orders/{orderNumber}`), each delivered item now features a direct **"★ Write Review & Photos &rarr;"** button linking straight to the review anchor.
   - In the Store Admin Panel (`/admin/reviews`), staff can review customer photos, fit & quality tags, and approve/delete reviews.

6. **Automated Testing**:
   - Added comprehensive feature tests in `tests/Feature/CustomerReviewTest.php` covering:
     - Guest rejection & redirect to login.
     - Unverified customer rejection with error session.
     - Verified customer with delivered order submitting review, fit feedback, quality rating, and photo upload.
     - Review-once-per-product updating verification.
   - 20/20 tests passing with 50 assertions (`artisan test`).

---


---

## 9. Live Delivery Tracker Redesign & Admin Route Audit Log

### A. Live Delivery Progress Card Redesign
- **User Issue**: On `http://127.0.0.1:8000/orders/BF-2026-0004E9ADF`, the delivery progress card was described as *"bahut ganda hai"* and *"why it shoing down"*. The 5 stages were stacked vertically downward in the center with large blank spaces on the sides, while the connecting line was misaligned horizontally at the bottom.
- **Root Cause**:
  - The template used `<div class="grid grid-cols-1 sm:grid-cols-5 gap-4">`. In Tailwind v4 without an active `sm:grid-cols-5` match, CSS Grid defaulted to `grid-cols-1`, stacking all 5 steps into a single vertical column.
  - The connecting bar used an absolute `top-1/2` position across the entire card container, which separated from the vertical node layout.
- **Solution & Redesign**:
  - **Desktop / Tablet (`hidden md:block`)**: Dedicated horizontal rail with explicit flex layout (`flex items-start justify-between`). Features an animated gradient track (`from-festive-rose via-festive-gold to-emerald-500`) with a floating vehicle/beacon (`left: {{ $progressPercent }}%`) that glides to the active stage.
  - **Mobile Phones (`block md:hidden`)**: Dedicated connected vertical timeline with a clean vertical pipe on the left and stage details on the right, matching Apple/Amazon/Uber tracking UX.
  - **Showroom Guarantee Card**: Modern dispatch highlight box detailing Amritsar Showroom origin, boutique seal packaging, and live contact information.

---

### B. Invisible Buttons Resolution (`bg-primary`)
- **User Issue**: Buttons such as "Update Product Changes" appeared as white text on a white/light background, making the text invisible.
- **Root Cause**:
  - In `resources/css/app.css`, Tailwind v4 `@theme` only defined `--color-primary-50` through `--color-primary-900`. The root variable `--color-primary` was missing.
  - Because `--color-primary` was not defined, classes like `bg-primary` produced no background color, leaving `text-white` on a transparent/white background.
- **Solution**:
  - Defined `--color-primary: #c2185b;`, `--color-gold: #d4a017;`, and `--color-teal: #2bb3a3;` in `@theme`.
  - Added explicit utility classes (`.bg-primary`, `.text-primary`, `.border-primary`, `.hover\:bg-primary-700`, `.focus\:ring-primary`) in `@layer utilities`.
  - Re-compiled production assets with `npm run build`. All primary buttons now display with vibrant Bombay Festive Crimson (`#C2185B`) and hover shade (`#880E4F`).

---

### C. Admin Route Aliases & 24-Route Complete Audit
- **User Issue**: Visiting `/admin/orders`, `/admin/orders/{id}`, `/admin/products`, and `/admin/users` threw `RouteNotFoundException` (500 Internal Server Error) for camelCase route names.
- **Routes Repaired**:
  - `admin.orders.packingSlip` (aliased to `admin.orders.packing-slip`)
  - `admin.orders.updateStatus` (aliased to `admin.orders.status` with `PUT` / `PATCH`)
  - `admin.orders.markCodPaid` (aliased to `admin.orders.mark-cod-paid` with `POST` / `PATCH`)
  - `admin.orders.updateReturn` (aliased to `admin.orders.return` with `POST` / `PUT` / `PATCH`)
  - `admin.products.toggleActive` (aliased to `admin.products.toggle-active` with `POST` / `PATCH`)
  - `admin.products.toggleFeatured` (aliased to `admin.products.toggle-featured` with `POST` / `PATCH`)
  - `admin.products.setPrimaryImage` (aliased to `admin.products.images.primary` with `POST` / `PATCH`)
  - `admin.products.deleteImage` (aliased to `admin.products.images.delete` with `POST` / `DELETE`)
  - `admin.users.toggleActive` (aliased to `admin.users.toggle-active` with `POST` / `PATCH`)
- **Verification**: All 24 core admin routes verified with HTTP 200 OK. All 26 PHPUnit/Pest test suites passing (80 assertions).

---

### D. Staff Profiles & Showroom Roles Expansion
- Added comprehensive staff profiling to `users` table via migration `2026_10_25_172227_add_profile_and_staff_fields_to_users_table.php`:
  - `avatar`: Photo upload support stored in `storage/app/public/avatars` with fallback to dynamic monogram avatars (`getAvatarUrlAttribute()`).
  - `employee_id`: Unique staff identifier (e.g., `BF-STF-01`).
  - `designation`: Store roles (Senior Saree Consultant, Master Tailor, Billing & POS Cashier, etc.).
  - `department`: Section allocation (Bridal Sarees, Punjabi Suits, School Uniforms, Inventory, Cash Counter).
  - `date_of_joining`: Employment tenure tracking.
  - `address`: Amritsar local residential address.
  - `emergency_contact_name` & `emergency_contact_phone`: Safety records.
  - `salary`: Confidential monthly compensation figure.
  - `notes`: Store owner's private audit remarks.
- Upgraded staff CRUD views (`admin/staff/create.blade.php`, `admin/staff/edit.blade.php`, `admin/staff/index.blade.php`) with real-time avatar previews and festive badge styling.

---

### E. Dynamic 20-Second Rolling QR Staff Attendance System
- **Real-Time Dynamic Security**:
  - The counter kiosk (`/admin/attendance/kiosk`) generates a dynamic rotating token signed with `HMAC-SHA256` that rotates every **20 seconds**.
  - Payload encodes: `admin_email`, `current_date`, `current_time`, `year`, and a `time_window` index.
  - A photo or screenshot of the QR code shared via WhatsApp/Bluetooth becomes completely invalid after 20 seconds, preventing proxy attendance from home.
- **1-Mobile-1-Staff Security Enforcement ("ek mobile se ek hi scan ho")**:
  - Each mobile punch binds the device via cookie and local fingerprint (`bf_attendance_device_token`).
  - The system checks if the device has already punched today for another staff member. If detected, proxy punch is blocked:
    `⚠️ Device Restriction: This mobile device has already recorded attendance today for another staff member. Proxy attendance is prohibited.`
  - Double punch-in prevention (minimum 2 minutes interval before Punch Out is permitted).
- **Showroom Rules & Cutoffs**:
  - Showroom opens at 10:00 AM. Scans before 10:30 AM are marked `present` (On Time). Scans after 10:30 AM are automatically flagged as `late` arrival.
  - Shift duration (`total_hours`) is automatically calculated upon Punch Out.
- **Kiosk Counter Display Customization**:
  - **Hide/Show Punches Feed**: Admin can click `👁️ Hide Punches` anytime to hide the live check-ins feed for privacy or counter cleanliness.
  - **Dynamic QR Code Resizing**: Admin can resize the QR code with one tap:
    - **Normal** (280px)
    - **Large** (380px)
    - **Giant Counter Mode** (480px, automatically centers in fullscreen so staff can scan from 1-2 meters away).

---

### F. Admin Responsive Sticky Sidebar & Universal Toggle Button
- **Fixed Overlap & Sticking Behavior**:
  - Re-architected desktop layout to use natural flex flow with `w-64 shrink-0 sticky top-0 h-screen`.
  - The sidebar no longer overlaps the page content or page titles on desktop.
  - The sidebar remains permanently pinned to the top of the browser viewport during page scrolling, so navigation links never scroll off-screen.
- **Universal Toggle Button**:
  - Added a responsive toggle button in the header (`admin-header.blade.php`) with live status ("Hide Menu" / "Show Menu").
  - On desktop: Collapses the sidebar to full-width screen mode with one click.
  - On mobile: Triggers a smooth off-canvas drawer with a blurred backdrop overlay and close button.





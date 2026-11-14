<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\BannerController as AdminBannerController;

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ContactMessageController as AdminContactMessageController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\InventoryController as AdminInventoryController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Admin\PosController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;

use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\ReviewModerationController as AdminReviewModerationController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\StaffAdvanceController;
use App\Http\Controllers\Admin\StaffController as AdminStaffController;
use App\Http\Controllers\Admin\StaffLeaveController;
use App\Http\Controllers\Admin\StaffProfileController;
use App\Http\Controllers\Admin\StaffSalaryController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\VariantController as AdminVariantController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\CustomerProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SchoolUniformController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Storefront Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/search/live', [ProductController::class, 'liveSearch'])->name('search.live');
Route::get('/school-uniforms', [SchoolUniformController::class, 'index'])->name('uniforms.index');
Route::get('/track-order', [CustomerOrderController::class, 'track'])->name('orders.track');
Route::get('/pages/{slug}', [PageController::class, 'show'])->name('pages.show');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'submitContact'])->name('contact.submit');
Route::get('/attendance/scan', [AttendanceController::class, 'scan'])->name('attendance.scan');
Route::post('/attendance/punch', [AttendanceController::class, 'submitPunch'])->name('attendance.submit-punch');

/*
|--------------------------------------------------------------------------
| Cart Routes (Session + User)
|--------------------------------------------------------------------------
*/
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::post('/update', [CartController::class, 'updateByPost'])->name('update');
    Route::patch('/{id}', [CartController::class, 'update'])->name('update.id');
    Route::post('/remove', [CartController::class, 'removeByPost'])->name('remove');
    Route::delete('/{id}', [CartController::class, 'remove'])->name('remove.id');
    Route::post('/coupon', [CartController::class, 'applyCoupon'])->name('coupon.apply');
    Route::post('/coupon/remove', [CartController::class, 'removeCoupon'])->name('coupon.remove');
    Route::delete('/coupon', [CartController::class, 'removeCoupon'])->name('coupon.delete');
});

/*
|--------------------------------------------------------------------------
| Wishlist Routes
|--------------------------------------------------------------------------
*/
Route::prefix('wishlist')->name('wishlist.')->group(function () {
    Route::get('/', [WishlistController::class, 'index'])->name('index')->middleware('auth');
    Route::post('/toggle/{productId?}', [WishlistController::class, 'toggle'])->name('toggle');
});

/*
|--------------------------------------------------------------------------
| Checkout & Payment Routes
|--------------------------------------------------------------------------
*/
Route::prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/', [CheckoutController::class, 'process'])->name('process');
    Route::get('/razorpay/{orderNumber}', [CheckoutController::class, 'razorpayCheckout'])->name('razorpay');
    Route::post('/verify', [CheckoutController::class, 'verifyPayment'])->name('verify');
    Route::post('/razorpay/verify', [CheckoutController::class, 'verifyPayment'])->name('razorpay.verify');
    Route::get('/success/{orderNumber}', [CheckoutController::class, 'success'])->name('success');
});
Route::post('/webhook/razorpay', [CheckoutController::class, 'webhook'])->name('checkout.webhook');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.post');

    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])->name('password.update');
});
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Customer Account Routes (Protected)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    // Orders
    Route::get('/orders', [CustomerOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{orderNumber}', [CustomerOrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{orderNumber}/cancel', [CustomerOrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/orders/{orderNumber}/return', [CustomerOrderController::class, 'requestReturn'])->name('orders.return');
    Route::get('/orders/{orderNumber}/invoice', [CustomerOrderController::class, 'downloadInvoice'])->name('orders.invoice');

    // Customer Reviews
    Route::post('/products/{productId}/review', [ReviewController::class, 'store'])->name('reviews.store');

    // Profile & Address Book
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [CustomerProfileController::class, 'profile'])->name('index');
        Route::put('/', [CustomerProfileController::class, 'updateProfile'])->name('update');
        Route::put('/password', [CustomerProfileController::class, 'updatePassword'])->name('password');
        Route::get('/addresses', [CustomerProfileController::class, 'addresses'])->name('addresses');
        Route::post('/addresses', [CustomerProfileController::class, 'storeAddress'])->name('addresses.store');
        Route::delete('/addresses/{id}', [CustomerProfileController::class, 'deleteAddress'])->name('addresses.destroy');
        Route::patch('/addresses/{id}/default', [CustomerProfileController::class, 'setDefaultAddress'])->name('addresses.default');
    });
});

// Staff Attendance Dynamic QR Scanning (Mobile Landing & Auto-Punch)
Route::get('/attendance/scan', [AttendanceController::class, 'scan'])->name('attendance.scan');
Route::post('/attendance/submit-punch', [AttendanceController::class, 'submitPunch'])->name('attendance.submit-punch');

/*
|--------------------------------------------------------------------------
| Admin & Staff Panel Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin,staff'])->group(function () {
    // Shared Dashboard & Operations Quick Launchpad Hub
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('hub', [AdminDashboardController::class, 'hub'])->name('hub');

    // Staff & Admin Self-Service: Profile, Shift Info, Attendance, Salary & Personal Purchases
    Route::get('profile', [StaffProfileController::class, 'show'])->name('profile.show');
    Route::put('profile', [StaffProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [StaffProfileController::class, 'updatePassword'])->name('profile.password');
    Route::get('profile/salary', [StaffProfileController::class, 'salary'])->name('profile.salary');
    Route::get('profile/purchases', [StaffProfileController::class, 'purchases'])->name('profile.purchases');
    Route::get('mark-attendance', [AttendanceController::class, 'myAttendance'])->name('attendance.mark');

    // Self-Service Payslip View (Staff can view own, Admin can view all)
    Route::get('staff-salaries/{salary}/payslip', [StaffSalaryController::class, 'payslip'])->name('staff.salaries.payslip');

    // Viewable Thermal POS Receipt & Order Invoice (Authorized for Admin, Managers & Purchaser)
    Route::get('pos/receipt/{order}', [PosController::class, 'receipt'])->name('pos.receipt');
    Route::get('orders/{order}/invoice', [AdminOrderController::class, 'printInvoice'])->name('orders.invoice');

    // Products Management
    Route::middleware('permission:products.manage')->group(function () {
        Route::resource('products', AdminProductController::class);
        Route::match(['POST', 'DELETE'], 'products/images/{imageId}', [AdminProductController::class, 'deleteImage'])->name('products.images.delete');
        Route::match(['POST', 'DELETE'], 'products/images/{imageId}/delete', [AdminProductController::class, 'deleteImage'])->name('products.deleteImage');
        Route::match(['POST', 'PATCH'], 'products/images/{imageId}/primary', [AdminProductController::class, 'setPrimaryImage'])->name('products.images.primary');
        Route::match(['POST', 'PATCH'], 'products/images/{imageId}/set-primary', [AdminProductController::class, 'setPrimaryImage'])->name('products.setPrimaryImage');
        Route::match(['POST', 'PATCH'], 'products/{product}/toggle-active', [AdminProductController::class, 'toggleActive'])->name('products.toggle-active');
        Route::match(['POST', 'PATCH'], 'products/{product}/toggleActive', [AdminProductController::class, 'toggleActive'])->name('products.toggleActive');
        Route::match(['POST', 'PATCH'], 'products/{product}/toggle-featured', [AdminProductController::class, 'toggleFeatured'])->name('products.toggle-featured');
        Route::match(['POST', 'PATCH'], 'products/{product}/toggleFeatured', [AdminProductController::class, 'toggleFeatured'])->name('products.toggleFeatured');

        // Product Variants
        Route::post('products/{product}/variants', [AdminVariantController::class, 'store'])->name('variants.store');
        Route::put('variants/{variant}', [AdminVariantController::class, 'update'])->name('variants.update');
        Route::delete('variants/{variant}', [AdminVariantController::class, 'destroy'])->name('variants.destroy');
    });

    // Orders Management
    Route::middleware('permission:orders.manage')->group(function () {
        Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::match(['PUT', 'PATCH'], 'orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
        Route::match(['PUT', 'PATCH'], 'orders/{order}/update-status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');
        Route::match(['POST', 'PATCH'], 'orders/{order}/mark-cod-paid', [AdminOrderController::class, 'markCodPaid'])->name('orders.mark-cod-paid');
        Route::match(['POST', 'PATCH'], 'orders/{order}/markCodPaid', [AdminOrderController::class, 'markCodPaid'])->name('orders.markCodPaid');
        Route::match(['POST', 'PUT', 'PATCH'], 'orders/{order}/return', [AdminOrderController::class, 'updateReturn'])->name('orders.return');
        Route::match(['POST', 'PUT', 'PATCH'], 'orders/{order}/updateReturn', [AdminOrderController::class, 'updateReturn'])->name('orders.updateReturn');
        Route::get('orders/{order}/packing-slip', [AdminOrderController::class, 'printPackingSlip'])->name('orders.packing-slip');
        Route::get('orders/{order}/packing-slip-pdf', [AdminOrderController::class, 'printPackingSlip'])->name('orders.packingSlip');
    });

    // Showroom Walk-in Counter POS Billing
    Route::middleware('permission:pos.access')->group(function () {
        Route::get('pos', [PosController::class, 'index'])->name('pos.index');
        Route::post('pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');
        Route::get('pos/history', [PosController::class, 'history'])->name('pos.history');
    });

    // Inventory, Stock Logs, Reorder Alerts & Dead Stock
    Route::middleware('permission:inventory.manage')->group(function () {
        Route::get('inventory', [AdminInventoryController::class, 'index'])->name('inventory.index');
        Route::post('inventory/adjust', [AdminInventoryController::class, 'adjust'])->name('inventory.adjust');
        Route::get('inventory/logs', [AdminInventoryController::class, 'logs'])->name('inventory.logs');
        Route::get('inventory/reorder', [AdminInventoryController::class, 'reorderAlerts'])->name('inventory.reorder');
        Route::get('inventory/dead-stock', [AdminInventoryController::class, 'deadStock'])->name('inventory.dead-stock');
    });

    // Review Moderation
    Route::middleware('permission:reviews.moderate')->group(function () {
        Route::get('reviews', [AdminReviewModerationController::class, 'index'])->name('reviews.index');
        Route::patch('reviews/{review}/approve', [AdminReviewModerationController::class, 'approve'])->name('reviews.approve');
        Route::delete('reviews/{review}', [AdminReviewModerationController::class, 'destroy'])->name('reviews.destroy');
    });

    // Contact Messages
    Route::middleware('permission:messages.manage')->group(function () {
        Route::get('messages', [AdminContactMessageController::class, 'index'])->name('messages.index');
        Route::get('messages/{message}', [AdminContactMessageController::class, 'show'])->name('messages.show');
        Route::delete('messages/{message}', [AdminContactMessageController::class, 'destroy'])->name('messages.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Admin Only Routes (Restricted from Staff)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin'])->group(function () {
        // Showroom Staff Attendance & 20s Dynamic Rolling QR (Strictly Admin Only)
        Route::get('attendance', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::get('attendance/kiosk', [AttendanceController::class, 'kiosk'])->name('attendance.kiosk');
        Route::get('attendance/live-token', [AttendanceController::class, 'getLiveToken'])->name('attendance.live-token');
        Route::post('attendance/manual', [AttendanceController::class, 'updateManual'])->name('attendance.manual');

        Route::resource('categories', AdminCategoryController::class);
        Route::resource('banners', AdminBannerController::class);
        Route::resource('coupons', AdminCouponController::class);
        Route::resource('users', AdminUserController::class)->only(['index', 'show']);
        Route::match(['POST', 'PATCH'], 'users/{user}/toggle-active', [AdminUserController::class, 'toggleActive'])->name('users.toggle-active');
        Route::match(['POST', 'PATCH'], 'users/{user}/toggleActive', [AdminUserController::class, 'toggleActive'])->name('users.toggleActive');

        Route::resource('staff', AdminStaffController::class);

        // Staff HR Management: Salary / Payroll, Leaves / Chutti, Advances / Peshgi
        Route::get('staff-salaries', [StaffSalaryController::class, 'index'])->name('staff.salaries.index');
        Route::post('staff-salaries/generate', [StaffSalaryController::class, 'generate'])->name('staff.salaries.generate');
        Route::patch('staff-salaries/{salary}/payment', [StaffSalaryController::class, 'updatePayment'])->name('staff.salaries.payment');

        Route::get('staff-leaves', [StaffLeaveController::class, 'index'])->name('staff.leaves.index');
        Route::post('staff-leaves', [StaffLeaveController::class, 'store'])->name('staff.leaves.store');
        Route::patch('staff-leaves/{leave}/status', [StaffLeaveController::class, 'updateStatus'])->name('staff.leaves.update-status');
        Route::delete('staff-leaves/{leave}', [StaffLeaveController::class, 'destroy'])->name('staff.leaves.destroy');

        Route::get('staff-advances', [StaffAdvanceController::class, 'index'])->name('staff.advances.index');
        Route::post('staff-advances', [StaffAdvanceController::class, 'store'])->name('staff.advances.store');
        Route::patch('staff-advances/{advance}/settle', [StaffAdvanceController::class, 'markSettled'])->name('staff.advances.settle');

        // Retail Garment & Cloth Sales Reports ("Kitna cloth bikha or kon sa bikha")
        Route::get('reports', [AdminReportController::class, 'index'])->name('reports.index');
        Route::get('reports/export', [AdminReportController::class, 'exportCsv'])->name('reports.export');
        Route::get('reports/cloth-sales', [AdminReportController::class, 'clothSales'])->name('reports.cloth-sales');
        Route::get('reports/export-cloth-sales', [AdminReportController::class, 'exportClothSalesCsv'])->name('reports.export-cloth-sales');
        Route::get('reports/categories', [AdminReportController::class, 'categorySales'])->name('reports.categories');
        Route::get('reports/fabrics', [AdminReportController::class, 'fabricSales'])->name('reports.fabrics');
        Route::get('reports/wishlist', [AdminReportController::class, 'wishlistInsights'])->name('reports.wishlist');
        Route::get('reports/abandoned-carts', [AdminReportController::class, 'abandonedCarts'])->name('reports.abandoned-carts');
        Route::get('reports/tax', [AdminReportController::class, 'taxReport'])->name('reports.tax');
        Route::get('reports/export-tax', [AdminReportController::class, 'exportTaxCsv'])->name('reports.export-tax');
        Route::get('reports/customers', [AdminReportController::class, 'customerInsights'])->name('reports.customers');

        // Store Settings & CMS Pages
        Route::get('settings', [AdminSettingController::class, 'index'])->name('settings.index');
        Route::put('settings', [AdminSettingController::class, 'update'])->name('settings.update');
        Route::resource('pages', AdminPageController::class)->only(['index', 'edit', 'update']);

        // Activity Logs
        Route::get('activity', [ActivityLogController::class, 'index'])->name('activity.index');
    });
});

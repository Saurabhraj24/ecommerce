<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'role_id',
        'name',
        'email',
        'phone',
        'avatar',
        'employee_id',
        'designation',
        'department',
        'date_of_joining',
        'address',
        'emergency_contact_name',
        'emergency_contact_phone',
        'salary',
        'bank_name',
        'bank_account_no',
        'bank_ifsc',
        'upi_id',
        'weekly_off',
        'shift_timings',
        'notes',
        'permissions',
        'password',
        'is_active',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'date_of_joining' => 'date',
            'salary' => 'decimal:2',
            'permissions' => 'array',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            if (str_starts_with($this->avatar, 'http://') || str_starts_with($this->avatar, 'https://')) {
                return $this->avatar;
            }
            return asset('storage/' . $this->avatar);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=c2185b&color=fff&size=128';
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function defaultAddress(): HasOne
    {
        return $this->hasOne(Address::class)->where('is_default', true);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class)->latest();
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaves(): HasMany
    {
        return $this->hasMany(StaffLeave::class)->latest('start_date');
    }

    public function salaries(): HasMany
    {
        return $this->hasMany(StaffSalary::class)->latest('month');
    }

    public function advances(): HasMany
    {
        return $this->hasMany(StaffAdvance::class)->latest('advance_date');
    }

    public function isAdmin(): bool
    {
        return $this->role?->name === Role::ADMIN;
    }

    public function isStaff(): bool
    {
        return $this->role?->name === Role::STAFF;
    }

    public function isCustomer(): bool
    {
        return $this->role?->name === Role::USER;
    }

    public function canAccessAdmin(): bool
    {
        return $this->is_active && ($this->isAdmin() || $this->isStaff());
    }

    /**
     * Definition of showroom staff operational permissions with descriptive metadata
     */
    public static function availablePermissions(): array
    {
        return [
            'pos.access' => [
                'label' => 'Showroom Walk-in POS Billing',
                'desc' => 'Generate counter bills, scan barcodes, take cash/card/UPI payments & print thermal receipts',
                'icon' => '🧾',
                'category' => 'Sales Counter',
            ],
            'orders.manage' => [
                'label' => 'Customer Orders & Returns',
                'desc' => 'View online and showroom orders, update status (dispatch/delivered), mark COD paid & handle returns',
                'icon' => '📦',
                'category' => 'Sales Counter',
            ],
            'products.manage' => [
                'label' => 'Products & Catalog Management',
                'desc' => 'Add new sarees, lehengas & suits, edit fabric details, update prices and upload product images',
                'icon' => '👗',
                'category' => 'Catalog & Stock',
            ],
            'inventory.manage' => [
                'label' => 'Inventory & Stock Adjustments',
                'desc' => 'Update showroom stock counts, review stock audit logs, monitor reorders & dead stock',
                'icon' => '📊',
                'category' => 'Catalog & Stock',
            ],
            'attendance.manage' => [
                'label' => 'Staff Attendance & Kiosk Scan',
                'desc' => 'Scan 20s dynamic QR code to clock in/out and view daily attendance timeline',
                'icon' => '⏱️',
                'category' => 'Operations',
            ],
            'reviews.moderate' => [
                'label' => 'Customer Reviews Moderation',
                'desc' => 'Moderate, approve or reject customer feedback and saree photos uploaded with reviews',
                'icon' => '⭐',
                'category' => 'Customer Desk',
            ],
            'messages.manage' => [
                'label' => 'Customer Inquiries & Messages',
                'desc' => 'Read and reply to showroom contact form inquiries and bridal consultation requests',
                'icon' => '✉️',
                'category' => 'Customer Desk',
            ],
        ];
    }

    /**
     * Check if user has specific permission. Admins automatically have all permissions.
     */
    public function hasPermission(string $permission): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->isAdmin()) {
            return true;
        }

        if (!$this->isStaff()) {
            return false;
        }

        // Staff only have permissions that were explicitly granted by the Admin
        $perms = (array)($this->permissions ?? []);
        return in_array($permission, $perms, true);
    }

    /**
     * Check if user has any of the given permissions
     */
    public function hasAnyPermission(array $permissions): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        foreach ($permissions as $perm) {
            if ($this->hasPermission($perm)) {
                return true;
            }
        }

        return false;
    }
}

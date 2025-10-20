<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'phone',
        'email',
        'address',
        'city',
        'country',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Get the users for the branch.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the appointments for the branch.
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Get the appointment recurrences for the branch.
     */
    public function appointmentRecurrences(): HasMany
    {
        return $this->hasMany(AppointmentRecurrence::class);
    }

    /**
     * Get the appointment groups for the branch.
     */
    public function appointmentGroups(): HasMany
    {
        return $this->hasMany(AppointmentGroup::class);
    }

    /**
     * Get the waitlist entries for the branch.
     */
    public function waitlistEntries(): HasMany
    {
        return $this->hasMany(AppointmentWaitlist::class);
    }

    /**
     * Get the appointment conflicts for the branch.
     */
    public function appointmentConflicts(): HasMany
    {
        return $this->hasMany(AppointmentConflict::class);
    }

    /**
     * Get the settings for the branch.
     */
    public function settings(): HasMany
    {
        return $this->hasMany(BranchSetting::class);
    }

    /**
     * Get the purchase orders for the branch.
     */
    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    /**
     * Get the stock alerts for the branch.
     */
    public function stockAlerts(): HasMany
    {
        return $this->hasMany(StockAlert::class);
    }

    /**
     * Get the stock audits for the branch.
     */
    public function stockAudits(): HasMany
    {
        return $this->hasMany(StockAudit::class);
    }

    /**
     * Get the product bundles for the branch.
     */
    public function productBundles(): HasMany
    {
        return $this->hasMany(ProductBundle::class);
    }

    /**
     * Get the stock transfers from this branch.
     */
    public function outgoingStockTransfers(): HasMany
    {
        return $this->hasMany(StockTransfer::class, 'from_branch_id');
    }

    /**
     * Get the stock transfers to this branch.
     */
    public function incomingStockTransfers(): HasMany
    {
        return $this->hasMany(StockTransfer::class, 'to_branch_id');
    }

    /**
     * Get a setting value by key
     */
    public function getSetting(string $key, $default = null)
    {
        return BranchSetting::get($this->id, $key, $default);
    }

    /**
     * Set a setting value
     */
    public function setSetting(string $key, $value, ?string $type = 'string', ?string $group = null, bool $isEncrypted = false)
    {
        return BranchSetting::set($this->id, $key, $value, $type, $group, $isEncrypted);
    }
}

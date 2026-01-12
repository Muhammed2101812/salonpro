<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\TwoFactorAuthenticatable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasRoles;
    use HasUuids;
    use Notifiable;
    use SoftDeletes;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'branch_id',
        'phone',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Get the branch that the user belongs to.
     */
    public function branch(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the purchase orders created by the user.
     */
    public function createdPurchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class, 'created_by');
    }

    /**
     * Get the purchase orders approved by the user.
     */
    public function approvedPurchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class, 'approved_by');
    }

    /**
     * Get the stock alerts acknowledged by the user.
     */
    public function acknowledgedStockAlerts(): HasMany
    {
        return $this->hasMany(StockAlert::class, 'acknowledged_by');
    }

    /**
     * Get the stock transfers requested by the user.
     */
    public function requestedStockTransfers(): HasMany
    {
        return $this->hasMany(StockTransfer::class, 'requested_by');
    }

    /**
     * Get the stock transfers approved by the user.
     */
    public function approvedStockTransfers(): HasMany
    {
        return $this->hasMany(StockTransfer::class, 'approved_by');
    }

    /**
     * Get the stock transfers received by the user.
     */
    public function receivedStockTransfers(): HasMany
    {
        return $this->hasMany(StockTransfer::class, 'received_by');
    }

    /**
     * Get the stock audits conducted by the user.
     */
    public function conductedStockAudits(): HasMany
    {
        return $this->hasMany(StockAudit::class, 'conducted_by');
    }

    /**
     * Get the stock audits approved by the user.
     */
    public function approvedStockAudits(): HasMany
    {
        return $this->hasMany(StockAudit::class, 'approved_by');
    }

    /**
     * Get the appointment histories created by the user.
     */
    public function appointmentHistories(): HasMany
    {
        return $this->hasMany(AppointmentHistory::class);
    }

    /**
     * Get the appointment conflicts resolved by the user.
     */
    public function resolvedConflicts(): HasMany
    {
        return $this->hasMany(AppointmentConflict::class, 'resolved_by');
    }

    /**
     * Get the appointment cancellations made by the user.
     */
    public function appointmentCancellations(): HasMany
    {
        return $this->hasMany(AppointmentCancellation::class, 'cancelled_by');
    }

    /**
     * Get the push notification tokens for the user.
     */
    public function pushNotificationTokens(): HasMany
    {
        return $this->hasMany(PushNotificationToken::class);
    }
}

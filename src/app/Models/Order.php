<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Order model representing customer orders.
 *
 * @property int $id
 * @property int $customer_id
 * @property string $status
 * @property float $total_amount
 * @property \Illuminate\Support\Carbon|null $confirmed_at
 * @property \Illuminate\Support\Carbon|null $shipped_at
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property mixed $customer
 * @property mixed $items
 */
class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'status',
        'total_amount',
        'confirmed_at',
        'shipped_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'confirmed_at' => 'datetime',
        'shipped_at' => 'datetime',
    ];

    /**
     * Get the customer that owns the order.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get order items.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get order export record.
     */
    public function export(): HasOne
    {
        return $this->hasOne(OrderExport::class);
    }

    public function canTransitionTo(string $newStatus): bool
    {
        $allowedTransitions = OrderStatus::STATUS_TRANSITIONS[$this->status] ?? [];
        return in_array($newStatus, $allowedTransitions, true);
    }

    /**
     * Boot method to handle model events.
     */
    protected static function booted(): void
    {
        static::updating(function (Order $order) {
            if ($order->isDirty('status')) {
                if ($order->status === OrderStatus::CONFIRMED->value && !$order->confirmed_at) {
                    $order->confirmed_at = now();
                }
                if ($order->status === OrderStatus::SHIPPED->value && !$order->shipped_at) {
                    $order->shipped_at = now();
                }
            }
        });
    }
}

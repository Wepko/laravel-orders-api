<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * OrderItem model representing items within an order.
 *
 * @property int $id
 * @property int $order_id
 * @property int $product_id
 * @property int $quantity
 * @property float $unit_price
 * @property float $total_price
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property mixed $product
 */
class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'unit_price',
        'total_price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    /**
     * Get the order this item belongs to.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the product for this item.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Calculate total price based on quantity and unit price.
     */
    public function calculateTotal(): float
    {
        return round($this->quantity * $this->unit_price, 2);
    }

    /**
     * Boot method to calculate total price on creating.
     */
    protected static function booted(): void
    {
        static::creating(function (OrderItem $item) {
            $item->total_price = $item->calculateTotal();
        });

        static::updating(function (OrderItem $item) {
            if ($item->isDirty('quantity') || $item->isDirty('unit_price')) {
                $item->total_price = $item->calculateTotal();
            }
        });
    }
}

<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Product model representing catalog items in the parts store.
 *
 * @property int $id
 * @property string $name
 * @property string $sku
 * @property float $price
 * @property int $stock_quantity
 * @property string $category
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'price',
        'stock_quantity',
        'category',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock_quantity' => 'integer',
    ];

    /**
     * Get order items for this product.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Scope for filtering by category.
     */
    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for searching by name or SKU.
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'ilike', "%{$search}%")
              ->orWhere('sku', 'ilike', "%{$search}%");
        });
    }

    /**
     * Check if product has sufficient stock.
     */
    public function hasStock(int $quantity): bool
    {
        return $this->stock_quantity >= $quantity;
    }

    /**
     * Reduce stock quantity atomically.
     */
    public function reduceStock(int $quantity): bool
    {
        if (!$this->hasStock($quantity)) {
            return false;
        }

        $this->decrement('stock_quantity', $quantity);
        return true;
    }

    /**
     * Increase stock quantity.
     */
    public function increaseStock(int $quantity): void
    {
        $this->increment('stock_quantity', $quantity);
    }
}

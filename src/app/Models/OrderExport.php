<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * OrderExport model for tracking external export status.
 *
 * @property int $id
 * @property int $order_id
 * @property string $status
 * @property string|null $response
 * @property int $attempts
 * @property \Illuminate\Support\Carbon|null $processed_at
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class OrderExport extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'order_id',
        'status',
        'response',
        'attempts',
        'processed_at',
    ];

    protected $casts = [
        'attempts' => 'integer',
        'processed_at' => 'datetime',
    ];

    /**
     * Get the order this export belongs to.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Mark export as processing.
     */
    public function markProcessing(): void
    {
        $this->update([
            'status' => self::STATUS_PROCESSING,
            'attempts' => $this->attempts + 1,
        ]);
    }

    /**
     * Mark export as completed.
     */
    public function markCompleted(string $response): void
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'response' => $response,
            'processed_at' => now(),
        ]);
    }

    /**
     * Mark export as failed.
     */
    public function markFailed(string $response): void
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'response' => $response,
        ]);
    }
}

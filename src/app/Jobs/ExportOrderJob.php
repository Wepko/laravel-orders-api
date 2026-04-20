<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Order;
use App\Models\OrderExport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\RequestException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * Job for exporting order to external system.
 */
class ExportOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Number of retry attempts.
     */
    public int $tries = 3;

    /**
     * Backoff time in seconds.
     */
    public array $backoff = [10, 30, 60];

    /**
     * Maximum execution time in seconds.
     */
    public int $timeout = 60;

    public function __construct(
        public readonly Order $order
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Create or update export record
        $export = OrderExport::updateOrCreate(
            ['order_id' => $this->order->id],
            ['status' => OrderExport::STATUS_PROCESSING, 'attempts' => 0]
        );

        $export->markProcessing();

        try {
            $apiUrl = config('services.export_api.url');

            $response = Http::timeout(30)->post($apiUrl, [
                'order_id' => $this->order->id,
                'customer_id' => $this->order->customer_id,
                'total_amount' => $this->order->total_amount,
                'status' => $this->order->status,
                'items' => $this->order->items->map(fn($item) => [
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total_price' => $item->total_price,
                ])->toArray(),
                'exported_at' => now()->toIso8601String(),
            ]);

            if ($response->successful()) {
                $export->markCompleted($response->body());
            } else {
                throw new RequestException($response);
            }
        } catch (Throwable $e) {
            $export->markFailed($e->getMessage());

            if ($this->attempts() < $this->tries) {
                throw $e;
            }
        }
    }

    /**
     * Handle job failure.
     */
    public function failed(?Throwable $exception): void
    {
        $export = OrderExport::where('order_id', $this->order->id)->first();

        if ($export) {
            $export->markFailed($exception?->getMessage() ?? 'Unknown error');
        }
    }
}
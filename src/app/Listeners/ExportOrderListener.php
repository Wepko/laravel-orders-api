<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\OrderConfirmed;
use App\Jobs\ExportOrderJob;

/**
 * Listener that dispatches export job when order is confirmed.
 */
class ExportOrderListener
{
    /**
     * Handle the event.
     */
    public function handle(OrderConfirmed $event): void
    {
        ExportOrderJob::dispatch($event->order);
    }
}

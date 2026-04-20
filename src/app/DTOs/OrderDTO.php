<?php

declare(strict_types=1);

namespace App\DTOs;

use Carbon\Carbon;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

/**
 * DTOs Transfer Object for Order responses.
 */
class OrderDTO extends Data
{
    public function __construct(
        public readonly int            $id,
        public readonly CustomerDTO    $customer,
        public readonly string         $status,
        public readonly float          $total_amount,
        #[DataCollectionOf(OrderItemDTO::class)]
        public readonly DataCollection $items,
        public readonly ?Carbon        $confirmed_at,
        public readonly ?Carbon        $shipped_at,
        public readonly Carbon         $created_at,
        public readonly Carbon         $updated_at,
    )
    {
    }
}
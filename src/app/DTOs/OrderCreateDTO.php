<?php

declare(strict_types=1);

namespace App\DTOs;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

/**
 * DTO Transfer Object for Order Creation.
 */
class OrderCreateDTO extends Data
{
    public function __construct(
        public readonly int $customer_id,
        #[DataCollectionOf(OrderItemDTO::class)]
        public readonly DataCollection $items,
    ) {}
}
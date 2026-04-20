<?php

declare(strict_types=1);

namespace App\DTOs;

use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;

/**
 * DTOs Transfer Object for Order Item input and responses.
 */
class OrderItemDTO extends Data
{
    public function __construct(
        public readonly int         $product_id,
        public readonly int         $quantity,
        public readonly ?int        $id = null,
        public readonly ?ProductDTO $product = null,
        public readonly ?float      $unit_price = null,
        public readonly ?float      $total_price = null,
    ) {}

}
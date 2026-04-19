<?php

declare(strict_types=1);

namespace App\DTOs;

use Spatie\LaravelData\Data;
use Carbon\Carbon;

/**
 * DTOs Transfer Object for Product responses.
 */
class ProductDTO extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $sku,
        public readonly float $price,
        public readonly int $stock_quantity,
        public readonly string $category,
        public readonly ?Carbon $created_at,
        public readonly ?Carbon $updated_at,
    ) {}
}

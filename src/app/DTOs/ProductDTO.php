<?php

declare(strict_types=1);

namespace App\DTOs;

use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Data;
use Carbon\Carbon;
use Spatie\LaravelData\Transformers\DateTimeInterfaceTransformer;

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
        #[MapOutputName('stock_quantity')]
        public readonly int $stockQuantity,
        public readonly string $category,
        #[WithTransformer(DateTimeInterfaceTransformer::class)]
        #[MapOutputName('created_at')]
        public readonly ?Carbon $createdAt,
        #[WithTransformer(DateTimeInterfaceTransformer::class)]
        #[MapOutputName('updated_at')]
        public readonly ?Carbon $updatedAt,
    ) {}
}

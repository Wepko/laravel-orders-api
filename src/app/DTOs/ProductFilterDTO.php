<?php

declare(strict_types=1);

namespace App\DTOs;

use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Data;

class ProductFilterDTO extends Data
{
    public function __construct(
        public readonly ?string $category = null,
        public readonly ?string $search = null,
        public readonly ?string $sort = null,
        public readonly int $perPage = 15,
    ) {}

}

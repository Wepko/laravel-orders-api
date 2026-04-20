<?php

declare(strict_types=1);

namespace App\DTOs;

use Spatie\LaravelData\Data;

class OrderUpdateStatusDTO extends Data
{
    public function __construct(
        public readonly string $status,
    ) {}

}
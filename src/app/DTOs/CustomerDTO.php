<?php

declare(strict_types=1);

namespace App\DTOs;

use Spatie\LaravelData\Data;

/**
 * DTOs Transfer Object for Customer responses.
 */
class CustomerDTO extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $phone,
    ) {}
}
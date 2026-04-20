<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Enums\OrderStatus;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;

class OrderUpdateStatusDTO extends Data
{
    public function __construct(
        public readonly string $status,
    ) {}

    /**
     * Define validation rules dynamically.
     */
    public static function rules(): array
    {
        return [
            'status' => ['required', 'string', Rule::in(OrderStatus::values())],
        ];
    }

}
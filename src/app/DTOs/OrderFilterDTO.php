<?php

declare(strict_types=1);

namespace App\DTOs;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\MapInputName;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'OrderFilterDTO',
    title: 'Фильтр заказов',
    description: 'Параметры фильтрации, поиска и курсорной пагинации заказов'
)]
class OrderFilterDTO extends Data
{
    public function __construct(
        #[OA\Property(
            description: 'Статус заказа',
            type: 'string',
            example: 'pending',
            nullable: true
        )]
        public readonly ?string $status = null,

        #[OA\Property(
            description: 'ID клиента',
            type: 'integer',
            example: 1,
            nullable: true
        )]
        #[MapInputName('customer_id')]
        public readonly ?int $customerId = null,

        #[OA\Property(
            description: 'Дата заказа от (YYYY-MM-DD)',
            type: 'string',
            format: 'date',
            example: '2024-01-01',
            nullable: true
        )]
        #[MapInputName('date_from')]
        public readonly ?string $dateFrom = null,

        #[OA\Property(
            description: 'Дата заказа до (YYYY-MM-DD)',
            type: 'string',
            format: 'date',
            example: '2024-12-31',
            nullable: true
        )]
        #[MapInputName('date_to')]
        public readonly ?string $dateTo = null,

        #[OA\Property(
            description: 'Количество заказов на странице',
            type: 'integer',
            example: 15,
            default: 15,
            maximum: 100,
            minimum: 1
        )]
        #[MapInputName('limit')]
        public readonly int $limit = 15,

        #[OA\Property(
            description: 'Курсор для пагинации (значение поля, с которого начать следующий запрос)',
            type: 'string',
            example: 'eyJpZCI6MTAsIl9wb2ludHMiOlsiMTBfeWF6eGlsb2ZmdW5rIl19',
            nullable: true
        )]
        public readonly ?string $cursor = null,

    ) {
    }
}
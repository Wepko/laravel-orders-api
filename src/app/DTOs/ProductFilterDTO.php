<?php

declare(strict_types=1);

namespace App\DTOs;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\MapInputName;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ProductFilterDTO',
    title: 'Фильтр товаров',
    description: 'Параметры фильтрации, поиска и курсорной пагинации товаров'
)]
class ProductFilterDTO extends Data
{
    public function __construct(
        #[OA\Property(
            description: 'Поисковый запрос по названию или SKU товара',
            type: 'string',
            nullable: true
        )]
        public readonly  ?string $q = null,

        #[OA\Property(
            description: 'ID категории',
            type: 'integer',
            example: 1,
            nullable: true
        )]
        public readonly ?string  $category = null,

        #[OA\Property(
            description: 'Количество товаров на странице',
            type: 'integer',
            example: 15,
            default: 15,
            maximum: 100,
            minimum: 1
        )]
        #[MapInputName('limit')]
        public readonly  int $limit = 15,

        #[OA\Property(
            description: 'Курсор для пагинации (значение поля, с которого начать следующий запрос)',
            type: 'string',
            example: 'eyJpZCI6MTAsIl9wb2ludHMiOlsiMTBfeWF6eGlsb2ZmdW5rIl19',
            nullable: true
        )]
        public readonly ?string $cursor = null,
        public readonly ?int $page = null,
    ) {
    }
}
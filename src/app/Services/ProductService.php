<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\ProductFilterDTO;
use App\Services\Product\ProductPaginationService;
use Illuminate\Contracts\Pagination\CursorPaginator;

class ProductService
{
    public function __construct(
        protected ProductPaginationService $productPaginationService
    ) {}

    public function getPaginationProducts(ProductFilterDTO $filter): CursorPaginator
    {
        return $this->productPaginationService->cursorPaginate(filter: $filter);
    }

}

<?php

declare(strict_types=1);

namespace App\Services\Product;

use App\DTOs\ProductFilterDTO;
use App\Filters\ProductFilter;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;

class ProductPaginationService
{
    public function __construct(
        protected ProductFilter $filter,
    ) {}

    public function cursorPaginate(ProductFilterDTO $filter): CursorPaginator
    {
        $query = $this->buildQuery($filter);

        return $query->cursorPaginate(
            $filter->limit,
            ['*'],
            'cursor',
            $filter->cursor // Параметр cursor из DTO
        );
    }

    /**
     * @param ProductFilterDTO $filter
     * @return Paginator
     */
    public function simplePaginate(ProductFilterDTO $filter): Paginator
    {
        $query = $this->buildQuery($filter);

        return $query->simplePaginate(
            $filter->limit,
            ['*'],
            'page',
            $filter->page
        );
    }

    private function buildQuery(ProductFilterDTO $filters): Builder
    {
        $query = Product::query();

        $this->filter->apply($query, [
            'category' => $filters->category,
            'q' => $filters->q,
        ]);

        return $query;
    }

}

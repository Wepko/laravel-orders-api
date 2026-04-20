<?php

declare(strict_types=1);

namespace App\Services\Order;

use App\DTOs\OrderFilterDTO;
use App\Filters\OrderFilter;
use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;

class OrderPaginationService
{
    public function __construct(
        protected OrderFilter $filter,
    ) {}

    /**
     * Курсорная пагинация (для больших наборов данных)
     */
    public function cursorPaginate(OrderFilterDTO $filter): CursorPaginator
    {
        $query = $this->buildQuery($filter);

        return $query->cursorPaginate(
            $filter->limit,
            ['*'],
            'cursor',
            $filter->cursor
        );
    }

    /**
     * Обычная пагинация со страницами (для небольших наборов данных)
     */
    public function simplePaginate(OrderFilterDTO $filter, int $page = 1): Paginator
    {
        $query = $this->buildQuery($filter);

        return $query->simplePaginate(
            $filter->limit,
            ['*'],
            'page',
            $page
        );
    }

    /**
     * Построение запроса с применением фильтров и сортировки
     */
    private function buildQuery(OrderFilterDTO $filters): Builder
    {
        $query = Order::query()->with(['items.product', 'customer']);

        $this->filter->apply($query, [
            'status' => $filters->status,
            'customer_id' => $filters->customerId,
            'date_from' => $filters->dateFrom,
            'date_to' => $filters->dateTo,
        ]);

        return $query;
    }

}
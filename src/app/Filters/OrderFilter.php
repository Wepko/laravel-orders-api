<?php

declare(strict_types=1);

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class OrderFilter
{
    public function apply(Builder $query, array $filters): Builder
    {
        foreach ($filters as $filter => $value) {
            if (method_exists($this, $filter) && !is_null($value) && $value !== '') {
                $this->$filter($query, $value);
            }
        }

        return $query;
    }

    /**
     * Фильтр по статусу заказа
     */
    protected function status(Builder $query, string $value): void
    {
        $query->where('status', $value);
    }

    /**
     * Фильтр по ID клиента
     */
    protected function customer_id(Builder $query, int $value): void
    {
        $query->where('customer_id', $value);
    }

    /**
     * Фильтр по дате от
     */
    protected function date_from(Builder $query, string $value): void
    {
        $query->whereDate('created_at', '>=', $value);
    }

    /**
     * Фильтр по дате до
     */
    protected function date_to(Builder $query, string $value): void
    {
        $query->whereDate('created_at', '<=', $value);
    }

}
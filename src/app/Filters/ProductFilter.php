<?php

declare(strict_types=1);

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class ProductFilter
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

    protected function category(Builder $query, string $value): void
    {
        $query->where('category', $value);
    }

    protected function q(Builder $query, string $value): void
    {
        $query->where(function (Builder $q) use ($value) {
            $q->where('name', 'LIKE', "%{$value}%")
                ->orWhere('sku', 'LIKE', "%{$value}%");
        });
    }
}
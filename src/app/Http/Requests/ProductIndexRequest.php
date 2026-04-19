<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\DTOs\ProductFilterDTO;
use Illuminate\Foundation\Http\FormRequest;

class ProductIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
            'filter' => ['nullable', 'array'],
            'filter.category' => ['nullable', 'string', 'max:255'],
            'filter.name' => ['nullable', 'string', 'max:255'],
            'filter.sku' => ['nullable', 'string', 'max:255'],
            'sort' => ['nullable', 'string', 'in:name,price,category,created_at,-name,-price,-category,-created_at'],
        ];
    }

    public function messages(): array
    {
        return [
            'per_page.max' => 'Максимум 100 товаров на странице',
            'sort.in' => 'Сортировка возможна по полям: name, price, category, created_at',
        ];
    }

    public function toDTO(): ProductFilterDTO
    {
        return new ProductFilterDTO(
            category: $this->input('filter.category'),
            search: $this->input('filter.name'),
            sort: $this->input('sort'),
            perPage: (int) $this->input('per_page', 15),
        );
    }
}
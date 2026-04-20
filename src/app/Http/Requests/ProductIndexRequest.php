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
            'q' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
            'cursor' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'limit.max' => 'Максимум 100 товаров на странице',
            'limit.min' => 'Минимум 1 товар на странице',
        ];
    }

    public function toDTO(): ProductFilterDTO
    {
        return new ProductFilterDTO(
            q: $this->input('q'),
            category: $this->input('category'),
            limit: (int) min($this->input('limit', 15), 100),
            cursor: $this->input('cursor'),
        );
    }
}
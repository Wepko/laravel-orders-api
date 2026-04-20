<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\DTOs\OrderFilterDTO;
use App\Enums\OrderStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['nullable', 'string', Rule::in(OrderStatus::values())],
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'date_from' => ['nullable', 'date', 'date_format:Y-m-d'],
            'date_to' => ['nullable', 'date', 'date_format:Y-m-d', 'after_or_equal:date_from'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
            'cursor' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.in' => 'Недопустимый статус заказа',
            'customer_id.exists' => 'Клиент не найден',
            'date_from.date_format' => 'Неверный формат даты. Используйте Y-m-d',
            'date_to.date_format' => 'Неверный формат даты. Используйте Y-m-d',
            'date_to.after_or_equal' => 'Дата "до" должна быть больше или равна дате "от"',
            'limit.max' => 'Максимум 100 заказов на странице',
            'limit.min' => 'Минимум 1 заказ на странице',
        ];
    }

    public function toDTO(): OrderFilterDTO
    {
        return new OrderFilterDTO(
            status: $this->input('status'),
            customerId: $this->input('customer_id') ? (int) $this->input('customer_id') : null,
            dateFrom: $this->input('date_from'),
            dateTo: $this->input('date_to'),
            limit: (int) min($this->input('limit', 15), 100),
            cursor: $this->input('cursor'),
        );
    }
}
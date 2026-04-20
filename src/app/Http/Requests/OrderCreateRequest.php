<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\DTOs\OrderCreateDTO;
use App\DTOs\OrderItemDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\DataCollection;

class OrderCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => [
                'required',
                'integer',
                Rule::exists('customers', 'id'),
            ],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => [
                'required',
                'integer',
                Rule::exists('products', 'id'),
            ],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_id.required' => 'Customer ID is required',
            'customer_id.exists' => 'Customer not found',
            'items.required' => 'At least one item is required',
            'items.min' => 'At least one item is required',
            'items.*.product_id.required' => 'Product ID is required for each item',
            'items.*.product_id.exists' => 'Product not found',
            'items.*.quantity.required' => 'Quantity is required for each item',
            'items.*.quantity.min' => 'Quantity must be at least 1',
            'items.*.quantity.max' => 'Quantity cannot exceed 1000',
        ];
    }

    public function toDto(): OrderCreateDTO
    {
        return new OrderCreateDTO(
            customer_id: $this->input('customer_id'),
            items: OrderItemDTO::collect($this->input('items'), DataCollection::class)
        );
    }
}

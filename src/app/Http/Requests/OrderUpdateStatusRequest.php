<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\DTOs\OrderUpdateStatusDTO;
use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use function Symfony\Component\String\s;

class OrderUpdateStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {

        return [
            'status' => [
                'required',
                'string',
                Rule::in(OrderStatus::values()),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Status is required',
            'status.in' => 'Invalid status value. Allowed: new, confirmed, processing, shipped, completed, cancelled',
        ];
    }

    public  function toDto(): OrderUpdateStatusDTO
    {
        return new OrderUpdateStatusDTO(
            status: $this->input('status')
        );
    }
}
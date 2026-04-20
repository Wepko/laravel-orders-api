<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\DTOs\CustomerDTO;
use App\DTOs\OrderDTO;
use App\DTOs\OrderItemDTO;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\LaravelData\DataCollection;

/**
 * @mixin \App\Models\Order
 */
class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Order $this */


        $dto = new OrderDTO(
            id: $this->id,
            customer: CustomerDTO::from($this->customer),
            status: $this->status,
            total_amount: (float) $this->total_amount,
            items: OrderItemDTO::collect($this->items, DataCollection::class),
            confirmed_at: $this->confirmed_at,
            shipped_at: $this->shipped_at,
            created_at: $this->created_at,
            updated_at: $this->updated_at,
        );

        return $dto->toArray();
    }
}
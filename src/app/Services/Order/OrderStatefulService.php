<?php

declare(strict_types=1);

namespace App\Services\Order;

use App\DTOs\OrderUpdateStatusDTO;
use App\Enums\OrderStatus;
use App\Events\OrderConfirmed;
use App\Exceptions\OrderNotFoundException;
use App\Exceptions\OrderStatusUpdateException;
use App\Models\Order;

class OrderStatefulService
{
    private Order $order;

    /**
     * @throws OrderNotFoundException
     */
    public function __construct(int $orderId)
    {
        /** @var Order|null $order */
        $order = Order::find($orderId);

        if (!$order) {
            throw new OrderNotFoundException($orderId);
        }

        $this->order = $order;
    }

    // Для детального просмотра - грузим все отношения
    public function getDetails(): Order
    {
        return $this->order->load(['items.product', 'customer']);
    }

    /**
     * @param OrderUpdateStatusDTO $data
     * @return Order
     */
    public function updateStatus(OrderUpdateStatusDTO $data): Order
    {
        $currentStatus = OrderStatus::from($this->order->status);
        $newStatus = OrderStatus::from($data->status);

        if (!$currentStatus->canTransitionTo($newStatus)) {
            throw new OrderStatusUpdateException(
                "Invalid status transition from '{$this->order->status}' to '{$data->status}'"
            );
        }

        $this->order->status = $data->status;
        $this->order->save();

        if ($currentStatus === OrderStatus::NEW && $newStatus === OrderStatus::CONFIRMED) {
            event(new OrderConfirmed($this->order));
        }

        $this->order->load(['items.product', 'customer']);

        return $this->order;
    }

}
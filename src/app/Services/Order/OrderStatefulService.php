<?php

declare(strict_types=1);

namespace App\Services\Order;

use App\Exceptions\OrderNotFoundException;
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


}
<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\OrderCreateDTO;
use App\DTOs\OrderFilterDTO;
//use App\DTOs\UpdateOrderStatusDTO;
use App\Exceptions\OrderNotFoundException;
use App\Models\Order;
use App\Services\Order\OrderCreationService;
use App\Services\Order\OrderPaginationService;
use App\Services\Order\OrderStatefulService;
use phpDocumentor\Reflection\Exception;

class OrderService
{


    public function __construct(
        protected OrderCreationService $orderCreationService,
        protected OrderPaginationService $orderPaginationService,
    )
    {}

    /**
     * @throws Exception
     */
    public function createOrder(OrderCreateDTO $data): Order
    {
        return $this->orderCreationService->create($data);
    }


    //
//    public function updateStatusFromData(int $orderId, UpdateOrderStatusDTO $data): Order
//    {
//        $order = Order::find($orderId);
//
//        if (!$order) {
//            throw new OrderNotFoundException($orderId);
//        }
//
//        if (!$order->canTransitionTo($data->status)) {
//            throw new OrderStatusUpdateException(
//                "Invalid status transition from '{$order->status}' to '{$data->status}'"
//            );
//        }
//
//        $previousStatus = $order->status;
//        $order->status = $data->status;
//        $order->save();
//
//        if ($data->status === Order::STATUS_CONFIRMED && $previousStatus === Order::STATUS_NEW) {
//            event(new OrderConfirmed($order));
//        }
//
//        $order->load(['items.product', 'customer']);
//
//        return $order;
//    }
//
    /**
     * @throws OrderNotFoundException
     */
    public function getOrderWithDetails(int $orderId): Order
    {
        return new orderStatefulService($orderId)->getDetails();
    }

    public function getPaginationOrder(OrderFilterDTO $filter): \Illuminate\Pagination\CursorPaginator
    {
        return $this->orderPaginationService->cursorPaginate($filter);
    }
}
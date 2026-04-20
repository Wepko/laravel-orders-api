<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\OrderCreateDTO;
use App\DTOs\OrderFilterDTO;
//use App\DTOs\UpdateOrderStatusDTO;
use App\DTOs\OrderUpdateStatusDTO;
use App\Exceptions\OrderNotFoundException;
use App\Http\Requests\OrderUpdateStatusRequest;
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


    /**
     * @throws OrderNotFoundException
     */
    public function updateStatusOrder(int $id, OrderUpdateStatusDTO $data): Order
    {
        return new OrderStatefulService($id)->updateStatus($data);
    }


    /**
     * @throws OrderNotFoundException
     */
    public function getOrderWithDetails(int $orderId): Order
    {
        return new OrderStatefulService($orderId)->getDetails();
    }

    public function getPaginationOrder(OrderFilterDTO $filter): \Illuminate\Pagination\CursorPaginator
    {
        return $this->orderPaginationService->cursorPaginate($filter);
    }
}
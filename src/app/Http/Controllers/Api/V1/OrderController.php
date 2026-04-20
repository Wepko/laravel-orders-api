<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\OrderNotFoundException;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\OrderCreateRequest;
use App\Http\Requests\OrderIndexRequest;
use App\Http\Requests\OrderUpdateStatusRequest;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use phpDocumentor\Reflection\Exception;

class OrderController extends BaseApiController
{

    public function __construct(
        protected OrderService $orderService
    )
    {
    }

    public function index(OrderIndexRequest $request): JsonResponse
    {
        $paginator = $this->orderService->getPaginationOrder(
            filter: $request->toDTO()
        );

        $resource = OrderResource::collection($paginator);

        return $this->jsonPaginate(
            data: $resource,
            paginator: $paginator,
            message: __('Orders retrieved successfully')
        );
    }

    /**
     * @throws Exception
     */
    public function store(OrderCreateRequest $request): JsonResponse
    {
        $order = $this->orderService->createOrder(
            data: $request->toDto()
        );

        return $this->jsonCreated(
            data: new OrderResource($order),
            message: __("Create order success")
        );
    }

    public function show(int $id): JsonResponse
    {
        $order = $this->orderService->getOrderWithDetails($id);

        return response()->json(
            new OrderResource($order)
        );
    }

    /**
     * @throws OrderNotFoundException
     */
    public function updateStatus(OrderUpdateStatusRequest $request, int $id): JsonResponse
    {
        $order = $this->orderService->updateStatusOrder($id, $request->toDto());

        return $this->jsonUpdated(
            data: new OrderResource($order)
        );
    }
}

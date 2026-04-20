<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\OrderIndexRequest;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;

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

    public function store(): JsonResponse
    {
        return $this->jsonCreated([], 'Order created');
    }

    public function show(int $id): JsonResponse
    {
        $order = $this->orderService->getOrderWithDetails($id);

        return response()->json(
            new OrderResource($order)
        );
    }

    public function updateStatus(int $id): JsonResponse
    {
        return $this->json(['id' => $id], 'Status updated');
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use Illuminate\Http\JsonResponse;

class OrderController extends BaseApiController
{
    public function index(): JsonResponse
    {
        return $this->json([], 'Orders list');
    }

    public function store(): JsonResponse
    {
        return $this->jsonCreated([], 'Order created');
    }

    public function show(int $id): JsonResponse
    {
        return $this->json(['id' => $id], 'Order details');
    }

    public function updateStatus(int $id): JsonResponse
    {
        return $this->json(['id' => $id], 'Status updated');
    }
}

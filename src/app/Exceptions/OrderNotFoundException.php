<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderNotFoundException extends Exception
{
    protected int $orderId;
    protected string $customMessage;

    public function __construct(int $orderId, string $message = null)
    {
        $this->orderId = $orderId;
        $this->customMessage = $message ?? "Order with ID {$orderId} not found";

        parent::__construct($this->customMessage, 404);
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }

    /**
     * Render the exception as an HTTP response.
     */
    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'success' => false,
            'data' => null,
            'meta' => null,
            'error' => [
                'code' => 'ORDER_NOT_FOUND',
                'message' => $this->getMessage(),
                'details' => [
                    'order_id' => $this->orderId,
                ],
            ],
            'message' => $this->getMessage(),
        ], 404);
    }
}